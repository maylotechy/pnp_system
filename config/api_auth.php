<?php
session_start();
header("Content-Type: application/json");

// Simple response function
function apiUnauthorized() {
    http_response_code(401);
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized - Admin session required'
    ]);
    exit;
}

// Check for admin session
if (!isset($_SESSION['admin_id'])) {
    apiUnauthorized();
}

// Optional: Verify admin still exists in database
require_once 'config/db_connection.php';

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT id FROM admin_users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // Admin no longer exists
    session_unset();
    session_destroy();
    apiUnauthorized();
}

$stmt->close();
?>