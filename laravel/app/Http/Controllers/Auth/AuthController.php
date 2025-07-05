<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cart;
use App\Models\Shipment;
use App\Models\Product_Variants;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;

class AuthController extends Controller
{
    /**
     * Register a new user and log them in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validate user input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    Password::min(8)
                        ->letters()
                        ->numbers()
                        ->symbols()
                        ->uncompromised()
                ],
            ]);

            // Start DB transaction
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Log in the new user
            Auth::login($user);

            // Commit transaction
            DB::commit();
            $cartItems = $request->input('cart');
            if (is_array($cartItems) && !empty($cartItems)) {
                $this->store_cart($cartItems);
            }

            $ip = request()->ip();
            $location = Location::get($ip);
            
            if($location) { 
                session([
                    'delivery_location' => $location->countryName,
                    'delivery_zip' => $location->postalCode,
                ]);
            }else{
                $shipments=Shipment::select('country', 'zip_code')
                ->where('user_id', $user->uuid)
                ->first();
                if($shipments){ 
                    session([
                        'delivery_location' => $shipments->country,
                        'delivery_zip' => $shipments->zip_code,
                    ]);
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Registration successful.',
            ], 201);

        } catch (ValidationException $e) {
            // Validation error response
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            // Rollback transaction on error
            DB::rollBack(); 

            // Log the exception
            Log::error('User registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed due to an unexpected error.',
            ], 500);
        }
    }
    
    /**
     * Handle user login request with rate limiting and error handling.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {

            // Check rate limit
            $this->checkRateLimit($request);

            // Validate credentials
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            // Attempt login
            if (Auth::attempt($credentials)) {
                RateLimiter::clear($this->throttleKey($request));
                $request->session()->regenerate(); // Prevent session fixation

                $cartItems = $request->input('cart');
                if (is_array($cartItems) && !empty($cartItems)) {
                    $this->store_cart($cartItems);
                }

               $ip = request()->ip();
               $location = Location::get($ip);
               
               if($location) { 
                   session([
                       'delivery_location' => $location->countryName,
                       'delivery_zip' => $location->postalCode,
                   ]);
               }else{
                    $shipments=Shipment::select('country', 'zip_code')
                         ->where('user_id', Auth::user()->uuid)
                         ->first();
                    if($shipments){ 
                        session([
                            'delivery_location' => $shipments->country,
                            'delivery_zip' => $shipments->zip_code,
                        ]);
                    }
               }
               if ($request->session()->has('redirect')) {
                   $redirect = $request->session()->pull('redirect'); // pull() gets and forgets in one step
               
                   return response()->json([
                       'success' => true,
                       'message' => 'Login successful.',
                       'redirect' => $redirect
                   ]);
               }

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful.'
                ]);
            }

            // Increment rate limit on failed login
            RateLimiter::hit($this->throttleKey($request));

            return response()->json([
                'success' => false,
                'message' => 'email or password is incorrect.'
            ], 401);

        } catch (ValidationException $e) {
            // Validation error (e.g. wrong input format)
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            // Log unexpected errors
            Log::error('Login error', [
                'error' => $e->getMessage(),
                'context' => [
                    'email' => $request->input('email'),
                    'ip' => $request->ip(),
                ],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during login. Please try again later.',
            ], 500);
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @param  \Illuminate\Http\Request  $request
     * @throws \ValidationException
     */
    protected function checkRateLimit(Request $request): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 2)) {
            $seconds = RateLimiter::availableIn($this->throttleKey($request));

            throw ValidationException::withMessages([
                'attempts' => ['Too many login attempts. Please try again in ', $seconds]
            ]);
        }
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        unset($_SESSION['delivery_location']);
        unset($_SESSION['delivery_zip']);
        unset($_SESSION['items']);
        session()->invalidate();
        
        return redirect()->route('login');
    }

    public function store_cart(array $cartItems){

        foreach ($cartItems as $item) {
            if (!isset($item['product_variant_id']) || !isset($item['quantity']) || $item['quantity'] < 1) {
                continue; // skip invalid item
            }
    
            $variant = Product_Variants::find($item['product_variant_id']);

            if (!$variant || $variant->stock < $item['quantity']) {
                continue;
            }

            $cartItem = Cart::where('user_id', auth::user()->uuid)
            ->where('product_variant_id', $item['product_variant_id'])->first();
            if(!$cartItem){
                Cart::create([
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'user_id' =>  auth::user()->uuid,
                ]);
            }
        }

    }
    
} 