@props(['user' => [],'searchHistory' => [],'totalCartItems'=>0])
<x-main_layout :user="$user" :totalCartItems="$totalCartItems" :searchHistory="$searchHistory">
<section class="cart-section">
            <!-- Cart Items Table --> 
             <div class="cart-items">
                <div class="cart-title">
                    <div class="title-container">
                        <h2>Cart ({{$totalCartItems}})</h2>
                        <div class="cart-actions">
                            <label for="">Select items</label>
                            <input type="checkbox" name="" id="" class="Select_items">
                            <a href="#" class="btn btn-primary delete-selected">Delete</a>
                        </div>
                    </div>
                </div>
                <div class="cart-header">
                    <div class="header-item product-col">Product</div>
                    <div class="header-item price-col">Price</div>
                    <div class="header-item quantity-col">Quantity</div> 
                    <div class="header-item action-col">Action</div>
                </div>
                <button class="seeMoreBtn loading" id="load_more">
                    <!-- Loading animation -->
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <!-- Text -->
                    <span>see more</span>
               </button>
            </div>
            <!-- Cart Summary -->
              <x-ecommerce.order-summary />
    </section>
</x-main_layout>