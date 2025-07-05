 /**
 * Search and Filter functionality for the e-commerce site
 * This script handles the sidebar filter, color selection, and product filtering.
 * It also manages the "Load More" button functionality.
 * The script listens for DOMContentLoaded event to ensure all elements are available.
 * It adds event listeners for filter button clicks, color selection, and input changes.
 * The filter function constructs a URLSearchParams object based on selected filters and sends a request to the server.
 * The script also handles resetting filters and updating the UI accordingly.
 */
document.addEventListener("DOMContentLoaded", () => {
    const filterBtn = document.querySelector(".filter-btn"); 
        //filter sidebar
    window.sidebar = document.querySelector(".search-results-container .sidebar"); 

    filterBtn.addEventListener("click", () => {
        window.sidebar.classList.add("active");
        window.popupClose.classList.add("active");
    });

    document.querySelectorAll('.color-circle').forEach(circle => {
        circle.addEventListener('click', () => circle.classList.toggle('active'));
    });

    window.sidebar.addEventListener('input', function (e) {
        filter();
    });

    document.getElementById('sort').addEventListener('change', function () {
        filter();
    });

        //get brand & type from url
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('brand')) { 
        const brand = urlParams.get('brand'); 
        const checkboxes = document.querySelectorAll(`input[name="brand[]"]`);
        checkboxes.forEach(checkbox => {
            if (checkbox.value === brand) checkbox.checked = true; 
        });
    } 
    if (urlParams.has('type')) {
        const type = urlParams.get('type');
        const checkboxes = document.querySelectorAll(`input[name="type[]"]`);
        checkboxes.forEach(checkbox => {
            if (checkbox.value === type) checkbox.checked = true;
        });
    }

    /**
     * Filter function
     * This function collects the selected filters from the sidebar,
     * constructs a URLSearchParams object, and sends a request to the server.
     * It updates the product container with the filtered results.
     */
    function filter() {
        window.popupClose.classList.remove("active");
        window.sidebar.classList.remove("active");
        window.container.innerHTML = '';
        if( window.loadMoreBtn.classList.contains('error') ) {
            window.loadMoreBtn.classList.remove('error');
            window.loadMoreBtn.querySelector('span:last-child').innerText = 'See More'; 
        }  
        window.loadMoreBtn.classList.add('loading');

        const getChecked = name => [...document.querySelectorAll(`input[name="${name}[]"]:checked`)].map(el => el.value);
        const getColor = () => [...document.querySelectorAll('.color-circle.active')].map(el => el.dataset.color);

        const params = new URLSearchParams();
        if (window.category) params.set("category",window.category);
        if (getChecked("type").length) params.set("type", getChecked("type").join(","));
        if (getChecked("brand").length) params.set("brand", getChecked("brand").join(","));
        if (getChecked("size").length) params.set("size", getChecked("size").join(","));
        if (getColor().length) params.set("color", getColor().join(","));

        const min = document.querySelector('input[name="min_price"]').value;
        const max = document.querySelector('input[name="max_price"]').value;
        const sort = document.querySelector('.sort')?.value;
        const shipping = document.querySelector('input[name="free_shipping"]')?.checked;

        if (min) params.set('min_price', min);
        if (max) params.set('max_price', max);
        if (shipping) params.set('free_shipping', true);
        if (sort) params.set('sort', sort);

        if (typeof window.setFilterParams === 'function') {
            window.setFilterParams(params);
        }
    } 

    document.querySelector('.btn-reset')?.addEventListener('click', () => {
        window.popupClose.classList.remove("active");
        window.sidebar.classList.remove("active");
        window.container.innerHTML = ''; 
        if( window.loadMoreBtn.classList.contains('error') ) {
            window.loadMoreBtn.classList.remove('error');
            window.loadMoreBtn.querySelector('span:last-child').innerText = 'See More'; 
        }  
        window.loadMoreBtn.classList.add('loading');

        document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
        document.querySelectorAll('input[type="number"]').forEach(el => el.value = '');
        document.querySelectorAll('.color-circle').forEach(el => el.classList.remove('active'));
        document.querySelector('.sort').value = 'relevance';

        if (typeof window.setFilterParams === 'function') {
           const param =new URLSearchParams();
           if (window.category) param.set("category",window.category);
            window.setFilterParams(param);
        }
    });
});
