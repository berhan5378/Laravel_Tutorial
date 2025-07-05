@props(['user' => [],'totalCartItems' => 0])
<x-main_layout :user="$user" :totalCartItems="$totalCartItems">
    <div class="overflow"></div>
    <section class="Checkout-section">
        <div class="Checkout-details">
            <div class="shipping-address">
                <h3>Shipping address</h3>
                <div class="selected address-details">
                    <div>
                        @if(isset($shipments))
                       <input type="text" name="" class="shipping-address-id" value="{{$shipments->id}}" hidden>
                       <p class="name paragraph">{{$shipments->contact_name}} &nbsp; <span class="phone-number">{{$shipments->contact_phone}}</span></p>
                       <p class="address">{{$shipments->address}}</p>
                       <p class="location">{{$shipments->sub_city.", ".$shipments->city.", ".$shipments->country.", ".$shipments->zip_code}}</p>
                        @else
                        <p>No shipping address available.</p>
                        @endif
                    </div>
                    <button id="contact-edit">{{ $shipments ? 'Change' : 'Add' }}</button>
                </div>
            </div>
            <div class="payment-Methods">
                <h3>Payment Methods</h3>
                <div class="payment-card">
                    <div> 
                        <p><i class="ri-bank-card-line"></i> &nbsp;  5341 **** **** 1234</p>
                    </div>
                    <button>Change</button>
                </div>
            </div>
            <div class="Shipping-method">
                <h3>Shipping method</h3>
                <div class="shipping-method-details">
                    <p class="paragraph">Shipping: US $1.99</p>
                    <p>Delivery: May 17 - 31</p>
                </div>
                <h3 class="heading-for-details">Items' details</h3>
                <div class="cart-items items-details">
                @if(session('items'))
                    @foreach(session('items') as $item)
                        <div class="item cart-item" data-variant-id="{{$item['variant_id']}}" data-variant-stock="{{$item['stock']}}">
                            <div class="item-image" style="background-image: url('{{ asset('assets/img/'.$item['img']) }}');"></div>
                            <p>
                                US ${{$item['price']}} <br>
                                <span>stock : {{$item['stock']}}</span>
                            </p>
                            <div class="quantity-selector">
                                <button class="qty-btn minus">-</button>
                                <input type="number" data-cart-quantity="{{$item['quantity']}}" value="{{$item['quantity']}}" min="1" class="qty-input" {{($item['stock'] - $item['quantity']) > 0 ?'':'disabled'}}>
                                <button class="qty-btn plus">+</button>
                            </div>
                        </div>
                    @endforeach
                @else
                 <script>
                    window.location.href = "{{ route('view.products.cart') }}";
                 </script>
                @endif
                </div> 
            </div>
        </div>
            <!-- Cart Summary -->
 <x-ecommerce.order-summary />
    </section> 
</x-main_layout>