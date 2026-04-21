<?php
// Footer Component
?>

<!-- Footer -->
<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5><i class="fas fa-leaf" style="color: var(--secondary-color);"></i> Farmers Market</h5>
                <p class="text-muted">Connecting farmers directly to customers for fresh, quality produce.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="/farmers_market/" class="text-muted text-decoration-none">Home</a></li>
                    <li><a href="/farmers_market/user/view_products.php" class="text-muted text-decoration-none">Products</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">About Us</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Contact Info</h5>
                <p class="text-muted">
                    <i class="fas fa-envelope"></i> info@farmersmarket.com<br>
                    <i class="fas fa-phone"></i> +1 (555) 123-4567<br>
                    <i class="fas fa-map-marker-alt"></i> 123 Market Street, City
                </p>
            </div>
        </div>
        <hr class="bg-secondary">
        <div class="text-center text-muted">
            <p>&copy; 2026 Farmers Market. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Update cart count in navbar
    function updateCartCount() {
        fetch('/farmers_market/user/get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                const cartCount = document.getElementById('cartCount');
                if (cartCount) {
                    cartCount.textContent = data.count || 0;
                    cartCount.style.display = data.count > 0 ? 'inline-block' : 'none';
                }
            })
            .catch(error => console.log('Cart count fetch failed:', error));
    }

    // Update cart count on page load
    document.addEventListener('DOMContentLoaded', updateCartCount);
</script>

</body>
</html>
