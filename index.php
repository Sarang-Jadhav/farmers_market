<?php
require_once 'db.php';
require_once 'includes/auth.php';

$pageTitle = 'Home - Farmers Market';
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <!-- Hero Section -->
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold" style="color: #2c5f2d;">Welcome to Farmers Market</h1>
            <p class="lead text-muted">Connect directly with local farmers and get fresh, quality produce delivered to your doorstep.</p>
            
            <?php if (!isLoggedIn()): ?>
                <div class="d-grid gap-2 d-sm-flex">
                    <a href="register.php" class="btn btn-success btn-lg">
                        <i class="fas fa-user-plus"></i> Get Started
                    </a>
                    <a href="login.php" class="btn btn-outline-success btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
            <?php elseif (isCustomer()): ?>
                <a href="user/view_products.php" class="btn btn-success btn-lg">
                    <i class="fas fa-apple-alt"></i> Browse Products
                </a>
            <?php elseif (isFarmer()): ?>
                <a href="farmer/dashboard.php" class="btn btn-success btn-lg">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <div class="text-center">
                <i class="fas fa-leaf" style="font-size: 150px; color: #2c5f2d; opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row my-5">
        <h2 class="text-center mb-5" style="color: #2c5f2d;">How It Works</h2>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-seedling" style="font-size: 50px; color: #2c5f2d;"></i>
                    <h5 class="card-title mt-3">For Farmers</h5>
                    <p class="card-text">Register as a farmer, add your products, manage inventory, and track orders from customers.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-shopping-cart" style="font-size: 50px; color: #f4a642;"></i>
                    <h5 class="card-title mt-3">For Customers</h5>
                    <p class="card-text">Browse fresh produce from local farmers, add items to cart, and place orders easily.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-truck" style="font-size: 50px; color: #28a745;"></i>
                    <h5 class="card-title mt-3">Fast Delivery</h5>
                    <p class="card-text">Get your fresh produce delivered quickly. Support local farmers and eat fresh!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row bg-light py-5 rounded my-5">
        <div class="col-md-4 text-center">
            <h3 class="fw-bold" style="color: #2c5f2d;">500+</h3>
            <p class="text-muted">Active Farmers</p>
        </div>
        <div class="col-md-4 text-center">
            <h3 class="fw-bold" style="color: #2c5f2d;">5000+</h3>
            <p class="text-muted">Happy Customers</p>
        </div>
        <div class="col-md-4 text-center">
            <h3 class="fw-bold" style="color: #2c5f2d;">10K+</h3>
            <p class="text-muted">Orders Completed</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
