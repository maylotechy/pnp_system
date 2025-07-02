<?php
require_once 'config/db_connection.php';

if (!isset($_GET['badge_number'])) {
    header("HTTP/1.1 400 Bad Request");
    die("Badge number is required");
}

$badge_number = trim($_GET['badge_number']);
$conn = getDBConnection();

try {
    $stmt = $conn->prepare("SELECT pdf_path FROM personnel WHERE badge_number = ?");
    $stmt->bind_param("s", $badge_number);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result || empty($result['pdf_path'])) {
        header("HTTP/1.1 404 Not Found");
        die("CDLB document not found for this personnel");
    }

    $filepath = $result['pdf_path'];

    // Security check
    $filepath = realpath($filepath);
    $base_dir = realpath('assets/uploads/documents/');

    if ($filepath === false || !str_starts_with($filepath, $base_dir)) {
        header("HTTP/1.1 403 Forbidden");
        die("Invalid file path");
    }

    if (!file_exists($filepath)) {
        header("HTTP/1.1 404 Not Found");
        die("Document file not found at server location");
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="CDLB_'.$badge_number.'.pdf"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));

    readfile($filepath);
    exit;

} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    die("Error: " . $e->getMessage());
}