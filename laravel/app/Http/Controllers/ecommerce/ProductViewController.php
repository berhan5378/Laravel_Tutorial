<?php

namespace App\Http\Controllers\ecommerce;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\search_histories;
use App\Models\Cart;
use App\Models\Orders;
use App\Models\Wishlist;
use App\Models\Shipment;


class ProductViewController extends Controller
{
    /**
     * Display the product view page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            if(!auth::check()){
                return view('ecommerce.index');
            }
            $user = auth::user();
            // Check if the user has a search history
            $searchHistory = search_histories::where('user_id', $user->uuid)
                ->orderByDesc('updated_at')
                ->select('term', 'category_name','brand','type')
                ->limit(5)
                ->get(); 
            $totalCartItems = Cart::where('user_id', $user->uuid)->count();
            return view('ecommerce.index', compact('user', 'searchHistory', 'totalCartItems'));
        } catch (\Throwable $e) {
            // Handle any exceptions that may occur
            return view('components.errors.error_alert')->with('error', 'An unexpected error occurred. Please try again later.');
        }

    }

    /**
     * Display the filter page.
     *
     * @return \Illuminate\View\View
     */
    public function filter()
    {
        try {
            if(!auth::check()){
                return view('ecommerce.index');
            }
            $user = auth::user();
            // Check if the user has a search history
            $searchHistory = search_histories::where('user_id', $user->uuid)
                ->orderByDesc('updated_at')
                ->select('term', 'category_name','brand','type')
                ->limit(5)
                ->get();
            $totalCartItems = Cart::where('user_id', $user->uuid)->count();
            return view('ecommerce.filter', compact('user', 'searchHistory', 'totalCartItems'));
        } catch (\Throwable $e) {
            // Handle any exceptions that may occur
            return view('components.errors.error_alert')->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Display the cart page.
     *
     * @return \Illuminate\View\View
     */
    public function cart()
    {
        try {
            if(!auth::check()){
                return view('ecommerce.cart');
            }
            $user = auth::user();
            // Check if the user has a search history
            $searchHistory = search_histories::where('user_id', $user->uuid)
                ->orderByDesc('updated_at')
                ->select('term', 'category_name','brand','type')
                ->limit(5)
                ->get();
            $totalCartItems = Cart::where('user_id', $user->uuid)->count();
            return view('ecommerce.cart', compact('user', 'searchHistory', 'totalCartItems'));
        } catch (\Throwable $e) {
            // Handle any exceptions that may occur
            return view('components.errors.error_alert')->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }
    public function order()
    {
        try {
            if(!auth::check()){ 
                \session(['redirect' => '/view/carts']);
                return \redirect('auth/login');
            } 
            //remove items session
            \session()->forget('items');
            
            $user = auth::user();
            $userId= $user->uuid;
            $orders = Orders::with([
                           'items.variant' => function ($q) {
                               $q->select('id', 'img','price', 'product_id','size', 'color')
                                 ->with(['product:id,name,description']);
                           }
                       ])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
            $variant_id = Cart::where('user_id', $userId)
                   ->select('product_variant_id')->get();
            Wishlist::whereIn('product_variant_id', $variant_id)->delete();
            $wishlistItems = Wishlist::with([
                           'variant' => function ($q) {
                               $q->select('id', 'img','price', 'product_id')
                                 ->with(['product:id,name,description']);
                           }
                       ])
            ->select('id', 'product_variant_id')
            ->where('user_id', $userId)
            ->get();
     
            return view('ecommerce.order_list', compact('user','orders','wishlistItems')); 
        } catch (\Throwable $e) {
            // Handle any exceptions that may occur
            return view('components.errors.error_alert')->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Display the wishlist page.
     *
     * @return \Illuminate\View\View
     */
    public function orderForm()
    {
        try{
        
            if(!auth::check()){ 
                \session(['redirect' => '/view/carts']);
                return \redirect('auth/login');
            }
            $user= Auth::user();
            $shipments=Shipment::where('user_id', $user->uuid)->first();
            $totalCartItems = Cart::where('user_id', $user->uuid)->count();
            return view('ecommerce.orderForm', compact('shipments','user','totalCartItems'));
        } catch (\Throwable $e) {
            // Handle any exceptions that may occur
            return view('components.errors.error_alert')->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function EditPage()
    {
        try{
            $shipments=Shipment::where('user_id', Auth::user()->uuid)->get();
     
            return view('ecommerce.EditPage',\compact('shipments'));
        } catch (\Throwable $e) {
            // Handle any exceptions that may occur
            return view('components.errors.error_alert')->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function edit(Request $request)
    {
        try {
            $shipmentId = $request->input('id');
            $shipment = Shipment::find($shipmentId);
            return view('components.ecommerce.editForm', compact('shipment'));
        } catch (\Throwable $e) {
            // Handle any exceptions that may occur
            return view('components.errors.error_alert')->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }
    public function addNewAddress()
    {
        return view('components.ecommerce.editForm');
    }
}
