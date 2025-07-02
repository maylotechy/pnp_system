<?php
require_once '../config/db_connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

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

    // First get the current personnel data
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

    // Prepare the update fields
    $allowedFields = [
        'badge_number', 'first_name', 'middle_name', 'last_name',
        'rank', 'station', 'has_cdlb', 'cdlb_type', 'cdlb_printed_date'
    ];

    $updates = [];
    $types = '';
    $values = [];

    foreach ($allowedFields as $field) {
        if (isset($_POST[$field])) {
            // Handle checkbox specifically
            if ($field === 'has_cdlb') {
                $value = isset($_POST['has_cdlb']) ? 1 : 0;
            } else {
                $value = $_POST[$field];
            }

            $updates[] = "`" . str_replace("`", "``", $field) . "` = ?"; // Properly escape field names
            $types .= 's'; // assuming all fields are strings
            $values[] = $value;
        }
    }

    // Add the ID to the values for WHERE clause
    $values[] = $_POST['id'];
    $types .= 'i';

    // Build and execute the update query
    $query = "UPDATE personnel SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Successfully updated record for ' . $name;
    } else {
        $response['message'] = 'Error updating record: ' . $conn->error;
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
} finally {
    if (isset($conn)) $conn->close();
}

echo json_encode($response);
?>