const form = document.querySelector(".form"),
      continueBtn = form.querySelector("button"),
      unexpected_error = document.querySelector(".form-box .unexpected_error"),
      guestCart = JSON.parse(localStorage.getItem('guest_cart')) || [];
       
// Prevent default form submission behavior
form.onsubmit = (e) => {
    e.preventDefault();
};
 /**
  * Send guest cart to server before navigating to Google
  * This is necessary to ensure that the guest cart items are saved
  * before the user is redirected to Google for authentication.
  */
if (guestCart.length > 0) {
    // Send guest cart to server before navigating to Google
    const Continue_with_google = document.querySelector('.Continue-with-google');
    Continue_with_google.addEventListener('click', (e) => {
        e.preventDefault();
        const url = Continue_with_google.getAttribute('href');
        // Disable the button to prevent multiple clicks
        Continue_with_google.disabled = true; 
        // Send guest cart to server
        sendGuestCartToServer(url);
    });
}
const sendGuestCartToServer = (url) => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/guest-cart-session", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').getAttribute("content"));

    xhr.onload = () => {
        if (xhr.status === 200) {
            //remove guest cart from localStorage
            localStorage.removeItem('guest_cart');

             window.location.href=url;
        } else {
            unexpected_error.textContent="Failed to send guest cart";
        }
    };

    xhr.send(JSON.stringify({ cart: guestCart }));
};

// Handle the continue button click event
continueBtn.onclick = () => { 
    const input_password = form.querySelector("input[name='password']");
    const input_email = form.querySelector("input[name='email']");
    const input_name = form.querySelector("input[name='name']");
   
    // Reset error messages for all fields
    input_password.classList.remove("invalid");
    input_email.classList.remove("invalid");
    if( input_name) input_name.classList.remove("invalid");

    // after submitting the form, we will disable the input and continue button until we get a response from the server
    form.classList.add("disabled");

    // Validate required fields
    const xhr = new XMLHttpRequest(); 
    xhr.open("POST", form.getAttribute("action"), true);

    // Set CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
    xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    xhr.setRequestHeader("Accept", "application/json");

    xhr.onload = () => {
        form.classList.remove("disabled");
        try {
            const data = JSON.parse(xhr.response);
            unexpected_error.style.display="none";

            // Check the status code and handle the response accordingly
            switch (xhr.status) {
                case 201:
                case 200:
                    if (data.success) {
                         //remove guest cart from localStorage
                        localStorage.removeItem('guest_cart');
                        window.location.href = `${data.redirect || '/'}`; // Redirect to the specified URL or home page
                    } else {
                        unexpected_error.style.display="block";
                        unexpected_error.textContent = data.message || "Registration failed.";
                    }
                    break;

                case 401:
                    unexpected_error.style.display="block";
                    unexpected_error.textContent = data.message || "email or password is incorrect.";
                    break;    

                case 422:
                     
                    for (const field in data.message) {
                        if (field === "name") { 
                            input_name.classList.add("invalid");
                            document.querySelector(".name").textContent = data.message[field][0];
                        }
                        if (field === "email") {
                            input_email.classList.add("invalid");
                            document.querySelector(".email").textContent = data.message[field][0];
                        }
                        if (field === "password") {
                            
                            input_password.classList.add("invalid");
                            document.querySelector(".password").textContent = data.message[field][0];
                        }
                        if (field === "attempts") {
                            unexpected_error.style.display="block";
                            unexpected_error.innerHTML =data.message[field][0];
                            const countdown = document.createElement("span");
                            unexpected_error.appendChild(countdown);
                            startCountdown(data.message[field][1], countdown);
                        }
                    }  
                    break;

                case 500:
                    unexpected_error.style.display="block";
                    unexpected_error.textContent = "Server error. Please try again later.";
                    break;

                default:
                    unexpected_error.style.display="block";
                    unexpected_error.textContent = data.message || "Unexpected error occurred.";
            }
        } catch (error) {
            unexpected_error.style.display="block";
            unexpected_error.textContent = "Invalid response from server.";
        }
    };

    xhr.onerror = () => {
        unexpected_error.style.display="block";
        unexpected_error.textContent = "Network error. Please check your connection.";
    };

    const formData = new FormData(form);
    // Append array values to FormData
    guestCart.forEach((item, index) => {
      formData.append(`cart[${index}][product_variant_id]`, item.product_variant_id);
      formData.append(`cart[${index}][quantity]`, item.quantity);
    });
    xhr.send(formData);
};

// Function to start a countdown timer
function startCountdown(seconds,countdown) {
   
   const timer = setInterval(() => {
     seconds--;
     countdown.textContent = `${seconds} seconds`;
     
     if (seconds <= 0) {
       clearInterval(timer);
        unexpected_error.style.display="none";
     }
   }, 1000);
 }
