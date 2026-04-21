<?php
// Header Component
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_email']);
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Farmers Market'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c5f2d;
            --secondary-color: #f4a642;
            --danger-color: #dc3545;
            --success-color: #28a745;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a3a1b 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: #fff !important;
        }

        .navbar-brand i {
            margin-right: 8px;
            color: var(--secondary-color);
        }

        .nav-link {
            color: #fff !important;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--secondary-color) !important;
            transform: translateY(-2px);
        }

        .nav-link.active {
            color: var(--secondary-color) !important;
            border-bottom: 2px solid var(--secondary-color);
        }

        .btn-logout {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-logout:hover {
            background-color: #c82333;
            color: white;
        }

        .user-greeting {
            color: #fff;
            font-size: 0.9rem;
        }

        .dropdown-toggle::after {
            margin-left: 8px;
        }

        .dropdown-menu {
            min-width: 200px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/farmers_market/">
            <i class="fas fa-leaf"></i>Farmers Market
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                
                <?php if ($isLoggedIn): ?>
                    <?php if ($userRole === 'farmer'): ?>
                        <!-- Farmer Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="/farmers_market/farmer/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/farmers_market/farmer/add_product.php">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/farmers_market/farmer/manage_products.php">
                                <i class="fas fa-box"></i> My Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/farmers_market/farmer/view_orders.php">
                                <i class="fas fa-shopping-bag"></i> Orders
                            </a>
                        </li>
                    <?php elseif ($userRole === 'customer'): ?>
                        <!-- Customer Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="/farmers_market/user/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/farmers_market/user/view_products.php">
                                <i class="fas fa-apple-alt"></i> Shop
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/farmers_market/user/cart.php">
                                <i class="fas fa-shopping-cart"></i> Cart
                                <span class="badge bg-danger" id="cartCount">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/farmers_market/user/orders.php">
                                <i class="fas fa-history"></i> Orders
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($userName); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/farmers_market/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/farmers_market/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Guest Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="/farmers_market/login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/farmers_market/register.php">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

