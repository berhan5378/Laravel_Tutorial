<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Models\Wishlist; 
use App\Models\Cart; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

/**
 * WishlistController handles wishlist operations for the API.
 * It allows users to manage their wishlist items, including adding and removing products.
 */
class WishlistController extends Controller
{
    public function getUserWishlist()
    { 
        //
    }
    /**
     * Add a product variant to the user's wishlist.
     * Validates the request, checks for existing items, and creates a new wishlist item if it doesn't exist.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addToWishlist(Request $request): JsonResponse
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id'
        ]);
      
        $userId=Auth::user()->uuid; // Get the authenticated user's UUID
        // Check if cart item already exists
        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();
        $cartItem = Cart::where('user_id', $userId)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();
        if($wishlistItem){
            return response()->json([
                'success' => false,
                'message' => 'exist',
                'wishlist' => null
            ]);
        }
        
        if($cartItem){
            return response()->json([
                'success' => false,
                'message' => 'alradey on cart',
                'wishlist' => null
            ]);
        }
        
        $wishlistItem=Wishlist::create([
            'user_id' => $userId,
            'product_variant_id' => $request->product_variant_id
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'added to wishlist',
            'wishlist' => $wishlistItem
        ]);

    }
    /**
     * Remove a product variant from the user's wishlist.
     * Validates the request and deletes the specified wishlist item if it belongs to the authenticated user.
     *
     * @param Wishlist $wishlistItem
     * @return JsonResponse
     */
    public function destroy(Wishlist $wishlistItem): JsonResponse
    {
        // Verify the wishlist item belongs to current user
        if ($wishlistItem->user_id !== Auth::user()->uuid) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action. You cannot delete this wishlist item.'
            ], 403);
        }
    
        $wishlistItem->delete(); 
        return response()->json([
            'success' => true,
            'message' => 'Wishlist item deleted successfully'
        ]);
    }
}
