<nav class="navbar">
    <ul>
        <li>
            <button class="Categories popup-open">
                <i class="ri-menu-line"></i>All Categories
                <div class="Categories-dropdown">
                    <div> 
                        <ul>
                            <li><a href="{{ route('view.products.filter', ['category' => 'mens_Clothing']) }}">Men's Clothing</a></li>
                            <li><a href="{{ route('view.products.filter', ['category' => 'womens_Clothing']) }}">Women's Clothing</a></li>
                            <li><a href="{{ route('view.products.filter', ['category' => 'phones']) }}">Phones</a></li>
                            <li><a href="{{ route('view.products.filter', ['category' => 'phones_Accessories']) }}">Phones Accessories</a></li>
                            <li><a href="{{ route('view.products.filter', ['category' => 'computers_Tablets']) }}">Computers & Tablets</a></li>
                            <li><a href="{{ route('view.products.filter', ['category' => 'computers_Tablets_Accessories']) }}">Computers & Tablets Accessories</a></li>
                            <li><a href="{{ route('view.products.filter', ['category' => 'shoes']) }}">Shoes</a></li>
                            <li><a href="{{ route('view.products.filter', ['category' => 'watches']) }}">Watches</a></li>
                            <!-- not navigable -->
                            <li><a href="#">Luxury Home Decor</a></li>
                            <li><a href="#">Gourmet Food & Wine</a></li>
                            <li><a href="#">Luxury Travel</a></li>
                            <li><a href="#">Luxury Cars</a></li>
                            <li><a href="#">Luxury Furniture</a></li>
                            <li><a href="#">Luxury Accessories</a></li>
                        </ul>
                    </div>
                </div>
            </button>
        </li>
        <li class="subcategories"> 
            <ul>
                <li><a href="{{ route('view.products.filter', ['category' => 'phones']) }}">Phones</a></li>
                <li><a href="{{ route('view.products.filter', ['category' => 'mens_Clothing']) }}">Men's Clothing</a></li>
                <li><a href="{{ route('view.products.filter', ['category' => 'womens_Clothing']) }}">Women's Clothing</a></li>
                <li><a href="{{ route('view.products.filter', ['category' => 'computers_Tablets']) }}">Computers & Tablets</a></li>
                <li><a href="{{ route('view.products.filter', ['category' => 'shoes']) }}">Shoes</a></li>
                <li><a href="{{ route('view.products.filter', ['category' => 'watches']) }}">Watches</a></li>
                <li><a href="#">Luxury Cars</a></li>
            </ul>
        </li>
    </ul> 
</nav>