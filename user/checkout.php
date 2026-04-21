<?php
require_once '../db.php';
require_once '../includes/auth.php';

$pageTitle = 'Checkout - Farmers Market';
requireCustomer();

$userId = getUserId();
$error = '';
$success = '';

// Get cart total
$cartResult = $conn->query("
    SELECT SUM(c.quantity * p.price_per_kg) as total, COUNT(c.id) as items
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $userId
");
$cartData = $cartResult->fetch_assoc();
$total = $cartData['total'] ?? 0;
$cartItems = $cartData['items'] ?? 0;

if ($cartItems == 0) {
    header('Location: cart.php');
    exit;
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create order
        $insertOrder = "INSERT INTO orders (user_id, total_price, order_date) 
                       VALUES ($userId, $total, NOW())";
        
        if (!$conn->query($insertOrder)) {
            throw new Exception('Failed to create order');
        }
        
        $orderId = $conn->insert_id;
        
        // Get cart items
        $items = $conn->query("
            SELECT c.id, c.product_id, c.quantity, p.price_per_kg, p.quantity as available
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = $userId
        ");
        
        // Insert order items and update product quantity
        while ($item = $items->fetch_assoc()) {
            // Check if product still has enough quantity
            if ($item['quantity'] > $item['available']) {
                throw new Exception('Product ' . $item['product_id'] . ' out of stock!');
            }
            
            // Insert into order_items
            $insertItem = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                          VALUES ($orderId, {$item['product_id']}, {$item['quantity']}, {$item['price_per_kg']})";
            
            if (!$conn->query($insertItem)) {
                throw new Exception('Failed to add order items');
            }
            
            // Update product quantity
            $newQuantity = $item['available'] - $item['quantity'];
            $updateProduct = "UPDATE products SET quantity = $newQuantity WHERE id = {$item['product_id']}";
            
            if (!$conn->query($updateProduct)) {
                throw new Exception('Failed to update product quantity');
            }
        }
        
        // Clear cart
        $conn->query("DELETE FROM cart WHERE user_id = $userId");
        
        // Commit transaction
        $conn->commit();
        
        $success = 'Order placed successfully! Order ID: #' . $orderId;
        echo '<script>setTimeout(function() { window.location.href = "orders.php"; }, 2000);</script>';
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = 'Checkout failed: ' . $e->getMessage();
    }
}

// Get cart items for review
$cartItems = $conn->query("
    SELECT c.id, c.quantity, p.name, p.price_per_kg, u.name as farmer_name
    FROM cart c
    JOIN products p ON c.product_id = p.id
    JOIN users u ON p.farmer_id = u.id
    WHERE c.user_id = $userId
    ORDER BY c.added_at DESC
");
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="display-5 mb-5" style="color: #2c5f2d;">
                <i class="fas fa-credit-card"></i> Checkout
            </h1>

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

            <!-- Order Review -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Order Review</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Farmer</th>
                                    <th>Quantity</th>
                                    <th>Price/kg</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = $cartItems->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['farmer_name']); ?></td>
                                        <td><?php echo number_format($item['quantity'], 2); ?> kg</td>
                                        <td>₹<?php echo number_format($item['price_per_kg'], 2); ?></td>
                                        <td><strong>₹<?php echo number_format($item['quantity'] * $item['price_per_kg'], 2); ?></strong></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-end">
                            <p><strong>Subtotal:</strong></p>
                            <p><strong>Tax (0%):</strong></p>
                            <hr>
                            <p style="font-size: 1.2rem;"><strong>Total:</strong></p>
                        </div>
                        <div class="col-6 text-end">
                            <p>₹<?php echo number_format($total, 2); ?></p>
                            <p>₹0.00</p>
                            <hr>
                            <p style="font-size: 1.2rem; color: #2c5f2d;"><strong>₹<?php echo number_format($total, 2); ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Delivery Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-truck"></i> 
                        <strong>Delivery within 2-3 business days</strong>
                        <br>
                        <small>Your order will be prepared by farmers and delivered to your address.</small>
                    </div>
                </div>
            </div>

            <!-- Place Order -->
            <form method="POST" action="">
                <div class="d-grid gap-2 d-sm-flex">
                    <button type="submit" name="place_order" class="btn btn-success btn-lg">
                        <i class="fas fa-check-circle"></i> Place Order
                    </button>
                    <a href="cart.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left"></i> Back to Cart
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
