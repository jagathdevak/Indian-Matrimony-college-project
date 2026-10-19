<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "indian_matrimony";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) session_start();

// Permanent email bans are kept separately from user accounts so a banned
// email cannot be reused after the account itself is removed.
$conn->query("CREATE TABLE IF NOT EXISTS banned_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    reason VARCHAR(255) DEFAULT NULL,
    banned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$adminEmail = "admin@vivah.local";
$adminPassword = "password";
// Valid bcrypt hash for the demo password above.
$adminPasswordHash = '$2y$12$EbzuPO96qunCTuVBr77Kj.5Dis5ysQXDV1GRDA5aCbbfd5PekM48u';

$checkAdmin = $conn->prepare("SELECT id,password FROM admins WHERE email=?");
$checkAdmin->bind_param("s", $adminEmail);
$checkAdmin->execute();
$existingAdmin = $checkAdmin->get_result()->fetch_assoc();

if (!$existingAdmin) {
    $addAdmin = $conn->prepare("INSERT INTO admins (name,email,password) VALUES (?,?,?)");
    $adminName = "Vivah Administrator";
    $addAdmin->bind_param("sss", $adminName, $adminEmail, $adminPasswordHash);
    $addAdmin->execute();
} elseif (!password_verify($adminPassword, $existingAdmin["password"])) {
    // Repairs an older/invalid demo admin hash automatically.
    $updateAdmin = $conn->prepare("UPDATE admins SET password=? WHERE id=?");
    $updateAdmin->bind_param("si", $adminPasswordHash, $existingAdmin["id"]);
    $updateAdmin->execute();
}

$columnCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'banned'");
if ($columnCheck && $columnCheck->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD banned TINYINT(1) NOT NULL DEFAULT 0");
}

// Migrate any older accounts that were already marked banned, then remove
// them from users. Their email remains permanently blocked in banned_users.
$conn->query("INSERT IGNORE INTO banned_users (email, reason)
              SELECT email, 'Migrated permanent ban'
              FROM users
              WHERE banned=1");
$conn->query("DELETE FROM users WHERE banned=1");


function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header("Location: admin_login.php");
        exit;
    }
}
?>