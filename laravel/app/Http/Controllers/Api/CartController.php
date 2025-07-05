<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\product_variants;
use App\Models\Cart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

/**
 * CartController handles operations related to the user's cart.
 */

class CartController extends Controller
{
    /**
     * Display a listing of the user's cart items.
     * @param Request $request
     * @return JsonResponse
     * 
     * This method retrieves cart items for the authenticated user, with optional pagination.
     * It validates the 'offset' parameter and returns a JSON response with the cart items.
     * If no items are found, it returns a message indicating that the cart is empty.
     * 
     * @throws \Throwable
     */
    public function index( Request $request):JsonResponse
    { 
        try {
                   // Validate 'offset' input (optional, integer, min value 0)
                   $validator = Validator::make($request->only('offset'), [
                       'offset' => 'sometimes|integer|min:0',
                   ]);
           
                   if ($validator->fails()) {
                       return response()->json([
                           'success' => false,
                           'message' => 'Invalid input',
                           'errors' => $validator->errors(),
                       ], 422);
                   }
                     // Set default limit and offset
                   $limit = 4;
                   $offset = (int) $request->get('offset', 0);
    
                   // Fetch products with the 'randomVariant' relationship
                   $cartItems = Cart::with([
                           'variant' => function ($q) {
                               $q->select('id', 'img', 'stock', 'product_id','size', 'color')
                                 ->with(['product:id,name,description,discount_price,original_price']);
                           }
                       ])
                       ->where('user_id', auth::user()->uuid)
                       ->skip($offset)
                       ->take($limit)
                       ->get(); 
                 if($cartItems->isEmpty()){
                   return response()->json([
                       'success' => false,
                       'message' => 'No items in cart',
                   ], 404);
                 }
    
               return response()->json([
                   'success' => true,
                   'message' => 'Cart items retrieved successfully',
                   'carts' => $cartItems
               ]);
           } catch (\Throwable $e) {
               return response()->json([
                   'success' => false,
                   'message' => 'An unexpected error occurred.',
                   'error' => $e->getMessage()
               ], 500);
           }
    }

    /**
     * Update or create a cart item.
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * This method updates the quantity of an existing cart item or creates a new one if it doesn't exist.
     * It checks for sufficient stock before updating or creating the cart item.
     * If the item already exists, it updates the quantity and checks if it exceeds available stock.
     * If the item does not exist, it creates a new cart item with the specified quantity.
     * 
     * @throws \Throwable
     */

    public function updateOrCreateCart(Request $request): JsonResponse
    {
        $request->validate([ 
            'quantity' => 'required|integer',
            'product_variant_id' => 'required|exists:product_variants,id'
        ]);
        try {
           $userId = auth::user()->uuid;
           $quantity = $request->input('quantity');
    
           // Get stock from variant
           if ($request->filled('product_variant_id')) { 
                $variant = product_variants::select('id', 'stock')->find($request->product_variant_id);
               // Check if variant exists and has sufficient stock
               // If variant is not found or stock is insufficient, return an error response
               if (!$variant || $variant->stock < $quantity) {
                   return response()->json([
                       'success' => false,
                       'message' => 'Insufficient stock for this variant.'
                   ], 422);
               }
           }  
    
           // Check if cart item already exists
           $cartItem = Cart::where('user_id', $userId)
               ->select('id', 'quantity')
               ->where('product_variant_id', $request->product_variant_id)->first();
    
           if ($cartItem) {
               $newQuantity = $cartItem->quantity + $quantity;
    
               // Check stock again for the new total quantity
               $availableStock = $variant->stock;
        
               if ($newQuantity > $availableStock) {
                   return response()->json([
                       'success' => false,
                       'message' => 'Limit reached'
                   ], 422);
               }
    
               $cartItem->quantity = $newQuantity;
               $cartItem->save();
               return response()->json([
                   'success' => true,
                   'cart_count' => Cart::where('user_id', $userId)->count(),
                   'message' => 'Quantity updated',
                   'isMaxLimit'=>($availableStock -  $newQuantity) > 0 ?false:true,
                   'quantity'=> $cartItem->quantity
               ]);
    
           } else {
               Cart::create([
                   'user_id' => $userId, 
                   'product_variant_id' => $request->product_variant_id,
                   'quantity' => $quantity
               ]); 
               return response()->json([
                   'success' => true,
                   'cart_count' => Cart::where('user_id', $userId)->count(),
                   'message' => 'added to cart!',
                   'isMaxLimit'=>($variant->stock -  $quantity) > 0 ?false:true,
                   'quantity'=> $quantity
               ]);
           }
        }catch (\Throwable $e) {
               return response()->json([
                   'success' => false,
                   'message' => $e->getMessage(),
                   'error' => $e->getMessage()
               ], 500);
        }
    }
    /**
     * Get a summary of the cart items.
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * This method retrieves a summary of the cart items for the authenticated user.
     * It calculates the subtotal, total shipping price, and checks for stock availability.
     * It returns a JSON response with the cart summary, including any items that exceed available stock.
     * 
     * @throws \Throwable
     */

    public function cartSummary(Request $request): JsonResponse
    {
        $product_variant_ids = $request->input('product_variant_ids');
    
        if (!$product_variant_ids || !is_array($product_variant_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data format.',
            ], 422);
        }
        try{
            // Eager load variant with only selected columns, and product with shipping_price
            $cartItems = Cart::with([
                'variant' => function ($q) {
                    $q->select('id', 'img', 'price', 'stock', 'product_id')
                      ->with(['product:id,shipping_price']);
                }
            ])
            ->where('user_id', Auth::user()->uuid)
            ->whereIn('product_variant_id', $product_variant_ids)
            ->select('id', 'product_variant_id', 'quantity')
            ->get();
        
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'error on cart summary',
                ],404);
            }
        
            // Initialize variables for subtotal and total shipping price
            $subtotal=$total_shipping_price= 0;
            $items = [];
            $exceed_items =[];
        
            foreach ($cartItems as $item) {
                $variant = $item->variant;
                $product = $variant?->product;
        
                if ($variant && $product) {
                    if($variant->stock < $item->quantity){
                        $exceed_items[]=[
                            'variant_id'=>$item->product_variant_id,
                            'variant_stock' => $variant->stock
                        ];
                        continue;
                    }
                    $itemTotal = $variant->price * $item->quantity;
                    $subtotal += $itemTotal;
                    $total_shipping_price += $product->shipping_price;
        
                    $items[] = [
                        'cart_id' => $item->id,
                        'variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'img' => $variant->img,
                        'price' => $variant->price,
                        'stock' => $variant->stock
                    ];
                }
            }
        
            session(['items' => $items ]);
            return response()->json([
                'success' => true,
                'message' => 'Cart Summary',
                'total_shipping_price'=>"$".$total_shipping_price,
                'subtotal' => "$".$subtotal,
                'tax'=>"$0",
                'order_total' => "$".($subtotal + $total_shipping_price + 0),
                'exceed_items'=>$exceed_items
            ]);
        }catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' =>' An unexpected error occurred.',
                    'error' => $e->getMessage()
                ], 500);
        }
    }


    public function destroyMultiple(Request $request)
    {
    
        $ids = $request->input('cart_ids'); // array of cart item IDs

        if (!$ids || !is_array($ids)) {
                return response()->json([
                'success' => false,
                'message' => 'No cart items selected'
            ], 422);
        }
        try {
            Cart::whereIn('id', $ids)
            ->where('user_id', auth::user()->uuid)
            ->delete();
            // Optionally, you can return the count of remaining cart items
            $remainingCount = Cart::where('user_id', auth::user()->uuid)->count();
            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully.',
                'cart_count' => $remainingCount?? 0
            ]);
        }catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    
    }


}
