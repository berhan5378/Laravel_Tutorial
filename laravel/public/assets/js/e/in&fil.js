/*
* index.js and filter.js share the same code for loading products
* functionality, handles the "Load More" button.
* hero slider functionality is also included. for index page.
* This script initializes the product loading mechanism,
*/
document.addEventListener("DOMContentLoaded", function() {
    // Initialize variables
    let offset = 0;
    const limit = 4;
    window.container = document.querySelector('.products-container');
    window.loadMoreBtn = document.getElementById('load-more');

    // Check if we are on the filter page
    const isFilterPage = window.location.pathname.includes("/filter");
    let currentEndpoint = isFilterPage ? '/api/products/filter' : '/api/products';// Default to products endpoint
    let currentParams = new URLSearchParams(window.location.search); // Get the current URL params
    window.category = currentParams.get('category') || null;// Get the category from URL params

    // Highlight the clicked link in the filter nav & sidebar
    const selector = `[href="http://127.0.0.1:8000/products/filter?category=${encodeURIComponent(window.category)}"]`;
    const clickedLink = document.querySelectorAll(selector);

    if (clickedLink) clickedLink.forEach(link => link.classList.add('active')); 

    // Function to load products for the current index or filter page
    // If reset is true, clear the container and reset offset
    function loadProducts(params = new URLSearchParams(), reset = false) {
        if (reset) {
            offset = 0;
            container.innerHTML = '';
        }

        params.set('offset', offset);

        fetch(`${currentEndpoint}?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadMoreBtn.classList.remove('loading');
                    const products = data.products; 

                    products.forEach(product => {
                        let current_price = product.discount_price || product.original_price;
                        const variantId =product.random_variant?.id || product.variants[0]?.id
                        const card = document.createElement('article');
                        card.className = 'product-card';
                        card.setAttribute('data-variant-id',variantId );
                        card.setAttribute('data-variant-stock', product.random_variant?.stock || product.variants[0]?.stock);

                        const cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
                        let cart_quantity=0; 
                        if(cart){
                            const existingItem = cart.find(item => item.product_variant_id == variantId);
                            if (existingItem) {
                                cart_quantity = existingItem.quantity;
                            } 
                        }

                        const badge = `<div class="product-badge">${product.badge||''}</div>`;
                        const image = `<div class="product-image" style="background-image: url('/assets/img/${product.random_variant?.img || product.variants[0]?.img}')">
                            <button class="wishlist-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                                    2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81
                                    14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55
                                    11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </div>`;
                        const details = `
                            <div class="product-details">
                                <h3 title="${product.name}">${product.name}</h3>
                                <div class="star-rating">
                                    <span class="stars stars-filled" style="--rating: ${product.rating || 0};">★★★★★</span>
                                    <span class="stars stars-empty">★★★★★</span>
                                    <span class="rating-count">(4)</span>
                                </div>
                                <p class="product-description" title="${product.description}">${product.description}</p>
                                <div class="price-container"> 
                                    <span class="current-price">$${current_price}</span>
                                ${product.discount_price ? `<span class="original-price">$${product.original_price}</span>` : ''}
                                </div>
                                <button class="add-to-cart" data-cart-quantity="${cart_quantity}">
                                    Add to Cart
                                    <span class="loader"></span>
                                </button>
                            </div>`;
                        card.innerHTML = badge + image + details;
                        container.appendChild(card);
                    });

                    offset += limit; 
                    if (limit > products.length) {
                        loadMoreBtn.style.display = 'none';
                    } 
                } else {
                    loadMoreBtn.classList.add('error');
                    loadMoreBtn.querySelector('span:last-child').innerText = data.message;
                }
            })
            .catch(err => { 
                container.innerHTML = '<p>Error loading products.</p>';
            });
    }

    // load more products on button click
    loadMoreBtn.addEventListener('click', () => {
        loadMoreBtn.classList.add('loading');
        loadProducts(currentParams);
    });

    // Only load once initially 
    loadProducts(currentParams, true);

    //expose for filter.js
    window.setFilterParams = (params) => {
        currentEndpoint = '/api/products/filter';
        loadProducts(params, true);
    };

    // index page hero slider
    const slides = document.querySelectorAll('.hero .slide');
    let currentSlide = 0;
    if (slides.length > 0) {
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 5000);
    }
});
