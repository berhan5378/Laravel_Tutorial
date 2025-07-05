<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shipment;
use GuzzleHttp\Client;
use App\Models\Product_Variants;
use App\Models\Cart; 
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;

class GoogleController extends Controller
{
    /**
     * Redirect to Google for authentication.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google authentication callback.
     */
    public function handleGoogleCallback()
    {
        try {
            // Set custom Guzzle client to handle SSL verification issues (temporary fix)
            $client = new Client(['verify' => false]); 
            Socialite::driver('google')->setHttpClient($client);

            // Retrieve user from Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            if (!$googleUser->getEmail()) {
                return redirect()->route('login')->withErrors(['error' => 'Google account does not have an email.']);
            }

            // Check if the user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) { 
                $user = User::create([
                    'name'     => $googleUser->getName(),
                    'email'    => $googleUser->getEmail(),
                    'password' => bcrypt(uniqid()),
                ]);
            }

            // Log the user in
            Auth::login($user);

            $this->store_cart(session('guest_cart', [])); // Store guest cart items if any
            unset($_SESSION['guest_cart']); // Clear guest cart session
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
            return redirect()->route('view.products.index');
        } catch (\GuzzleHttp\Exception\ClientException $e) { 
            return redirect()->route('login')->withErrors(['error' => 'Google authentication failed.']);
        } catch (\Throwable $e) { 
            // Log the error for debugging
            Log::error('Google authentication error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Something went wrong. Please try again later.']);
        }
    }
    /**
     * Store cart items for the authenticated user.
     *
     * @param array $cartItems
     * @return void
     */
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
