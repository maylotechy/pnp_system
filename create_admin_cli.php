<?php
// create_admin_cli.php
require_once 'config/db_connection.php';

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from command line\n");
}

echo "================================\n";
echo " PNP System - Create Admin User\n";
echo "================================\n\n";

// Get admin details
$username = readline("Enter username: ");
$full_name = readline("Enter full name: ");

// Generate strong random password
function generateStrongPassword($length = 6) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ063456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

// Offer password generation option
echo "\nPassword options:\n";
echo "1. Auto-generate strong password\n";
echo "2. Enter custom password\n";
$choice = readline("Choose option (1/2): ");

if ($choice == '1') {
    // Auto-generate password
    $password = generateStrongPassword();
    echo "\nGenerated password: {$password}\n";

    $confirm = readline("Confirm creation with this password? (y/n): ");
    if (strtolower($confirm) !== 'y') {
        die("\n✖ Admin creation cancelled\n");
    }
} else {
    // Custom password
    echo "\nEnter password (minimum 6 characters with at least 1 uppercase, 1 lowercase, 1 number, and 1 special character):\n";

    $valid = false;
    while (!$valid) {
        $password = readline("Password: ");
        $confirm_password = readline("Confirm password: ");

        if ($password !== $confirm_password) {
            echo "✖ Passwords do not match. Please try again.\n";
            continue;
        }

        // Validate password strength
        if (strlen($password) < 6) {
            echo "✖ Password must be at least 6 characters long\n";
            continue;
        }

        if (!preg_match('/[A-Z]/', $password)) {
            echo "✖ Password must contain at least one uppercase letter\n";
            continue;
        }

        if (!preg_match('/[a-z]/', $password)) {
            echo "✖ Password must contain at least one lowercase letter\n";
            continue;
        }

        if (!preg_match('/[0-9]/', $password)) {
            echo "✖ Password must contain at least one number\n";
            continue;
        }

        if (!preg_match('/[!@#$%^&*()]/', $password)) {
            echo "✖ Password must contain at least one special character (!@#$%^&*())\n";
            continue;
        }

        $valid = true;
    }

    // Confirm the custom password
    $confirm_custom = readline("\nConfirm admin creation with this password? (y/n): ");
    if (strtolower($confirm_custom) !== 'y') {
        die("\n✖ Admin creation cancelled\n");
    }
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
$conn = getDBConnection();
$stmt = $conn->prepare("INSERT INTO admin_users (username, password_hash, full_name) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashed_password, $full_name);

if ($stmt->execute()) {
    echo "\n✔ Admin user created successfully!\n";
    if ($choice == '1') {
        echo "Password: {$password}\n";
    }
    echo "\n[!] SECURITY NOTICE:\n";
    echo "1. This password should be communicated securely\n";
    echo "2. The admin should change it immediately after login\n";
    echo "3. Consider enabling 2FA for admin accounts\n";
} else {
    echo "\n✖ Error creating admin user: " . $conn->error . "\n";
}