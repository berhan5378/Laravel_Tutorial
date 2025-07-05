/**
 * Cart Management Script
 * This script handles the cart functionality for both guest and logged-in users.
 * It allows adding, updating, and removing items from the cart, as well as checking out.
 * It also manages the display of cart items and their quantities.
 * 
 */
document.addEventListener("DOMContentLoaded", () => {
    const isGuest = document.querySelector('.signin');
    const checkoutBtn = document.querySelector('.checkout-btn');
    const cartContainer = document.querySelector('.cart-items'); 
    const cartCount = document.querySelector('.cart span');
    const guestCart = JSON.parse(localStorage.getItem('guest_cart')) || [];
    // Initialize cart count for guest users
    if(isGuest) {
        cartCount.textContent = guestCart.length;
        if(cartContainer) document.querySelector('.title-container h2').textContent =`Cart (${guestCart.length})`
    }
    /**
     * Update or create cart item via API
     * This function sends a request to the server to update or create a cart item.
     * It handles the response and updates the cart count and item quantity accordingly.
     * @param {number} variantId - The ID of the product variant to update or create.
     * @param {number} quantity - The quantity to update or create.
     * @param {Event} event - The event that triggered the function, used to access the button or input element.
     */
    function updateOrCreate_CartAPI(variantId, quantity,event) {
        const loader = event.querySelector(".loader");
        if(loader) loader.style.display = "inline-block"; 
        fetch('/api/carts/updateOrCreate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                product_variant_id: variantId,
                quantity: quantity
            })
        })
        .then(res => res.json())
        .then(data => {
            
            if(loader) loader.style.display = "none";
            if (data.success) {
                cartCount.textContent = data.cart_count;
                event.dataset.cartQuantity = data.quantity; 
                if (event instanceof HTMLInputElement) { 
                    event.value=data.quantity;  
                    event.previousElementSibling.disabled=(event.value > 1)?false:true;
                }
                window.createNotification('success', data.message);
                event.disabled = data.isMaxLimit;      
            } else {
                if(data.message !== 'Limit reached') event.disabled = false;  
                window.createNotification('error', data.message || 'Failed to add to cart');
            }
        })
        .catch(err => { 
            window.createNotification('error','Something went wrong');
        });
    } 
 
    /**
     * Update or create guest cart item
     * This function updates or creates a cart item in the guest cart stored in localStorage.
     * It handles the item quantity and updates the cart count displayed on the page.
     * @param {number} variantId - The ID of the product variant to update or create.
     * @param {number} quantity - The quantity to update or create.
     * @param {Event} event - The event that triggered the function, used to access the button or input element.
     */
    function updateOrCreate_guestCart(variantId, quantity,event){
        const item = event.closest('.product-card'); 
        const variantStock = parseInt(item?.dataset.variantStock)??null;
        const cartItem = {
            product_variant_id: variantId,
            variant_stock: variantStock,
            quantity: quantity,
            product_name: item?.querySelector('h3').textContent,
            currentPrice : item?.querySelector('.current-price').textContent,
            originalPrice : item?.querySelector('.original-price') ? item?.querySelector('.original-price').textContent : null,
            product_image: item?.querySelector('.product-image').style.backgroundImage.slice(5, -2),
            product_description: item?.querySelector('p').textContent,
            product_badge: item?.querySelector('.product-badge').textContent, 
            product_rating: item?.querySelector('.star-rating .stars').style.setProperty('--rating', item.querySelector('.star-rating .stars').style.getPropertyValue('--rating'))
        };
        const cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        let isMaxLimit=false; 
        const existingItem = cart.find(item => item.product_variant_id === variantId);
        let msg='added to cart!';
        if (existingItem) {
            existingItem.quantity += quantity;
            isMaxLimit=(existingItem.variant_stock - existingItem.quantity) > 0 ?false:true;
            msg='Quantity updated';
        } else {
            cart.push(cartItem); 
            isMaxLimit=(cartItem.variant_stock - cartItem.quantity) > 0 ?false:true;
        }  
        event.disabled = isMaxLimit; 
        event.dataset.cartQuantity = parseInt(event.dataset.cartQuantity)+ quantity; 
        if (event instanceof HTMLInputElement) {
            event.value=(existingItem) ?existingItem.quantity:cartItem.quantity; 
            event.previousElementSibling.disabled=(event.value > 1)?false:true;
        }
        localStorage.setItem('guest_cart', JSON.stringify(cart));
        window.createNotification('success', msg);
        const totalQuantity = cart.length;
        cartCount.textContent = totalQuantity;
    } 

    // Load more button functionality
    // This section handles the loading of more cart items when the "Load More" button is clicked.
    // It uses an offset and limit to control how many items are loaded at a time.
    const loadMoreBtn = document.getElementById('load_more');
    let offset = 0;
    const limit = 4;
    if(loadMoreBtn){
        loadCartItems();
        loadMoreBtn.addEventListener('click', () => {
        loadMoreBtn.classList.add('loading');
            loadCartItems();
        });
    }

    /** * Load cart items
     * This function loads cart items either from localStorage for guest users or from the server for logged-in users.
     * It handles pagination using offset and limit, and updates the cart container with the loaded items.
     * @param {URLSearchParams} params - Optional parameters for the API request.
     * @returns {void}
     */
    function loadCartItems(params = new URLSearchParams()) { 

        if (!cartContainer) return;
        if (isGuest) {
            // Guest user logic (load from localStorage)
            const itemsToShow = guestCart.slice(offset, offset + limit);

            itemsToShow.forEach(cart => {
                const isMaxLimit= (cart.variant_stock - cart.quantity) > 0 ?false:true;
                const cartItemHTML = `
                <div class="cart-item" data-variant-id="${cart.product_variant_id}" data-variant-stock="${cart.variant_stock}">
                    <input type="checkbox" class="cart-checkbox" value="${cart.product_variant_id}">
                    <div class="product-col">
                        <div class="product-image" style="background-image: url('${cart.product_image}')"></div>
                        <div class="product-details">
                            <h3 class="product-title" title="${cart.product_name}">${cart.product_name}</h3>
                            <p class="product-description" title="${cart.product_description}">${cart.product_description}</p>
                            <div class="product-attributes">
                                <span class="attribute">${cart.product_badge || ''}</span>
                            </div>
                        </div>
                    </div>
                    <div class="price-col">
                        <span class="current-price">${cart.currentPrice}</span>
                        <span class="original-price">${cart.originalPrice?cart.originalPrice:''}</span>
                    </div>
                    <div class="quantity-col">
                        <div class="quantity-selector">
                            <button class="qty-btn minus" ${cart.quantity == 1 ? 'disabled':''}>-</button>
                            <input type="number" data-cart-quantity="${cart.quantity}" value="${cart.quantity}" min="1" step="1"  class="qty-input" ${isMaxLimit?'disabled':''}>
                            <button class="qty-btn plus">+</button>
                        </div>
                    </div> 
                    <div class="action-col">
                        <button class="remove-item" title="Remove item" data-cart-id="${cart.product_variant_id}"><i class="ri-delete-bin-line"></i></button>
                    </div>
                </div>
                `;
                cartContainer.insertAdjacentHTML('beforeend', cartItemHTML);
            }); 
            offset += limit;
            loadMoreBtn.classList.remove('loading');
            if (offset >= guestCart.length) {
                loadMoreBtn.style.display = 'none';
            }
    
        } else {
            // Logged-in user logic (fetch from server)
            params.set('offset', offset);
    
            fetch(`/api/carts?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) { 
                        const carts = data.carts;
    
                        carts.forEach(cart => {
                            let current_price = cart.variant.product.discount_price || cart.variant.product.original_price;
                            const deff = cart.variant.stock - cart.quantity;
                            const isMaxLimit= deff > 0 ?false:true;
                            const cartItemHTML = `
                            <div class="cart-item ${ deff < 0 ? 'exceed-limit' : ''}" data-variant-id="${cart.product_variant_id}" data-variant-stock="${cart.variant.stock}">
                                <input type="checkbox" class="cart-checkbox" value="${cart.id}">
                                <div class="product-col">
                                    <div class="product-image" style="background-image: url('/assets/img/${cart.variant.img}')"></div>
                                    <div class="product-details">
                                        <h3 class="product-title">${cart.variant.product.name}</h3>
                                        <p class="product-description">${cart.variant.product.description}</p>
                                        <div class="product-attributes">
                                            <span class="attribute"> Color: ${cart.variant.color}</span>
                                            <span class="attribute"> Size: ${cart.variant.size}</span>
                                            <span class="attribute"> Stock: ${cart.variant.stock}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="price-col">
                                    <span class="current-price">$${current_price}</span>
                                    ${cart.variant.product.discount_price ? `<span class="original-price">$${cart.variant.product.original_price}</span>` : ''}
                                </div>
                                <div class="quantity-col">
                                    <div class="quantity-selector">
                                        <button class="qty-btn minus" ${cart.quantity == 1 ? 'disabled':''}>-</button>
                                        <input type="number" data-cart-quantity="${cart.quantity}" value="${cart.quantity}" min="1" step="1" class="qty-input" ${isMaxLimit?'disabled':''}>
                                        <button class="qty-btn plus">+</button>
                                    </div>
                                </div>
                                <div class="total-col">
                                    <span class="item-total">$${(current_price * cart.quantity).toFixed(2)}</span>
                                </div>
                                <div class="action-col">
                                    <button class="remove-item" title="Remove item" data-cart-id="${cart.id}"><i class="ri-delete-bin-line"></i></button>
                                </div>
                            </div>
                            `;
                            cartContainer.insertAdjacentHTML('beforeend', cartItemHTML);
                        }); 
                        offset += limit; 
                        loadMoreBtn.classList.remove('loading');
                        if (limit > carts.length) {
                            loadMoreBtn.style.display = 'none';
                        }  
                    } else {
                        loadMoreBtn.classList.add('error');
                        loadMoreBtn.querySelector('span:last-child').innerText = data.message;
                    }
                })
                .catch(error => {
                    console.error('Error loading cart:', error);
                });
        }
    }
    /**
     * Delete items from the guest cart
     * This function removes items from the guest cart stored in localStorage.
     * It filters out items whose IDs are in the variantIds array and updates the cart count.
     * It also removes the corresponding elements from the DOM.
     * @param {Array<number>} variantIds - The IDs of the product variants to delete.
     * @param {Array<Event>} event - The events that triggered the deletion.
     */
    function delete_guestCart(variantIds,event){
        const cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        // Filter out items whose IDs are NOT in the variantIds array
        const updatedCart = cart.filter(item => !variantIds.includes(item.product_variant_id));
    
        localStorage.setItem('guest_cart', JSON.stringify(updatedCart));
        // Remove deleted items from DOM
        event.forEach(e => {
            const item = e.closest('.cart-item');
            if (item) {
                item.classList.add('animate-pop');
                setTimeout(() => item.remove(), 350);
            }
        });
        window.createNotification('success', 'Deleted successfully.');
        cartCount.textContent = updatedCart.length;

    }
    /**
     * Delete items from the cart via API
     * This function sends a request to the server to delete multiple cart items.
     * @param {Array<number>} ids - The IDs of the cart items to delete.
     * @param {Array<Event>} event - The events that triggered the deletion.
     */
    function deleteCart(ids,event) {
            fetch('/api/carts/deleteMultiple', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ cart_ids: ids })
            })
            .then(response => response.json())
            .then(data => { 
                if (data.success) { 
                    window.createNotification('success', data.message);
                    // Remove deleted items from DOM
                    event.forEach(e => {
                    const item = e.closest('.cart-item');
                        if (item) {
                            item.classList.add('animate-pop');
                            setTimeout(() => item.remove(), 350);
                        }
                    });

                    // Update cart count
                    cartCount.textContent = data.cart_count;
                }else{
                    window.createNotification('error', data.message);
                }
            })
            .catch(err => window.createNotification('error', 'Failed to delete.'));
    }
    /**
     * Proceed to checkout for guest users
     * This function calculates the subtotal, shipping, tax, and order total for guest users.
     * It updates the cart summary section with these values based on the selected items.
     * It also handles the case where no items are selected by calculating totals for all items.
     * @returns {void}
     */
    function Proceed_To_checkout_guest(){
        const subtotal=document.querySelector('.cart-summary .subtotal');
        const shipping=document.querySelector('.cart-summary .shipping');
        const tax=document.querySelector('.cart-summary .tax');
        const order_total=document.querySelector('.cart-summary .order-total');
        const checkedInputs = cartContainer.querySelectorAll('.cart-checkbox:checked');

        let s_total=0; 
        checkedInputs.forEach((input) => { 
             const item = input.closest('.cart-item');
             const variantId = item.dataset.variantId;
             const cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
             const existingItem = cart.find(item => item.product_variant_id === variantId);
             if(existingItem){ 
               s_total+=parseInt(existingItem.currentPrice.replace('$', '')) * parseInt(existingItem.quantity);
             }
        });
            shipping.textContent=0;
            tax.textContent=0;
            subtotal.textContent="$"+s_total;
            order_total.textContent="$"+s_total;
    }
    function Proceed_To_checkout(){
        const subtotal=document.querySelector('.cart-summary .subtotal');
        const shipping=document.querySelector('.cart-summary .shipping');
        const tax=document.querySelector('.cart-summary .tax');
        const order_total=document.querySelector('.cart-summary .order-total');
        const checkedInputs = cartContainer.querySelectorAll('.cart-checkbox:checked');
        const orderItems = cartContainer.querySelectorAll('.cart-item');
        
        // If no checkboxes are checked, fallback to all cart items
        const itemsToProcess = checkedInputs.length > 0 ? checkedInputs : orderItems;
        
        // Extract data-variant-id from each cart item
        const product_variant_ids = Array.from(itemsToProcess).map(item => {
            const cartItem = item.closest('.cart-item') || item; // handles both checkbox or item directly
            return cartItem.dataset.variantId;
        });

        return fetch('/api/carts/cartSummary', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ product_variant_ids: product_variant_ids })
        })
        .then(async response => {    
            const res = await response.json();  
            return {
                status: response.status,
                ok: response.ok,
                data: res
            };
        })
        .then(result => {
            if (result.data.success) {
                shipping.textContent=result.data.total_shipping_price;
                tax.textContent=result.data.tax;
                subtotal.textContent=result.data.subtotal;
                order_total.textContent=result.data.order_total;
                if(result.data.exceed_items.length > 0){
                    result.data.exceed_items.forEach((item) => {
                        const itm = document.querySelector(`[data-variant-id="${item.variant_id}"]`);
                        itm.classList.add('exceed-limit');
                        itm.dataset.variantStock = item.variant_stock;
                    });  
                }
            }else{
                if (result.status !== 404) {
                    window.createNotification('error', result.data.message);
                }  
            } 
            return result.data;
        })
        .catch(err => window.createNotification('error', 'Failed to cartSummary.'));
    }

    // Event listeners for cart actions
    // This section handles the input changes, button clicks, and checkbox selections in the cart.
    // It updates the cart items, quantities, and checkout button state accordingly.
    // It also handles the checkout process for both guest and logged-in users.
    // It also handles the checkout process for both guest and logged-in users.

    if(cartContainer){
        cartContainer.addEventListener('input', function (e) {
            if (e.target && e.target.matches('input.qty-input')) {
                const item = e.target.closest('.cart-item'); 
                const variantId = item.dataset.variantId;
                const variantStock = parseInt(item.dataset.variantStock);
                const originalQty = parseInt(e.target.dataset.cartQuantity);
                const currentQty = parseInt(e.target.value); 
                (currentQty < 1 ) ? e.target.value = originalQty : e.target.value = currentQty;

                if (currentQty > variantStock && currentQty > originalQty) { 
                    window.createNotification('error', 'Limit reached');
                    e.target.value = originalQty;
                    return;
                }
                e.target.disabled = true; 
                if (currentQty !== originalQty && currentQty > 0) { 
                    if(isGuest) {
                        updateOrCreate_guestCart(variantId, (currentQty - originalQty),e.target); 
                    }else{
                        updateOrCreate_CartAPI(variantId, (currentQty - originalQty),e.target); 
                    }
                }else{
                    e.target.disabled = false; 
                    e.target.focus();
                }
            }else if(e.target && e.target.matches('input.Select_items')){
              const isChecked = e.target.checked;

              document.querySelectorAll('.cart-item:not(.exceed-limit) .cart-checkbox').forEach((input) => {
                  input.checked = isChecked;
              });  
              
            }

            const checkedInputs = cartContainer.querySelectorAll('.cart-checkbox:checked');
            if (checkedInputs.length > 0 || cartContainer.classList.contains('items-details')) {
                checkoutBtn.disabled = false;
                if(isGuest){
                    Proceed_To_checkout_guest()
                }else{
                    Proceed_To_checkout();
                }
            } else { 
                checkoutBtn.disabled = true;
            }
       
            
        });


        cartContainer.addEventListener('click', function (e) {

            const checkedInputs = cartContainer.querySelectorAll('.cart-checkbox:checked');
            
            if (e.target && e.target.matches('button.qty-btn.minus')){
                const item = e.target.closest('.cart-item');
                const input = e.target.nextElementSibling;
                const variantId = item.dataset.variantId;
                let currentQty = parseInt(input.value);
                e.target.disabled = true;
                if (currentQty > 1) {
                   input.disabled =true;
                   let newQty= currentQty-1; 
                   if(newQty !=1 )e.target.disabled = false;
         
                    if(isGuest){ 
                         updateOrCreate_guestCart(variantId, -1,input);
                    }else{
                         updateOrCreate_CartAPI(variantId, -1,input);
                    }
                } 
                if (checkedInputs.length > 0 || cartContainer.classList.contains('items-details')) {  
                   if(isGuest){
                        Proceed_To_checkout_guest()
                    }else{
                        Proceed_To_checkout();
                    }
                }
            }else if(e.target && e.target.matches('button.qty-btn.plus')){
                const item = e.target.closest('.cart-item');
                const input = e.target.previousElementSibling; 
                const variantId = item.dataset.variantId;
                const variantStock = parseInt(item.dataset.variantStock);
                const currentQty = parseInt(input.value); 
                input.disabled = true;
                if (currentQty >= variantStock) {
                    return;
                } 
                if(isGuest){ 
                     updateOrCreate_guestCart(variantId, 1,input);
                }else{
                     updateOrCreate_CartAPI(variantId, 1,input);
                }
                if (checkedInputs.length > 0 || cartContainer.classList.contains('items-details')) {  
                   if(isGuest){
                        Proceed_To_checkout_guest()
                    }else{
                        Proceed_To_checkout();
                    }
                }
            }else if (e.target && e.target.closest('button.remove-item')) {
                const button = e.target.closest('button.remove-item');
                const cart_id = button.dataset.cartId; 
                 
                if(isGuest){ 
                     delete_guestCart([cart_id],[button]);
                }else{
                     deleteCart([cart_id],[button]);
                }
                   if(isGuest){
                        Proceed_To_checkout_guest()
                    }else{
                        Proceed_To_checkout();
                    } 
                    document.querySelector('input.Select_items').checked =false;
             
            }else if(e.target && e.target.matches('a.delete-selected')){
                e.preventDefault();
                const checkedItems = document.querySelectorAll('.cart-checkbox:checked');

                if (checkedItems.length === 0) {
                    window.createNotification('error', 'Please select item'); 
                    return;
                }
                const ids = Array.from(checkedItems).map(input => input.value);
                if(isGuest){ 
                     delete_guestCart(ids,checkedItems);
                }else{
                     deleteCart(ids,checkedItems);
                } 
                document.querySelector('input.Select_items').checked =false;            
                
            }

        });

    }

    // Add to cart button functionality
    // This section handles the click event on the "Add to Cart" button in the product cards.
    // It checks the stock limit and updates the cart accordingly for both guest and logged-in users.
    // It also disables the button if the stock limit is reached.
    // It also updates the cart count displayed on the page.

    const products_container = document.querySelector('.products-container');

    if(products_container){
        products_container.addEventListener('click', function (e) {
            if (e.target && e.target.matches('button.add-to-cart')) {

                const item = e.target.closest('.product-card'); 
                const variantId = item.dataset.variantId; 
                const variantStock = parseInt(item.dataset.variantStock); 
                let originalQty = parseInt(e.target.dataset.cartQuantity);
                originalQty+=1;
                e.target.disabled = true; 
                
                if (originalQty > variantStock) { 
                   window.createNotification('error', 'Limit reached');
                   return;
                } 
                if(isGuest){ 
                     updateOrCreate_guestCart(variantId, 1,e.target);
                }else{
                     updateOrCreate_CartAPI(variantId, 1,e.target);
                }
            } 
            if(e.target && e.target.closest('button.wishlist-btn')){ 
                const item = e.target.closest('.product-card'); 
                const variantId = item.dataset.variantId; 
                if(isGuest){
                    window.location.href ='/auth/login';
                }else{
                    addWishlist(variantId);
                }
            }
           
        });
    }
    
    // Checkout button functionality
    // This section handles the click event on the checkout button.
    // It checks if the cart contains items and proceeds to checkout if it does.
    // If the cart is empty, it redirects to the order form page.
    // It also handles the case where the cart is in "items-details" mode.

    if(checkoutBtn){
        if(cartContainer.classList.contains('items-details')){
            checkoutBtn.disabled = false;
            checkoutBtn.textContent=  'Pay Now';
            Proceed_To_checkout();
        } 
        checkoutBtn.addEventListener('click', async () => {
            if(cartContainer.classList.contains('items-details')){
                checkoutBtn.disabled = true;
                const resp = await Proceed_To_checkout();  // 🔁 await here 

                if (resp && resp.success) {
                    const orderItems = cartContainer.querySelectorAll('.cart-item'); 
                    //extract shipping address ID and variant IDs
                    const shipping_address_id = document.querySelector('.shipping-address-id')?.value;
                    const variant_ids = Array.from(orderItems).map(item => item.dataset.variantId);
                    order(shipping_address_id, variant_ids);
                    checkoutBtn.disabled = false;
                }
            
            }else{
               window.location.href = '/view/orderForm';
            }
            
        });
    }
    //order functionality
    function order(shipping_address_id, variant_id) {
        fetch('/api/orders/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                shipping_address_id: shipping_address_id,
                product_variant_id: variant_id,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.createNotification('success', 'Order placed successfully');
                // Redirect to the order confirmation page
                window.location.href = `/view/orderlist`;
            } else {
                window.createNotification('error', 'Failed to place order');
            }
        });
    }
    //add wishlist 
    function addWishlist(variant_id){
        fetch('/api/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                product_variant_id: variant_id,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.createNotification('success', data.message);
            } else {
                window.createNotification('error', data.message);
            }
        });
    }
});