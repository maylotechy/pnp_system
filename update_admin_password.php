<?php
// reset_admin_password_cli.php
require_once 'config/db_connection.php';

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from command line\n");
}

echo "================================\n";
echo " PNP System - Admin Password Reset\n";
echo "================================\n\n";

// Get admin username to reset
$username = readline("Enter admin username to reset: ");

// Verify admin exists
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT id, full_name FROM admin_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("\n✖ Error: Admin user not found\n");
}

$admin = $result->fetch_assoc();
echo "\nResetting password for: {$admin['full_name']} ({$username})\n";

// Generate strong random password
function generateStrongPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

// Generate and confirm new password
$new_password = generateStrongPassword();
echo "\nGenerated new password: {$new_password}\n\n";

$confirm = readline("Confirm reset with this password? (y/n): ");
if (strtolower($confirm) !== 'y') {
    // If user declines auto-generated password, prompt for custom password
    echo "\nEnter custom password (minimum 6 characters with at least 1 uppercase, 1 lowercase, 1 number, and 1 special character):\n";

    $valid = false;
    while (!$valid) {
        $new_password = readline("New password: ");
        $confirm_password = readline("Confirm new password: ");

        if ($new_password !== $confirm_password) {
            echo "✖ Passwords do not match. Please try again.\n";
            continue;
        }

        // Validate password strength
        if (strlen($new_password) < 6) {
            echo "✖ Password must be at least 6 characters long\n";
            continue;
        }

        if (!preg_match('/[A-Z]/', $new_password)) {
            echo "✖ Password must contain at least one uppercase letter\n";
            continue;
        }

        if (!preg_match('/[a-z]/', $new_password)) {
            echo "✖ Password must contain at least one lowercase letter\n";
            continue;
        }

        if (!preg_match('/[0-9]/', $new_password)) {
            echo "✖ Password must contain at least one number\n";
            continue;
        }

        if (!preg_match('/[!@#$%^&*()]/', $new_password)) {
            echo "✖ Password must contain at least one special character (!@#$%^&*())\n";
            continue;
        }

        $valid = true;
    }

    // Confirm the custom password
    $confirm_custom = readline("\nConfirm reset with this custom password? (y/n): ");
    if (strtolower($confirm_custom) !== 'y') {
        die("\n✖ Password reset cancelled\n");
    }
}

// Update password in database
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
$update_stmt = $conn->prepare("UPDATE admin_users SET password_hash = ? WHERE username = ?");
$update_stmt->bind_param("ss", $new_hash, $username);

if ($update_stmt->execute()) {
    echo "\n✔ Password reset successful!\n";
    if ($confirm === 'y') {
        echo "New password: {$new_password}\n";
    } else {
        echo "Password was set to the custom value provided.\n";
    }
    echo "Please change this password immediately after login.\n";
} else {
    echo "\n✖ Error resetting password: " . $conn->error . "\n";
}

// Security recommendations
echo "\n[!] SECURITY NOTICE:\n";
echo "1. This password should be communicated securely\n";
echo "2. The admin should change it immediately after login\n";
echo "3. Consider enabling 2FA for admin accounts\n";