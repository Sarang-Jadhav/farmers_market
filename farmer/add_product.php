<?php
require_once '../db.php';
require_once '../includes/auth.php';

$pageTitle = 'Add Product - Farmers Market';
requireFarmer();

$userId = getUserId();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        // Insert product
        $insertQuery = "INSERT INTO products (farmer_id, name, description, price_per_kg, quantity, created_at) 
                       VALUES ($userId, '$name', '$description', $price_per_kg, $quantity, NOW())";
        
        if ($conn->query($insertQuery)) {
            $success = 'Product added successfully!';
            $_POST = []; // Clear form
        } else {
            $error = 'Failed to add product. Please try again.';
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0"><i class="fas fa-plus"></i> Add New Product</h3>
                </div>
                <div class="card-body p-5">
                    
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

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                   placeholder="e.g., Fresh Tomatoes">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required 
                                      placeholder="Describe your product..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price_per_kg" class="form-label">Price per Kg (₹) *</label>
                                <input type="number" class="form-control" id="price_per_kg" name="price_per_kg" 
                                       step="0.01" min="0" required 
                                       value="<?php echo htmlspecialchars($_POST['price_per_kg'] ?? ''); ?>"
                                       placeholder="5.99">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">Available Quantity (kg) *</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                       step="0.01" min="0" required 
                                       value="<?php echo htmlspecialchars($_POST['quantity'] ?? ''); ?>"
                                       placeholder="100">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-sm-flex">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> Add Product
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
