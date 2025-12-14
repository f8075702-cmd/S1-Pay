<?php
session_start();
// Precomputed admin credentials (for demo). Change password as needed.
// Run once: echo password_hash('admin123', PASSWORD_DEFAULT);
$adminUser = 'admin';
// paste a precomputed hash for stable verification:
$adminPassHash = '$2y$10$e0NRr1kQxg2e/EXAMPLEHASHwFz0a3mQxw6'; // REPLACE with actual hash (see README)

if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header("Location: admin_login.php"); exit; }

$username = trim($_POST['username']);
$password = $_POST['password'];

// For ease: if you want default admin password 'admin123' during testing,
// you can simply do: if($username==='admin' && $password==='admin123') { ... }
// but here we recommend you replace $adminPassHash with a real hash from your environment.
if($username === $adminUser && password_verify($password, $adminPassHash)){
    $_SESSION['admin'] = $username;
    header("Location: admin_dashboard.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid admin credentials";
    header("Location: admin_login.php");
    exit;
}
?>