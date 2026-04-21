<?php
// Get cart count for AJAX
require_once '../db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$count = 0;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $result = $conn->query("SELECT COUNT(*) as count FROM cart WHERE user_id = $userId");
    
    if ($result) {
        $count = $result->fetch_assoc()['count'];
    }
}

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
?>
