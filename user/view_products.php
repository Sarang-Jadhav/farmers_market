<?php
require_once '../db.php';
require_once '../includes/auth.php';

$pageTitle = 'Browse Products - Farmers Market';
requireCustomer();

$userId = getUserId();
$error = '';
$success = '';

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $productId = intval($_POST['product_id']);
    $quantity = floatval($_POST['quantity'] ?? 0);

    // Validation
    if ($quantity <= 0) {
        $error = 'Please enter a valid quantity!';
    } else {
        // Check if product exists
        $productResult = $conn->query("SELECT id, quantity FROM products WHERE id = $productId");
        
        if ($productResult->num_rows > 0) {
            $product = $productResult->fetch_assoc();
            
            if ($quantity > $product['quantity']) {
                $error = 'Not enough quantity available!';
            } else {
                // Check if product already in cart
                $cartCheck = $conn->query("SELECT id, quantity FROM cart WHERE user_id = $userId AND product_id = $productId");
                
                if ($cartCheck->num_rows > 0) {
                    // Update quantity
                    $cartItem = $cartCheck->fetch_assoc();
                    $newQuantity = $cartItem['quantity'] + $quantity;
                    
                    if ($newQuantity > $product['quantity']) {
                        $error = 'Total quantity exceeds available stock!';
                    } else {
                        $conn->query("UPDATE cart SET quantity = $newQuantity WHERE user_id = $userId AND product_id = $productId");
                        $success = 'Quantity updated in cart!';
                    }
                } else {
                    // Insert into cart
                    $insertQuery = "INSERT INTO cart (user_id, product_id, quantity, added_at) 
                                  VALUES ($userId, $productId, $quantity, NOW())";
                    
                    if ($conn->query($insertQuery)) {
                        $success = 'Product added to cart!';
                    } else {
                        $error = 'Failed to add product to cart.';
                    }
                }
            }
        } else {
            $error = 'Product not found!';
        }
    }
}

// Get all products with farmer information
$products = $conn->query("
    SELECT p.id, p.name, p.description, p.price_per_kg, p.quantity, u.name as farmer_name
    FROM products p
    JOIN users u ON p.farmer_id = u.id
    WHERE p.quantity > 0
    ORDER BY p.created_at DESC
");
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="display-5" style="color: #2c5f2d;">
                <i class="fas fa-apple-alt"></i> Shop Products
            </h1>
            <p class="text-muted">Browse fresh produce from our local farmers</p>
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

    <?php if ($products->num_rows > 0): ?>
        <div class="row">
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="col-md-6 mb-4" id="product<?php echo $product['id']; ?>">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #2c5f2d;">
                                <i class="fas fa-leaf"></i> <?php echo htmlspecialchars($product['name']); ?>
                            </h5>
                            <p class="card-text text-muted">
                                <small>By: <strong><?php echo htmlspecialchars($product['farmer_name']); ?></strong></small>
                            </p>
                            <p class="card-text"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Price per kg:</small>
                                    <h5 style="color: #f4a642;">₹<?php echo number_format($product['price_per_kg'], 2); ?></h5>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Available:</small>
                                    <h5 style="color: #28a745;">₹<?php echo number_format($product['quantity'], 2); ?> kg</h5>
                                </div>
                            </div>

                            <!-- Add to Cart Form -->
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <div class="row">
                                    <div class="col-8">
                                        <input type="number" class="form-control form-control-sm" name="quantity" 
                                               step="0.01" min="0.01" max="<?php echo $product['quantity']; ?>" 
                                               placeholder="Qty in kg" required>
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" name="add_to_cart" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-cart-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No products available at the moment.
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
