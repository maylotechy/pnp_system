<?php
require_once '../config/db_connection.php';
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => '', 'errors' => []];

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Validation options
$station_options = [
    'CPPO-HQ', 'KIDAPAWAN-CPS', 'ALAMADA-MPS', 'ALEOSAN-MPS', 'ANTIPAS-MPS',
    'ARAKAN-MPS', 'BANISILAN-MPS', 'CARMEN-MPS', 'KABACAN-MPS', 'LIBUNGAN-MPS',
    'MAGPET-MPS', 'MAKILALA-MPS', 'MATALAM-MPS', 'MIDSAYAP-MPS', 'MLANG-MPS',
    'PIGKAWAYAN-MPS', 'PIKIT-MPS', 'PRESIDENT-ROXAS-MPS', 'TULUNAN-MPS',
    'CPPO-1PMFC', 'CPPO-PROVINCIAL-SAF', 'CPPO-TRAFFIC', 'CPPO-CIDG',
    'NORCOT-PPO', 'KIDAPAWAN-CPO', 'MIDSAYAP-PS', 'KABACAN-PS'
];

$rank_options = [
    'Pat', 'PEM', 'PMSg', 'PCMS', 'PSMS', 'PEMS', 'PCpt', 'PMaj',
    'PLtCol', 'PCol', 'PBGen', 'PMGen', 'PLtGen', 'PGen'
];

// Validate required fields
$required = ['badge_number', 'first_name', 'last_name', 'rank', 'station'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $response['errors'][$field] = 'This field is required';
    }
}

// Validate dropdowns
if (!in_array($_POST['rank'], $rank_options)) {
    $response['errors']['rank'] = 'Invalid rank selected';
}
if (!in_array($_POST['station'], $station_options)) {
    $response['errors']['station'] = 'Invalid station selected';
}

// CDLB fields
$has_cdlb = isset($_POST['has_cdlb']) && $_POST['has_cdlb'] === 'on';
$cdlb_type = null;
$cdlb_printed_date = null;

if ($has_cdlb) {
    if (empty($_POST['cdlb_type']) || !in_array(strtoupper($_POST['cdlb_type']), ['NQH', 'RHQ'])) {
        $response['errors']['cdlb_type'] = 'Invalid or missing CDLB type';
    } else {
        $cdlb_type = strtoupper($_POST['cdlb_type']);
    }

    if (empty($_POST['cdlb_printed_date'])) {
        $response['errors']['cdlb_printed_date'] = 'CDLB printed date is required';
    } else {
        $cdlb_printed_date = $_POST['cdlb_printed_date'];
    }
}

// If validation failed
if (!empty($response['errors'])) {
    $response['message'] = 'Please fix the validation errors';
    echo json_encode($response);
    exit;
}

try {
    $conn = getDBConnection();
    if (!$conn) throw new Exception('Database connection failed');

    // Check for duplicate badge number
    $stmt = $conn->prepare("SELECT id FROM personnel WHERE badge_number = ?");
    $stmt->bind_param("s", $_POST['badge_number']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $response['message'] = 'Badge number already exists';
        $response['errors']['badge_number'] = 'This badge number is already in use';
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    // Handle photo upload
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['photo']['type'];

        if (!in_array($file_type, $allowed_types)) {
            throw new Exception('Invalid file type. Only JPG, PNG, and GIF files are allowed.');
        }

        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            throw new Exception('File size too large. Maximum 5MB allowed.');
        }

        $photo_dir = '../assets/uploads/';
        if (!is_dir($photo_dir)) {
            mkdir($photo_dir, 0755, true);
        }

        $photo_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $photo_name = 'photo_' . uniqid() . '_' . time() . '.' . $photo_ext;
        $photo_target = $photo_dir . $photo_name;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_target)) {
            throw new Exception('Failed to upload photo');
        }

        $photo_path = 'assets/uploads/' . $photo_name;
    }

    
    $pdf_path = null;
    if (isset($_FILES['document_pdf']) && $_FILES['document_pdf']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['application/pdf'];
        $file_type = $_FILES['document_pdf']['type'];

        if (!in_array($file_type, $allowed_types)) {
            throw new Exception('Invalid file type. Only PDF files are allowed.');
        }

        if ($_FILES['document_pdf']['size'] > 10 * 1024 * 1024) {
            throw new Exception('File size too large. Maximum 10MB allowed.');
        }

        $pdf_dir = '../assets/uploads/documents/';
        if (!is_dir($pdf_dir)) {
            mkdir($pdf_dir, 0755, true);
        }

        $pdf_ext = strtolower(pathinfo($_FILES['document_pdf']['name'], PATHINFO_EXTENSION));
        $pdf_name = 'document_' . uniqid() . '_' . time() . '.' . $pdf_ext;
        $pdf_target = $pdf_dir . $pdf_name;

        if (!move_uploaded_file($_FILES['document_pdf']['tmp_name'], $pdf_target)) {
            throw new Exception('Failed to upload document');
        }

        $pdf_path = 'assets/uploads/documents/' . $pdf_name;
    }

    // Prepare SQL - Updated to match your table structure
    $sql = "INSERT INTO personnel (
        badge_number, `rank`, first_name, middle_name, last_name, station,
        date_created, photo_path, pdf_path, has_cdlb, cdlb_type, cdlb_printed_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception('SQL prepare failed: ' . $conn->error);

    $has_cdlb_int = $has_cdlb ? 1 : 0;
    $date_created = date('Y-m-d H:i:s');

    // Note: pdf_path is bound as a string, but your schema shows it as INT - you may need to modify your schema
    $stmt->bind_param("sssssssssiss",
        $_POST['badge_number'],
        $_POST['rank'],
        $_POST['first_name'],
        $_POST['middle_name'],
        $_POST['last_name'],
        $_POST['station'],
        $date_created,
        $photo_path,
        $pdf_path, // This should be a string path, but your schema shows INT
        $has_cdlb_int,
        $cdlb_type,
        $cdlb_printed_date
    );

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Personnel added successfully';
        $response['personnel_id'] = $conn->insert_id;
    } else {
        throw new Exception('Insert failed: ' . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Personnel add error: " . $e->getMessage());
    $response['message'] = 'An error occurred: ' . $e->getMessage();
} finally {
    if (isset($conn)) $conn->close();
}

echo json_encode($response);