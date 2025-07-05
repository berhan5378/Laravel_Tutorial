/**
 * Order Form JavaScript
 * This script handles the editing of contact information and addresses in the order form.
 * It includes functionality to fetch edit forms, update shipments, and manage address selection.
 * 
 */

const editButton = document.getElementById('contact-edit');
const overflow=document.querySelector('.overflow');
const mainAddress = document.querySelector('.selected.address-details div');
/**
 * Fetch and display the edit form for the selected address.
 */
function editpage() {
    fetch("/view/orderForm/EditPage")
        .then(response => response.text())
        .then(html => {
            overflow.style.display = 'block';
            overflow.innerHTML = html;
        });
}
 
/**
 * Handle click events for the order form.
 * This function manages the display of edit forms and the submission of address updates.
 */
document.addEventListener('click', (event) => { 
    const edit=document.querySelector('.edit div');
    // Check if the click target is NOT inside the .edit element
    if (edit && !edit.contains(event.target)) {
        overflow.style.display = 'none';
    }
    if(event.target.matches('#contact-edit')) {
        editpage();
    }
    if(event.target.matches('.ri-close-circle-line')) {
        overflow.style.display = 'none';
    }
    if(event.target.matches('.edit-btn-b-link')) {
        event.preventDefault(); // Prevent default link behavior 
        const url = event.target.getAttribute('href'); // Get the URL from the link
 
        formPage(url);
    }
    if(event.target.matches('.add-btn')) {
        event.preventDefault(); // Prevent default link behavior 
        const url = "/view/orderForm/EditPage/addNewAddress"; // URL for adding a new address
        formPage(url);
    }
    if(event.target.matches('.confirm-btn')) {
        event.preventDefault(); // Prevent form submission
        const shipmentId = event.target.getAttribute('data-shipmentId'); // Get the shipment ID from the button
        if (shipmentId) {
            updateShipment(shipmentId); // Call the function to update the shipment
        } else {
            storeShipment(); // Call the function to store the shipment
        }
    }
    if(event.target.matches('.cancel-btn')) {
        event.preventDefault(); // Prevent default link behavior 
        overflow.style.display = 'none'; // Hide the edit form
    }

}); 
/**
 * Update the shipment details.
 * This function sends the updated shipment information to the server via a POST request.
 * @param {*} shipmentId 
 */
function updateShipment(shipmentId) {
    const editForm = document.querySelector('.edit form');
    const formData = new FormData(editForm);

    fetch(`/api/shipment/update/${shipmentId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }, 
        body: JSON.stringify(Object.fromEntries(formData)) // Convert FormData to JSON
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.createNotification('success', data.message);
        } else {
            window.createNotification('error', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
/**
 * Store a new shipment address.
 * This function sends the new shipment information to the server via a POST request.
 * 
 */
function storeShipment() {
    const editForm = document.querySelector('.edit form');
    const formData = new FormData(editForm);

    fetch("/api/shipment/store", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }, 
        body: JSON.stringify(Object.fromEntries(formData)) // Convert FormData to JSON
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.createNotification('success', data.message);
            // Update the main address with the new one
            mainAddress.innerHTML =`
                <input type="text" name="" class="shipping-address-id" value="${data.shipment.id}" hidden>
                <p class="name paragraph">${data.shipment.contact_name} &nbsp; <span class="phone-number">${data.shipment.contact_phone}</span></p>
                <p class="address">${data.shipment.address}</p>
                <p class="location">${data.shipment.sub_city}, ${data.shipment.city},${data.shipment.country}, ${data.shipment.zip_code}</p>`; // Update the main address with the new one
            
                document.querySelector('#contact-edit').textContent='Change'; // Change the button text to 'Edit'
            overflow.style.display = 'none'; // Hide the edit form 
        } else {
            window.createNotification('error', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
/**
 * Fetch and display the form for adding or editing an address.
 * This function retrieves the HTML content from the specified URL and updates the edit section.
 * @param {string} url - The URL to fetch the form from.
 */
function formPage(url){
    fetch(url)
        .then(response => response.text())
        .then(html => {
            const edit=document.querySelector('.edit div');
            edit.innerHTML = html;
        });
}
/**
 * Handle input events for the edit form.
 * This function manages the selection of addresses for editing.
 * It deselects other radio buttons when one is selected and updates the main address display.
 */
document.addEventListener('input', (event) => {
    if(event.target.matches('.edit input[type="radio"]')) { 
        
        // Deselect other radio buttons
        document.querySelectorAll('.edit input[type="radio"]').forEach(radio => {
            if (radio !== event.target) {
                radio.checked = false;
            }
        }); 
        const selectedAddress = document.querySelector('.edit input[type="radio"]:checked + div');

        // update the selected address
        if (mainAddress) { 
            mainAddress.innerHTML = selectedAddress.innerHTML;
        }

    }
});