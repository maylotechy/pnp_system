<?php
header('Content-Type: application/json');
session_start();
require_once '../config/db_connection.php';

$response = ['success' => false, 'message' => ''];

try {
    // Get input
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        throw new Exception('Please fill in all fields');
    }

    // Check credentials
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, username, password_hash, full_name FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            // Login successful
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['full_name'];

            // Update last login
            $conn->query("UPDATE admin_users SET last_login = NOW() WHERE id = {$user['id']}");

            $response['success'] = true;
            $response['message'] = 'Login successful! Redirecting...';
        } else {
            throw new Exception('Invalid username or password');
        }
    } else {
        throw new Exception('Invalid username or password');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    echo json_encode($response);
}
?>