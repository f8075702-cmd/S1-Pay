<?php
session_start();
if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header("Location: index.php"); exit; }

$email = strtolower(trim($_POST['email']));
$password = $_POST['password'];

$users = json_decode(file_get_contents('users.json'), true) ?: [];

$found = null;
foreach($users as $u){
    if($u['email'] === $email){
        $found = $u;
        break;
    }
}

if(!$found || !password_verify($password, $found['password'])){
    $_SESSION['error'] = "Invalid credentials.";
    header("Location: index.php");
    exit;
}

// login
$_SESSION['user_email'] = $found['email'];
$_SESSION['user_upi'] = $found['upi_id'];
header("Location: dashboard.php");
exit;
?>