 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxuryFinds - Premium Online Shopping</title>
    <link rel="stylesheet" href="{{ asset('assets/css/e/header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/e/footer.css') }}">
    @if (Request::is('/'))
    <link rel="stylesheet" href="{{ asset('assets/css/e/index.css') }}">
    @endif
    @if (Request::is('/') || Request::is('products/filter'))
    <link rel="stylesheet" href="{{ asset('assets/css/e/product.css') }}">
    @endif
    @if (Request::is('products/filter'))
    <link rel="stylesheet" href="{{ asset('assets/css/e/product.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/e/filter.css') }}">
    @endif
    @if (Request::is('view/carts'))
    <link rel="stylesheet" href="{{ asset('assets/css/e/cart.css') }}">
    @endif
    @if (Request::is('view/orderForm'))
    <link rel="stylesheet" href="{{ asset('assets/css/e/orderForm.css') }}">
    @endif
    @if (Request::is('view/orderlist'))
    <link rel="stylesheet" href="{{ asset('assets/css/e/orderlist.css') }}">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <!--  notification -->
    <div class="notification-container" id="notification-container"></div>
    <!--- Header Section -->
    <header>  
        <!-- Top Header -->
        <div class="header-top">
            <a href="{{route('view.products.index')}}" class="logo h-item">Luxury<span>Finds</span></a>
            <!-- search bar -->
            @if (!Request::is('view/orderForm') && !Request::is('view/orderlist'))
            <div class="search-bar h-item">
                <input class="popup-open" type="text" placeholder="Search for products...">
                <i class="ri-search-line"></i>
                <!-- dropdown container -->
                <div class="search-dropdown">
                    @if(!empty($searchHistory) && count($searchHistory) > 0)
                    <h3>Recent searches</h3>
                    <ul class="recent-searches">
                        @foreach($searchHistory as $search)
                            <li><a href="{{ route('view.products.filter', ['category' => $search->category_name, 'product' => $search->term]) }}"><i class="ri-history-line"></i>{{ $search->term }}</a></li>
                        @endforeach
                    </ul>
                    @endif
                    <h3>Recommended for you</h3>
                    <ul class="recommended-searches"> 
                        <li class="recommended"><a href="{{ route('view.products.filter', ['category' => 'mens_Clothing']) }}"><i class="ri-search-line"></i>Men's Clothing</a></li>
                        <li class="recommended"><a href="{{ route('view.products.filter', ['category' => 'womens_Clothing']) }}"><i class="ri-search-line"></i>Women's Clothing</a></li>
                        <li class="recommended"><a href="{{ route('view.products.filter', ['category' => 'phones']) }}"><i class="ri-search-line"></i>Phones</a></li>
                        <li class="recommended"><a href="{{ route('view.products.filter', ['category' => 'phones_Accessories']) }}"><i class="ri-search-line"></i>Phones Accessories</a></li>
                        <li class="recommended"><a href="{{ route('view.products.filter', ['category' => 'computers_Tablets']) }}"><i class="ri-search-line"></i>Computers & Tablets</a></li>
                    </ul>
                    <h3 class="search-results-title">Search Results</h3>
                    <ul class="search-results"></ul>
                </div>
            </div>
            <!-- location -->
            <div class="delivery-info h-item" >
                <i class="ri-map-pin-line"></i>
                <p>
                    <span>Deliver to</span>
                    <span class="location">{{ session('delivery_location', 'Unknown') }}</span>
                </p>
                <!-- dropdown container -->
                <div class="location-dropdown"  >
                   <div>
                    @if(session('delivery_location') || !empty($user))
                        <h3>Deliver to <span class="dro-location">{{session('delivery_location', 'Unknown')}}</span></h3>
                        <p>Change location</p> 
                    @else
                    <h3>Specify your location</h3>
                    <p>Delivery options and delivery speeds may vary for different locations</p>
                    <a href="{{route('login')}}">Sign in to add address</a>
                    <p>-------------------or-------------------</p>
                    @endif
                    <div class="tab-content" id="cityTab">
                        <select id="citySelect"> 
                            <option {{session('delivery_location','Unknown') == 'China' ? 'selected' : '' }}  value="China">China</option>
                            <option {{session('delivery_location','Unknown') == 'Ethiopia' ? 'selected' : '' }}  value="Ethiopia">Ethiopia</option>
                            <option {{session('delivery_location','Unknown') == 'USA' ? 'selected' : '' }}  value="USA">United States</option>
                            <option {{session('delivery_location','Unknown') == 'UK' ? 'selected' : '' }}  value="UK">United Kingdom</option> 
                        </select>
                    </div>
                    <div class="tab-content" id="zipTab">
                        <input type="text" id="zipCodeInput" placeholder="Enter ZIP/Postal Code" maxlength="11">
                        <button id="applyZipCode" class="apply-btn">Apply</button>
                        <div class="zip-error" id="zipError"></div>
                    </div> 
                   </div>
                </div>
            </div>  
            @endif
            <!-- user actions -->
            <div class="user-actions h-item">
                @if (!Request::is('view/orderlist'))
                <a href="{{route('view.products.cart')}}" class="cart"><span>{{ $totalCartItems }}</span><i class="ri-shopping-cart-line"></i></a>
                <a href="{{route('view.products.order')}}" class="wishlist"><i class="ri-heart-line"></i></a>
                @endif
                @php
                    $background =$firstLetter=$class = '';
                
                    if(!empty($user)) {
                        if($user->img){
                            $background = "background: url('{$user->img}'); background-size: cover; background-position: center;";
                        } else {
                            $firstLetter = strtoupper(mb_substr($user->name, 0, 1));
                        }
                        $class = 'user-avatar';
                    }
                @endphp

                <button  class="login popup-open {{$class}}" style="{{ $background }}">
                @if(!empty($user))
                    @if($firstLetter)
                        <span class="avatar-text">{{ $firstLetter }}</span>
                    @endif
                @else
                    <i class="ri-account-circle-line"></i>
                    <p>Sign in/up</p>
                @endif
                    <!-- dropdown container -->
                    <div class="login-dropdown">
                        <div style="color: gray;">
                            @if(!empty($user))
                            <h3>Welcome, {{ $user->name }}</h3>
                            <p>Manage your account and orders</p>
                            @else
                             <a href="{{route('login')}}" class="signin">Sign in</a>
                             <a href="{{route('register')}}">Register</a>  
                            @endif
                            <hr>
                            <ul>
                                <li><a href="#"><i class="ri-account-circle-line"></i>My Account</a></li>
                                <li><a href="{{route('view.products.order')}}"><i class="ri-survey-line"></i>My Orders</a></li>
                                <li><a href="{{route('view.products.order')}}"><i class="ri-heart-line"></i>My Wishlist</a></li>
                                <li><a href="#"><i class="ri-user-line"></i>Seller Log In</a></li>
                                <li><a href="#" class="Changecurrency"><span class="currency"><i class="ri-currency-line"></i>USD</span><p>Change currency</p></a></li>
                                <li><a href="#"><i class="ri-settings-2-line"></i>Settings</a></li>
                                <li><a href="#"><i class="ri-question-line"></i>Help</a></li>
                                <li><a href="{{route('logout')}}"><i class="ri-logout-circle-line"></i>Logout</a></li>
                                <hr>
                                <li>
                                    <a href="#">
                                        <div class="location_link">
                                            <i class="ri-map-pin-line"></i>
                                            <p>
                                                <span>Deliver to</span>

                                                <span class="location">{{session('delivery_location', 'Unknown')}}</span>
                                            </p>
                                        </div>
                                        <p>Change location</p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </button>
            </div>
        </div>
        @if (!Request::is('view/carts') && !Request::is('view/orderForm') && !Request::is('view/orderlist'))
        <!-- Bottom Header -->
         <x-ecommerce.navbar />
        @endif
        <div class="popup-close"></div>
    </header>
    <main>
        {{ $slot }}
    </main>
    @if (!Request::is('products/filter') && !Request::is('view/orderForm') && !Request::is('view/orderlist'))
    <!-- Footer Section -->
     <x-ecommerce.footer />
    @endif
</body>
<script src="{{ asset('assets/js/e/Cart.js') }}" defer></script>
<script src="{{ asset('assets/js/e/header.js') }}" defer></script>
@if (Request::is('/') || Request::is('products/filter'))
<script src="{{ asset('assets/js/e/in&fil.js') }}" defer></script>
@endif
@if (Request::is('products/filter'))
<script src="{{ asset('assets/js/e/script.js') }}" defer></script>
@endif
@if (Request::is('view/orderForm'))
<script src="{{ asset('assets/js/e/orderform.js') }}" defer></script>
@endif
</html>