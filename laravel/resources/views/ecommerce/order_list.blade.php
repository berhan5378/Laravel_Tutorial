@props(['user' => []])
<x-main_layout :user="$user">
    <!-- Orders -->
    <section>
      <h2>Your Orders</h2>
      <!-- Order -->
      @if($orders->isEmpty())
        <div class="order">You have no orders yet.</div>
      @else
    @foreach($orders as $order)
      <div class="order">
        <div class="order-header">
          <h3>Order #{{ $order->id }}</h3>
          <p>Ordered on: {{ $order->created_at->format('F j, Y') }}</p>
        </div>

        <div class="product-grid">
          <!-- Product 1 --> 
           @foreach($order->items as $item)
            <div class="product">
              <div class="pro_img">
                <img src="{{ asset('assets/img/' . $item->variant->img) }}" alt="">
              </div>
              <div class="details">
                <h3 class="product-name">{{ $item->variant->product->name }}</h3>
                <p class="product-description">{{ $item->variant->product->description }}</p>
                <p>Qty: {{ $item->quantity }}</p>
                <div class="product-attributes">
                    <span class="attribute">Color: {{ $item->variant->color }}</span>
                    <span class="attribute">Size: {{ $item->variant->size }}</span>
                </div>
                <p class="price">${{ $item->price }}</p>
              </div>
            </div>
            @endforeach
        </div>

        <div class="order-footer">
          <p>Total: ${{ $order->total_amount }}</p>
          <button>View Invoice</button>
        </div>
      </div>
    @endforeach
    @endif
    </section>

    <!-- Wishlist -->
    <section>
      <h2>Your Wishlist</h2>
      <div class="wishlist-grid">
     @if($wishlistItems->isEmpty())
        <p>Your wishlist is empty.</p>
      @else
        @foreach($wishlistItems as $Item)
        <div class="wishlist-item">
            <div class="pro_img">
                <img src="{{ asset('assets/img/' . $Item->variant->img) }}" alt="">  
            </div>  
            <h3 class="product-name">{{ $Item->variant->product->name }}</h3>
            <p class="product-description">{{ $Item->variant->product->description }}</p>
          <p class="price">${{ $Item->variant->price }}</p>
          <div class="wishlist-actions">
            <button class="add" data-variant-id="{{$Item->product_variant_id}}">Add to Cart</button>
            <button class="remove">Remove</button>
          </div>
        </div>
        @endforeach
      @endif
      </div>
    </section>
    <script> 
        document.querySelector('.wishlist-grid').addEventListener('click', function (e) {
            if (e.target && e.target.matches('button.add')){
                e.target.disabled = true;
                const variantId=e.target.dataset.variantId; 
                fetch('/api/carts/updateOrCreate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        product_variant_id: variantId,
                        quantity: 1
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) { 
                        window.createNotification('success', data.message);
                        e.target.disabled = data.isMaxLimit;      
                    } else {
                        if(data.message !== 'Limit reached') e.target.disabled = false;  
                        window.createNotification('error', data.message || 'Failed to add to cart');
                    } 
                })
                .catch(err => {
                    window.createNotification('error','Something went wrong');
                });
            }
        });
    </script>
</x-main_layout>    