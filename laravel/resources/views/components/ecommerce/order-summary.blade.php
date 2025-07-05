<div class="cart-summary">
    <div class="summary-card">
        <h3 class="summary-title">Order Summary</h3>
        <div class="summary-row">
            <span>Subtotal</span>
            <span class="subtotal">$0</span>
        </div>
        <div class="summary-row">
            <span>Shipping</span>
            <span class="shipping">$0</span>
        </div>
        <div class="summary-row">
            <span>Tax</span>
            <span class="tax">$0</span>
        </div>
        <div class="summary-row total-row">
            <span>Total</span>
            <span class="order-total">$0</span>
        </div>
        <div class="coupon-section">
            <input type="text" placeholder="Enter coupon code" class="coupon-input">
            <button class="apply-coupon">Apply</button>
        </div>
        <button class="checkout-btn" disabled>Proceed to Checkout</button>
        <div class="continue-shopping">
            <a href="{{ route('view.products.filter') }}"><i class="ri-arrow-left-line"></i> Continue Shopping</a>
        </div>
    </div>
</div> 