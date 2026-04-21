<?php
require_once 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Login - Farmers Market';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required!';
    } else {
        // Check user in database
        $result = $conn->query("SELECT id, name, email, password, role FROM users WHERE email = '$email'");
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'farmer') {
                    header('Location: farmer/dashboard.php');
                } else {
                    header('Location: user/dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid password!';
            }
        } else {
            $error = 'User not found!';
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-sign-in-alt"></i> Login</h3>
                </div>
                <div class="card-body p-5">
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-arrow-right"></i> Login
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="text-muted">Don't have an account? 
                            <a href="register.php" class="text-decoration-none text-primary fw-bold">Register here</a>
                        </p>
                    </div>

                    <hr>
                    
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Demo Credentials</h6>
                        <p class="mb-2"><strong>Farmer:</strong> farmer@test.com / password123</p>
                        <p class="mb-0"><strong>Customer:</strong> customer@test.com / password123</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
