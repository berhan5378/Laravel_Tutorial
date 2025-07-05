@props(['user' => [],'searchHistory' => [],'totalCartItems' => 0])
<x-main_layout :user="$user" :searchHistory="$searchHistory" :totalCartItems="$totalCartItems">
        <!-- hero section -->    
        <section class="hero">
            <div class="hero-slideshow">
                <div class="slide active" style="background-image: url('/assets/img/hero-img.avif')"></div>
                <div class="slide" style="background-image: url('/assets/img/hero-img2.avif')"></div>
            </div>
            
            <div class="hero-content">
                <h1 class="hero-title">Discover Luxury Like Never Before</h1>
                <p class="hero-subtitle">Curated Elegance from Around the Globe</p>
                <a href="#" class="hero-cta">
                    Explore Collection
                    <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="hero-overlay"></div>
        </section>
        <!-- main categories section -->
        <x-ecommerce.category />
        <!-- featured products section -->
        <section class="featured-products">
            <div class="section-header">
                <h2>Curated Selections</h2>
                <p>Exquisite pieces chosen by our style directors</p>
            </div>
            <div class="products-container"> 
                  <!-- dynamically generated product cards -->
            </div> 
            <button class="seeMoreBtn loading" id="load-more">
                <!-- Loading animation -->
               <span></span>
               <span></span>
               <span></span>
               <span></span>
               <span></span>
                <!-- Text -->
               <span>see more</span>
            </button>
        </section> 
</x-main_layout>