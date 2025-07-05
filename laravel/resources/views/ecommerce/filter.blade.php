@props(['user' => [],'searchHistory' => [],'totalCartItems' => 0])
<x-main_layout :user="$user" :searchHistory="$searchHistory" :totalCartItems="$totalCartItems">
<!-- search results section -->
<section class="search-results-container">
  <!-- Sidebar for filtering products -->
   <aside class="sidebar">
       <h2>Filter Products</h2> 
       <!-- Filter options -->
        @if(isset($_GET['category']) && $_GET['category']=="phones")
           <div class="filter-group">
               <h3>Phones</h3>
               <label><input type="checkbox" name="type[]"  value="smartphone">Smartphones</label>
               <label><input type="checkbox" name="type[]"  value="feature_phone">Feature Phones</label>
               <label><input type="checkbox" name="type[]"  value="gaming_phone">Gaming Phones</label>
               <label><input type="checkbox" name="type[]"  value="foldable_phone">Foldable Phones</label>
           </div>
            <div class="filter-group">
                <h3>Brand</h3>
                <label><input type="checkbox" name="brand[]" value="apple">Apple</label>
                <label><input type="checkbox" name="brand[]" value="samsung">Samsung</label>
                <label><input type="checkbox" name="brand[]" value="xiaomi">Xiaomi</label>
                <label><input type="checkbox" name="brand[]" value="oneplus">OnePlus</label>
            </div>
        @endif
        @if(isset($_GET['category']) && $_GET['category']=="phone_accessories")   
            <div class="filter-group">
               <h3>Phones Accessories</h3>
               <label><input type="checkbox" name="type[]" value="power banks">Power Banks</label>
               <label><input type="checkbox" name="type[]" value="charger">Chargers</label>
               <label><input type="checkbox" name="type[]" value="headphone">Headphones</label>
               <label><input type="checkbox" name="type[]" value="earphone">Earphones</label>
               <label><input type="checkbox" name="type[]" value="earpad">Earpads</label>
               <label><input type="checkbox" name="type[]" value="cable">Cables</label>
               <label><input type="checkbox" name="type[]" value="battery_pack">Battery Pack</label>
           </div>
           <div class="filter-group">
                <h3>Brand</h3>
                <label><input type="checkbox" name="brand[]" value="apple">Apple</label>
                <label><input type="checkbox" name="brand[]" value="samsung">Samsung</label>
                <label><input type="checkbox" name="brand[]" value="xiaomi">Xiaomi</label>
                <label><input type="checkbox" name="brand[]" value="oneplus">OnePlus</label>
           </div>
        @endif   
        @if(isset($_GET['category']) && $_GET['category']=="mens_clothing")
           <div class="filter-group">
               <h3>Men's Clothing</h3>
               <label><input type="checkbox" name="type[]" value="tshirt">T-Shirts</label>
               <label><input type="checkbox" name="type[]" value="jeans">Jeans</label>
               <label><input type="checkbox" name="type[]" value="jacket">Jackets</label>
               <label><input type="checkbox" name="type[]" value="pants">Pants</label> 
           </div>
           <div class="filter-group">
               <h3>Brand</h3>
               <label><input type="checkbox" name="brand[]" value="nike">Nike</label>
               <label><input type="checkbox" name="brand[]" value="jeep">jeep</label>
               <label><input type="checkbox" name="brand[]" value="puma">Puma</label>
               <label><input type="checkbox" name="brand[]" value="adidas">Adidas</label>
           </div>
        @endif   
        @if(isset($_GET['category']) && $_GET['category']=="womens_clothing")
           <div class="filter-group">
               <h3>Women's Clothing</h3>
               <label><input type="checkbox" name="type[]" value="dress">Dresses</label>
               <label><input type="checkbox" name="type[]" value="skirt">Skirts</label>
               <label><input type="checkbox" name="type[]" value="wedding_dress">Wedding Dresses</label>
               <label><input type="checkbox" name="type[]" value="pants">Pants</label>
               <label><input type="checkbox" name="type[]" value="blouse">Blouses</label>
           </div>
           <div class="filter-group">
               <h3>Brand</h3>
               <label><input type="checkbox" name="brand[]" value="nike">Nike</label>
               <label><input type="checkbox" name="brand[]" value="jeep">jeep</label>
               <label><input type="checkbox" name="brand[]" value="puma">Puma</label>
           </div>
         @endif
         @if(isset($_GET['category']) && $_GET['category']=="computers_tablets")  
            <div class="filter-group">
               <h3>Computers & Tablets</h3>
               <label><input type="checkbox" name="type[]" value="laptop">Laptops</label>
               <label><input type="checkbox" name="type[]" value="tablet">Tablets</label>
               <label><input type="checkbox" name="type[]" value="desktop">Desktops</label>
               <label><input type="checkbox" name="type[]" value="gamingpc">Gaming PCs</label>
           </div>
           <div class="filter-group">
              <h3>Brand</h3>
              <label><input type="checkbox" name="brand[]" value="hp">HP</label>
              <label><input type="checkbox" name="brand[]" value="dell">Dell</label>
              <label><input type="checkbox" name="brand[]" value="apple">Apple</label>
           </div>
        @endif
        @if(isset($_GET['category']) && $_GET['category']=="computers_tablets_accessories")   
            <div class="filter-group">
               <h3>Computers & Tablets accessories</h3>
               <label><input type="checkbox" name="type[]" value="laptop_charger">Laptop Chargers</label>
               <label><input type="checkbox" name="type[]" value="tablet_charger">Tablet Chargers</label>
               <label><input type="checkbox" name="type[]" value="desktop_stand">Desktop Stands</label>
               <label><input type="checkbox" name="type[]" value="mouse">Mouses</label>
           </div>
           <div class="filter-group">
               <h3>Brand</h3>
               <label><input type="checkbox" name="brand[]" value="hp">HP</label>
               <label><input type="checkbox" name="brand[]" value="dell">Dell</label>
               <label><input type="checkbox" name="brand[]" value="apple">Apple</label>
           </div>
          @endif
          @if(isset($_GET['category']) && $_GET['category']=="shoes")
            <div class="filter-group">
               <h3>shoes</h3>
               <label><input type="checkbox" name="type[]" value="sneakers">Sneakers</label>
               <label><input type="checkbox" name="type[]" value="boots">Boots</label>
               <label><input type="checkbox" name="type[]" value="sandals">Sandals</label>
               <label><input type="checkbox" name="type[]" value="formal">Formal Shoes</label>
           </div>
           <div class="filter-group">
               <h3>Brand</h3>
               <label><input type="checkbox" name="brand[]" value="nike">Nike</label>
               <label><input type="checkbox" name="brand[]" value="jeep">jeep</label>
               <label><input type="checkbox" name="brand[]" value="puma">Puma</label>
           </div>
          @endif 
           <div class="filter-group">
               <h3>Delivery options</h3>
               <label><input type="checkbox" name="free_shipping">Free shipping</label>
           </div>
   
           <div class="filter-group">
               <h3>Price Range</h3>
               <div class="price-inputs">
                   <input type="number" name="min_price" min="1" placeholder="Min">
                   <input type="number" name="max_price" min="1" placeholder="Max">
               </div>
           </div>
   
           <div class="filter-group">
               <h3>Size</h3> 
               <label><input type="checkbox" name="size[]" value="small">Small</label>
               <label><input type="checkbox" name="size[]" value="medium">Medium</label>
               <label><input type="checkbox" name="size[]" value="large">Large</label>
           </div>
   
           <div class="filter-group">
               <h3>Color</h3>
               <div class="color-options">
                   <div class="color-circle" style="background: black;" data-color="black"></div>
                   <div class="color-circle" style="background: white; border: 2px solid #aaa;" data-color="white"></div>
                   <div class="color-circle" style="background: red;" data-color="red"></div>
                   <div class="color-circle" style="background: blue;" data-color="blue"></div>
                   <div class="color-circle" style="background: yellow;" data-color="yellow"></div>
               </div>
           </div>
   <!-- Reset and Apply buttons -->
        <button type="button" class="btn-reset">Reset</button>
   </aside>
    <!-- Main content area -->
   <div class="sorting-options">
        <label for="sort">Sort by:</label>
        <select class="sort" id="sort" name="sort">
          <option value="">Relevance</option>
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
          <option value="newest">Newest Arrivals</option>
          <option value="oldest">Oldest Arrivals</option>
        </select>
        <button class="filter-btn"><i class="ri-filter-line"></i>filter</button>
   </div>
   <!-- Featured products section -->
   <div class="featured-products">
     <div class="products-container"> 
       <!-- dynamically generated product cards -->
     </div> 
     <button class="seeMoreBtn loading" id="load-more">
       <span></span>
       <span></span>
       <span></span>
       <span></span>
       <span></span>
       <span>see more</span>
    </button>
   </div> 
</section> 
</x-main_layout>