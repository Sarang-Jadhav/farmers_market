<?php
require_once '../db.php';
require_once '../includes/auth.php';

$pageTitle = 'Manage Products - Farmers Market';
requireFarmer();

$userId = getUserId();
$error = '';
$success = '';

// Handle delete product
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['product_id'])) {
    $productId = intval($_GET['product_id']);
    
    // Verify product belongs to farmer
    $checkResult = $conn->query("SELECT id FROM products WHERE id = $productId AND farmer_id = $userId");
    
    if ($checkResult->num_rows > 0) {
        if ($conn->query("DELETE FROM products WHERE id = $productId")) {
            $success = 'Product deleted successfully!';
        } else {
            $error = 'Failed to delete product.';
        }
    } else {
        $error = 'Product not found or unauthorized.';
    }
}

// Handle update product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $productId = intval($_POST['product_id']);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price_per_kg = $_POST['price_per_kg'] ?? '';
    $quantity = $_POST['quantity'] ?? '';

    // Validation
    if (empty($name) || empty($description) || empty($price_per_kg) || empty($quantity)) {
        $error = 'All fields are required!';
    } elseif (!is_numeric($price_per_kg) || $price_per_kg <= 0) {
        $error = 'Price must be a valid number greater than 0!';
    } elseif (!is_numeric($quantity) || $quantity <= 0) {
        $error = 'Quantity must be a valid number greater than 0!';
    } else {
        // Verify product belongs to farmer
        $checkResult = $conn->query("SELECT id FROM products WHERE id = $productId AND farmer_id = $userId");
        
        if ($checkResult->num_rows > 0) {
            $updateQuery = "UPDATE products SET name = '$name', description = '$description', 
                           price_per_kg = $price_per_kg, quantity = $quantity 
                           WHERE id = $productId";
            
            if ($conn->query($updateQuery)) {
                $success = 'Product updated successfully!';
            } else {
                $error = 'Failed to update product.';
            }
        } else {
            $error = 'Product not found or unauthorized.';
        }
    }
}

// Get all products of the farmer
$products = $conn->query("SELECT id, name, description, price_per_kg, quantity, created_at 
                          FROM products WHERE farmer_id = $userId 
                          ORDER BY created_at DESC");
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="display-5" style="color: #2c5f2d;">
                <i class="fas fa-box"></i> Manage Products
            </h1>
            <a href="add_product.php" class="btn btn-success mt-3">
                <i class="fas fa-plus"></i> Add New Product
            </a>
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
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price/kg</th>
                        <th>Quantity</th>
                        <th>Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></td>
                            <td>₹<?php echo number_format($product['price_per_kg'], 2); ?></td>
                            <td><?php echo number_format($product['quantity'], 2); ?> kg</td>
                            <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                        data-bs-target="#editModal<?php echo $product['id']; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="?action=delete&product_id=<?php echo $product['id']; ?>" 
                                   class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $product['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Edit Product</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="">
                                        <div class="modal-body">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="update_product" value="1">

                                            <div class="mb-3">
                                                <label for="name<?php echo $product['id']; ?>" class="form-label">Product Name</label>
                                                <input type="text" class="form-control" id="name<?php echo $product['id']; ?>" 
                                                       name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="description<?php echo $product['id']; ?>" class="form-label">Description</label>
                                                <textarea class="form-control" id="description<?php echo $product['id']; ?>" 
                                                          name="description" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="price<?php echo $product['id']; ?>" class="form-label">Price/kg (₹)</label>
                                                    <input type="number" class="form-control" id="price<?php echo $product['id']; ?>" 
                                                           name="price_per_kg" step="0.01" min="0" 
                                                           value="<?php echo $product['price_per_kg']; ?>" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="qty<?php echo $product['id']; ?>" class="form-label">Quantity (kg)</label>
                                                    <input type="number" class="form-control" id="qty<?php echo $product['id']; ?>" 
                                                           name="quantity" step="0.01" min="0" 
                                                           value="<?php echo $product['quantity']; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No products yet. <a href="add_product.php">Add your first product</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
