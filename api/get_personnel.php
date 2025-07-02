<?php
require_once '../config/db_connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'data' => null, 'message' => ''];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $response['message'] = 'Invalid personnel ID';
    echo json_encode($response);
    exit;
}

try {
    $conn = getDBConnection();
    $id = $conn->real_escape_string($_GET['id']);
    $query = "SELECT * FROM personnel WHERE id = '$id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $response['success'] = true;
        $response['data'] = $result->fetch_assoc();
    } else {
        $response['message'] = 'Personnel not found';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
} finally {
    if (isset($conn)) $conn->close();
}

echo json_encode($response);