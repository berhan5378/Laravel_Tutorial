  /**
   * Header JavaScript for all pages
   * It includes search functionality, popup management, location and notification system.
   * 
   */
document.addEventListener("DOMContentLoaded", () => {
       window.popupClose = document.querySelector(".popup-close");
       const popupOpen = document.querySelectorAll(".popup-open");
       
       // Open popup on click
       popupOpen.forEach(p => p.addEventListener("click", () => {
           if (window.innerWidth <= 768 && popupClose) popupClose.classList.add("active");
       }));
   
       // close popup on click
       popupClose?.addEventListener("click", () => {
           popupClose.classList.remove("active");
           window.sidebar?.classList.remove("active");// Close sidebar if open for filter page
       });
   
        // Notification system
       function createNotification(type, message) {
           const container = document.getElementById('notification-container');
           
           // Create notification element
           const notification = document.createElement('div');
           notification.className = `notification ${type}`;
           
           // Set icon based on type
           let icon;
           if (type === 'success') {
               icon = '<i class="fas fa-check-circle notification-icon"></i>';
           } else {
               icon = '<i class="fas fa-exclamation-circle notification-icon"></i>';
           }
           
           // Notification content
           notification.innerHTML = `
               ${icon}
               <div class="notification-message">${message}</div>
               <button class="notification-close">&times;</button>
               <div class="progress-bar"></div>
           `;
           
           // Add to container
           container.appendChild(notification);
           
           // Show notification with animation
           setTimeout(() => {
               notification.classList.add('show');
           }, 10);
           
           // Auto-remove after 3 seconds
           const autoRemove = setTimeout(() => {
               removeNotification(notification);
           }, 3000);
       
           // Close button event
           const closeBtn = notification.querySelector('.notification-close');
           closeBtn.addEventListener('click', () => {
               clearTimeout(autoRemove);
               removeNotification(notification);
           });
           
       }
       // Function to remove notification with animation
       function removeNotification(notification) {
           notification.classList.remove('show');
           notification.addEventListener('transitionend', () => {
               notification.remove();
           });
       }
       // Create a global function to trigger notifications
       window.createNotification = createNotification;

       //search functionality
       const searchInput = document.querySelector('.popup-open');
       const dropdown = document.querySelector('.search-dropdown');
       const resultList = dropdown?.querySelector('.search-results');
    
       if( searchInput && dropdown && resultList) {
           searchInput.onkeyup = () => { 
               let query = searchInput.value;
               // If the input is empty, hide the dropdown
               if (query.trim() === '') {
                   dropdown.classList.remove('active');
                   return;
               }
               fetch(`/api/products/search?query=${encodeURIComponent(query)}`)
                   .then(res => res.json())
                   .then(data => {
                       // Clear previous results
                       dropdown.classList.add('active');
                       resultList.innerHTML = '';
   
                       //history and new search results
                       if (data.success && data.search_history.length > 0) { 
                           data.search_history.forEach(product => {
                               const li = document.createElement('li');
                               li.innerHTML = `<a href="http://127.0.0.1:8000/products/filter?category=${product.category}&product=${product.term}&brand=${product.brand}&type=${product.type}">
                                   <i class="ri-history-line"></i>${product.term}
                               </a>`;
                               resultList.appendChild(li);
                           });
                       }
   
                       if (data.success && data.search_new.length > 0) {
                           data.search_new.forEach(product => {
                               const li = document.createElement('li');
                               li.innerHTML = `<a href="http://127.0.0.1:8000/products/filter?category=${product.category}&product=${product.name}&brand=${product.brand}&type=${product.type}">
                                   <i class="ri-search-line"></i>${product.name}
                               </a>`;
                               resultList.appendChild(li);
                           });
                       } else if(!data.success) {
                           resultList.innerHTML = `<li><span style="margin-left: 18px;">${data.message}</span></li>`;
                       }
    
                   })
                   .catch(error => { 
                       resultList.innerHTML = '<li><span style="margin-left: 18px;">Error loading results</span></li>'; 
                   }); 
           };
       }
   
       //handle location
       document.getElementById('applyZipCode')?.addEventListener('click', function(){
           let zip = document.getElementById('zipCodeInput').value;
           let selectedCity = document.getElementById('citySelect').value;
           let zipError = document.getElementById('zipError');
   
           // check if input at least 4 characters
           if(zip.length < 4){
               zipError.textContent = 'ZIP code must be at least 4 characters.';
               return;
           }
   
           // post the zip code and selected city to the server
           fetch('/api/save-delivery-zip-session', {
               method: 'POST',
               headers: {
                   'Content-Type': 'application/json',
                   'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
               },
               body: JSON.stringify({
                   location: selectedCity,
                   zip: zip
               })
           })
           .then(response => response.json())
           .then(data => { 
               if (data.success) {
                   zipError.style.color = 'green'; 
                   document.querySelector('.delivery-info .dro-location').textContent = selectedCity; 
                   document.querySelector('.delivery-info .location').textContent = selectedCity;
                   document.querySelector('.login-dropdown .location').textContent = selectedCity;
                   zipError.textContent = 'ZIP code saved successfully.'; 
               } else {
                   zipError.style.color = 'red';
                   zipError.textContent = data.message || 'Failed to save ZIP.'; 
               }
           })
           .catch(error => { 
               zipError.textContent = 'Failed to save ZIP.';
           });
       }); 
});