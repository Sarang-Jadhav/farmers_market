<?php
// Authentication & Authorization Helper

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_email']) && isset($_SESSION['user_role']);
}

// Check if user is a farmer
function isFarmer() {
    return isLoggedIn() && $_SESSION['user_role'] === 'farmer';
}

// Check if user is a customer
function isCustomer() {
    return isLoggedIn() && $_SESSION['user_role'] === 'customer';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /farmers_market/login.php');
        exit;
    }
}

// Redirect if not a farmer
function requireFarmer() {
    if (!isFarmer()) {
        header('Location: /farmers_market/login.php');
        exit;
    }
}

// Redirect if not a customer
function requireCustomer() {
    if (!isCustomer()) {
        header('Location: /farmers_market/login.php');
        exit;
    }
}

// Get current logged-in user ID
function getUserId() {
    global $conn;
    if (!isLoggedIn()) {
        return null;
    }
    
    $email = $_SESSION['user_email'];
    $result = $conn->query("SELECT id FROM users WHERE email = '$email'");
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id'];
    }
    return null;
}

// Get current logged-in user details
function getUserDetails() {
    global $conn;
    if (!isLoggedIn()) {
        return null;
    }
    
    $email = $_SESSION['user_email'];
    $result = $conn->query("SELECT id, name, email, role FROM users WHERE email = '$email'");
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

?>
