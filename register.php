<?php
require_once 'db.php';

$pageTitle = 'Register - Farmers Market';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = 'All fields are required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format!';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters!';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } elseif (!in_array($role, ['farmer', 'customer'])) {
        $error = 'Invalid role selected!';
    } else {
        // Check if email already exists
        $checkEmail = $conn->query("SELECT id FROM users WHERE email = '$email'");
        
        if ($checkEmail->num_rows > 0) {
            $error = 'Email already registered!';
        } else {
            // Hash password and insert user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO users (name, email, password, role, created_at) 
                          VALUES ('$name', '$email', '$hashedPassword', '$role', NOW())";
            
            if ($conn->query($insertQuery)) {
                $success = 'Registration successful! Redirecting to login...';
                echo '<script>setTimeout(function() { window.location.href = "login.php"; }, 2000);</script>';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0"><i class="fas fa-user-plus"></i> Create Account</h3>
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
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">I am a *</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">-- Select Role --</option>
                                <option value="farmer" <?php echo (isset($_POST['role']) && $_POST['role'] == 'farmer') ? 'selected' : ''; ?>>
                                    <i class="fas fa-seedling"></i> Farmer
                                </option>
                                <option value="customer" <?php echo (isset($_POST['role']) && $_POST['role'] == 'customer') ? 'selected' : ''; ?>>
                                    <i class="fas fa-user"></i> Customer
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mb-3">
                            <i class="fas fa-user-check"></i> Register
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="text-muted">Already have an account? 
                            <a href="login.php" class="text-decoration-none text-success fw-bold">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
