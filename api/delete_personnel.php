<?php
require_once '../config/db_connection.php';
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => ''];

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    $response['message'] = 'Invalid personnel ID';
    echo json_encode($response);
    exit;
}

try {
    $conn = getDBConnection();

    // First get the personnel name for the response message
    $stmt = $conn->prepare("SELECT first_name, last_name FROM personnel WHERE id = ?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = 'Personnel record not found';
        echo json_encode($response);
        exit;
    }

    $personnel = $result->fetch_assoc();
    $name = $personnel['last_name'] . ', ' . $personnel['first_name'];

    // Now delete the record
    $stmt = $conn->prepare("DELETE FROM personnel WHERE id = ?");
    $stmt->bind_param("i", $_POST['id']);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Successfully deleted record for ' . $name;
    } else {
        $response['message'] = 'Error deleting record: ' . $conn->error;
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
} finally {
    if (isset($conn)) $conn->close();
}

echo json_encode($response);
?>