<?php
require_once '../db.php';
require_once '../includes/auth.php';

$pageTitle = 'Shopping Cart - Farmers Market';
requireCustomer();

$userId = getUserId();
$error = '';
$success = '';

// Handle remove from cart
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['cart_id'])) {
    $cartId = intval($_GET['cart_id']);
    
    // Verify cart item belongs to user
    $checkResult = $conn->query("SELECT id FROM cart WHERE id = $cartId AND user_id = $userId");
    
    if ($checkResult->num_rows > 0) {
        if ($conn->query("DELETE FROM cart WHERE id = $cartId")) {
            $success = 'Item removed from cart!';
        } else {
            $error = 'Failed to remove item.';
        }
    } else {
        $error = 'Cart item not found.';
    }
}

// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    $cartId = intval($_POST['cart_id']);
    $newQuantity = floatval($_POST['quantity'] ?? 0);

    if ($newQuantity <= 0) {
        $error = 'Quantity must be greater than 0!';
    } else {
        // Verify cart item and check product quantity
        $cartResult = $conn->query("SELECT c.*, p.quantity as available FROM cart c 
                                   JOIN products p ON c.product_id = p.id 
                                   WHERE c.id = $cartId AND c.user_id = $userId");
        
        if ($cartResult->num_rows > 0) {
            $cartItem = $cartResult->fetch_assoc();
            
            if ($newQuantity > $cartItem['available']) {
                $error = 'Not enough quantity available!';
            } else {
                $conn->query("UPDATE cart SET quantity = $newQuantity WHERE id = $cartId");
                $success = 'Cart updated!';
            }
        } else {
            $error = 'Cart item not found.';
        }
    }
}

// Get cart items
$cartItems = $conn->query("
    SELECT c.id, c.quantity, p.id as product_id, p.name, p.price_per_kg, u.name as farmer_name
    FROM cart c
    JOIN products p ON c.product_id = p.id
    JOIN users u ON p.farmer_id = u.id
    WHERE c.user_id = $userId
    ORDER BY c.added_at DESC
");

// Calculate total
$totalResult = $conn->query("
    SELECT SUM(c.quantity * p.price_per_kg) as total
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $userId
");
$total = $totalResult->fetch_assoc()['total'] ?? 0;
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="display-5" style="color: #2c5f2d;">
                <i class="fas fa-shopping-cart"></i> Shopping Cart
            </h1>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($cartItems->num_rows > 0): ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Cart Items (<?php echo $cartItems->num_rows; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php while ($item = $cartItems->fetch_assoc()): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <p class="text-muted"><small>From: <?php echo htmlspecialchars($item['farmer_name']); ?></small></p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-4">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                        <input type="hidden" name="update_cart" value="1">
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" class="form-control" name="quantity" 
                                                                   step="0.01" min="0.01" 
                                                                   value="<?php echo number_format($item['quantity'], 2); ?>"
                                                                   onchange="this.form.submit()">
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="col-4">
                                                    <p class="mb-0"><strong>₹<?php echo number_format($item['quantity'] * $item['price_per_kg'], 2); ?></strong></p>
                                                    <small class="text-muted">@ ₹<?php echo number_format($item['price_per_kg'], 2); ?>/kg</small>
                                                </div>
                                                <div class="col-4">
                                                    <a href="?action=remove&cart_id=<?php echo $item['id']; ?>" 
                                                       class="btn btn-sm btn-danger" onclick="return confirm('Remove item?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₹<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (0%):</span>
                            <span>₹0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong style="font-size: 1.2rem; color: #2c5f2d;">₹<?php echo number_format($total, 2); ?></strong>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="checkout.php" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </a>
                            <a href="view_products.php" class="btn btn-outline-success">
                                <i class="fas fa-shopping-bag"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Your cart is empty. <a href="view_products.php">Start shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
