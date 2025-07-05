<footer class="footer-section">
    <div class="footer-container">
        <!-- Main Footer Content -->
        <div class="footer-main">
            <!-- Brand Column -->
            <div class="footer-brand">
                <a href="{{route('view.products.index')}}" class="footer-logo">Luxury<span>Finds</span></a>
                <p class="footer-tagline">Curating the world's finest pieces for the discerning collector.</p>
                
                <div class="social-links">
                    <a href="#" aria-label="Instagram"><i class="ri-instagram-line"></i></a>
                    <a href="#" aria-label="Pinterest"><i class="ri-pinterest-line"></i></a>
                    <a href="#" aria-label="Twitter"><i class="ri-twitter-line"></i></a>
                    <a href="#" aria-label="Facebook"><i class="ri-facebook-line"></i></a>
                </div>
                
                <div class="payment-methods">
                    <img src="{{ asset('img/visa.png') }}" alt="Visa">
                    <img src="{{ asset('img/Mastercard.jpg') }}" alt="Mastercard">
                    <img src="{{ asset('img/apple.jpg') }}" alt="Apple Pay">
                    <img src="{{ asset('img/PayPal_Logo_OffWhite.png') }}" alt="PayPal">
                </div>
            </div>
            
            <!-- Links Columns -->
            <div class="footer-links">
                <div class="links-column">
                    <h3 class="links-title">Shop</h3>
                    <ul>
                        <li><a href="#">New Arrivals</a></li>
                        <li><a href="#">Best Sellers</a></li>
                        <li><a href="#">Limited Editions</a></li>
                        <li><a href="#">Gift Cards</a></li>
                        <li><a href="#">Private Sales</a></li>
                    </ul>
                </div>
                
                <div class="links-column">
                    <h3 class="links-title">Services</h3>
                    <ul>
                        <li><a href="#">Personal Shopping</a></li>
                        <li><a href="#">Authenticity Guarantee</a></li>
                        <li><a href="#">White Glove Delivery</a></li>
                        <li><a href="#">Concierge Service</a></li>
                        <li><a href="#">VIP Program</a></li>
                    </ul>
                </div>
                
                <div class="links-column">
                    <h3 class="links-title">Company</h3>
                    <ul>
                        <li><a href="#">Our Story</a></li>
                        <li><a href="#">Sustainability</a></li>
                        <li><a href="#">Press</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="links-column">
                    <h3 class="links-title">Client Care</h3>
                    <ul>
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Book an Appointment</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="copyright">
                &copy; 2023 LuxuryFinds. All rights reserved.
            </div>
            
            <div class="legal-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Accessibility</a>
                <a href="#">Sitemap</a>
            </div>
            
            <div class="country-selector">
                <select aria-label="Country selector">
                    <option value="US">United States</option>
                    <option value="UK">United Kingdom</option>
                    <option value="EU">European Union</option>
                    <option value="AE">United Arab Emirates</option>
                    <option value="CN">China</option>
                </select>
            </div>
        </div>
    </div>
</footer>