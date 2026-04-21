<?php
require_once 'db.php';
require_once 'includes/auth.php';

$pageTitle = 'My Profile - Farmers Market';
requireLogin();

$userDetails = getUserDetails();
$userId = getUserId();
$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email)) {
        $error = 'All fields are required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format!';
    } else {
        // Check if email is already taken (by another user)
        $checkEmail = $conn->query("SELECT id FROM users WHERE email = '$email' AND id != $userId");
        
        if ($checkEmail->num_rows > 0) {
            $error = 'Email already in use by another account!';
        } else {
            $updateQuery = "UPDATE users SET name = '$name', email = '$email' WHERE id = $userId";
            
            if ($conn->query($updateQuery)) {
                // Update session
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $userDetails = getUserDetails();
                $success = 'Profile updated successfully!';
            } else {
                $error = 'Failed to update profile.';
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All fields are required!';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters!';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match!';
    } else {
        // Verify old password
        $userResult = $conn->query("SELECT password FROM users WHERE id = $userId");
        $user = $userResult->fetch_assoc();
        
        if (!password_verify($oldPassword, $user['password'])) {
            $error = 'Old password is incorrect!';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE id = $userId";
            
            if ($conn->query($updateQuery)) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Failed to change password.';
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="display-5 mb-5" style="color: #2c5f2d;">
                <i class="fas fa-user-circle"></i> My Profile
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

            <!-- User Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted">Name</p>
                            <h6><?php echo htmlspecialchars($userDetails['name']); ?></h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">Email</p>
                            <h6><?php echo htmlspecialchars($userDetails['email']); ?></h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted">Account Type</p>
                            <h6>
                                <span class="badge bg-info">
                                    <?php echo ucfirst($userDetails['role']); ?>
                                </span>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($userDetails['name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($userDetails['email']); ?>" required>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="change_password" value="1">
                        
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-lock"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
