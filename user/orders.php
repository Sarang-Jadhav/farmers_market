<?php
require_once '../db.php';
require_once '../includes/auth.php';

$pageTitle = 'Order History - Farmers Market';
requireCustomer();

$userId = getUserId();

// Get all orders
$orders = $conn->query("
    SELECT id, order_date, total_price
    FROM orders
    WHERE user_id = $userId
    ORDER BY order_date DESC
");
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="display-5" style="color: #2c5f2d;">
                <i class="fas fa-history"></i> Order History
            </h1>
            <p class="text-muted">View all your past orders</p>
        </div>
    </div>

    <?php if ($orders->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Items</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                            <td><strong>₹<?php echo number_format($order['total_price'], 2); ?></strong></td>
                            <td>
                                <?php 
                                $itemCount = $conn->query("SELECT COUNT(*) as count FROM order_items WHERE order_id = {$order['id']}")->fetch_assoc()['count'];
                                echo $itemCount . ' item' . ($itemCount != 1 ? 's' : '');
                                ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                        data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            </td>
                        </tr>

                        <!-- Order Details Modal -->
                        <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title">Order #<?php echo $order['id']; ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></p>
                                        
                                        <h6 class="mt-3 mb-3">Order Items:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Quantity</th>
                                                        <th>Price/kg</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $items = $conn->query("
                                                        SELECT oi.product_id, p.name, oi.quantity, oi.price
                                                        FROM order_items oi
                                                        JOIN products p ON oi.product_id = p.id
                                                        WHERE oi.order_id = {$order['id']}
                                                    ");
                                                    
                                                    while ($item = $items->fetch_assoc()):
                                                    ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                            <td><?php echo number_format($item['quantity'], 2); ?> kg</td>
                                                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                                            <td><strong>₹<?php echo number_format($item['quantity'] * $item['price'], 2); ?></strong></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="text-end mt-3">
                                            <h5>Order Total: <strong style="color: #2c5f2d;">₹<?php echo number_format($order['total_price'], 2); ?></strong></h5>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No orders yet. <a href="view_products.php">Start shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
