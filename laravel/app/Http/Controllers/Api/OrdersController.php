<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Order_Items;
use App\Models\Product_Variants;
use App\Models\Cart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    public function index()
    {

        $order = Orders::with('items.variant.product')->get();
        return response()->json([
            'order' => $order
        ]);

    }

    public function show($id)
{
    $order = Orders::with('items.variant.product')->findOrFail($id);

    return response()->json($order);
}

    public function store(Request $request)
    {
        try{
         // Validate the request data
        $validatedData = $request->validate([
              //array of variant IDs
            'product_variant_id' => 'required|array',
            'product_variant_id.*' => 'exists:product_variants,id', 
            'shipping_address_id' => 'required|exists:shipments,id'
        ]); 
 
        $user_id=Auth::user()->uuid;
        // order summary by checking the cart for variant_ids and stock
        $cartItems = Cart::where('user_id', $user_id)
            ->whereIn('product_variant_id', $validatedData['product_variant_id'])
            ->with('variant.product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No items found in the cart for the provided variant IDs.'
            ], 404);
        }

        // Calculate total price and quantity
        $totalPrice = 0;
        $quantity = 0;

        foreach ($cartItems as $item) {
            // Check if the variant is in stock
            if ($item->variant->stock < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock for variant: ' . $item->variant->name
                ], 400);
            }
            $totalPrice += $item->variant->price * $item->quantity; 
        }

        // Create the order
        $order = Orders::create([
            'user_id' => $user_id, 
            'total_amount' => $totalPrice,
            'shipping_address_id' => $validatedData['shipping_address_id'],
            'status' => 'pending'
        ]);

        // Attach items to the order
        foreach ($cartItems as $item) {
            $order->items()->create([
                'product_variant_id' => $item->product_variant_id,
                'quantity' => $item->quantity,
                'price' => $item->variant->price
            ]);
        }

        // Clear the cart after order creation
        Cart::where('user_id', $user_id)
            ->whereIn('product_variant_id', $validatedData['product_variant_id'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'order' => $order
        ]);

    }catch(\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => $e->validator->errors()->first(),
        ], 422);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'error' => $e->getMessage()
        ], 500);
    }
    }

}
