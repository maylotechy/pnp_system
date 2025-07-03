<?php
require_once '../config/db_connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Validate personnel ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    $response['message'] = 'Invalid personnel ID';
    echo json_encode($response);
    exit;
}

$id = (int)$_POST['id'];

try {
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    $pdf_path = null;
    $old_pdf_path = null;

    // Get existing file path from DB
    $stmt = $conn->prepare("SELECT pdf_path FROM personnel WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $old_pdf_path = $row['pdf_path'];
    }
    $stmt->close();

    // Handle PDF upload if a new file is provided
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['application/pdf'];
        $file_type = $_FILES['pdf_file']['type'];

        if (!in_array($file_type, $allowed_types)) {
            throw new Exception('Invalid file type. Only PDF files are allowed.');
        }

        if ($_FILES['pdf_file']['size'] > 10 * 1024 * 1024) {
            throw new Exception('File size too large. Maximum 10MB allowed.');
        }

        $pdf_dir = '../assets/uploads/documents/';
        if (!is_dir($pdf_dir)) {
            mkdir($pdf_dir, 0755, true);
        }

        // Remove old file if it exists
        if ($old_pdf_path && file_exists('../' . $old_pdf_path)) {
            unlink('../' . $old_pdf_path);
        }

        // Save new file
        $pdf_ext = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
        $pdf_name = 'document_' . uniqid() . '_' . time() . '.' . $pdf_ext;
        $pdf_target = $pdf_dir . $pdf_name;

        if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdf_target)) {
            throw new Exception('Failed to upload new document');
        }

        // Save path relative to root
        $pdf_path = 'assets/uploads/documents/' . $pdf_name;
    }

    // Prepare update fields
    $allowedFields = [
        'badge_number', 'first_name', 'middle_name', 'last_name',
        'rank', 'station', 'has_cdlb', 'cdlb_type', 'cdlb_printed_date'
    ];

    $updates = [];
    $types = '';
    $values = [];

    foreach ($allowedFields as $field) {
        if (isset($_POST[$field])) {
            $value = ($_POST[$field] === '' && in_array($field, ['middle_name', 'cdlb_type', 'cdlb_printed_date']))
                ? null
                : ($_POST[$field] === 'on' && $field === 'has_cdlb' ? 1 : trim($_POST[$field]));

            $updates[] = "`$field` = ?";
            $types .= ($value === null || $field === 'has_cdlb') ? (($field === 'has_cdlb') ? 'i' : 's') : 's';
            $values[] = $value;
        }
    }

    // Include PDF path in update if a new file was uploaded
    if ($pdf_path !== null) {
        $updates[] = "pdf_path = ?";
        $types .= 's';
        $values[] = $pdf_path;
    }

    if (empty($updates)) {
        throw new Exception('No fields to update.');
    }

    // Finalize query
    $updates = implode(', ', $updates);
    $query = "UPDATE personnel SET $updates WHERE id = ?";
    $values[] = $id;
    $types .= 'i';

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = ($stmt->affected_rows > 0)
            ? 'Personnel updated successfully'
            : 'No changes made';
        if ($pdf_path !== null) {
            $response['pdf_path'] = $pdf_path;
        }
    } else {
        throw new Exception('Update failed: ' . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    if (isset($pdf_path) && file_exists('../' . $pdf_path)) {
        unlink('../' . $pdf_path); // Rollback if DB update fails
    }
    error_log("Update error: " . $e->getMessage());
    $response['message'] = 'Error: ' . $e->getMessage();
} finally {
    if (isset($conn)) $conn->close();
}

echo json_encode($response);
