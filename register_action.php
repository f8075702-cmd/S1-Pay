<?php
session_start();
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: register.php"); exit; }

function load_users(){
    $f = 'users.json';
    if(!file_exists($f)) file_put_contents($f, json_encode([]));
    $data = @file_get_contents($f);
    $arr = json_decode($data, true);
    return is_array($arr) ? $arr : [];
}
function save_users($users){
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
}

$name = trim($_POST['name']);
$email = strtolower(trim($_POST['email']));
$mobile = trim($_POST['mobile']);
$upi_id = trim($_POST['upi_id']);
$password = $_POST['password'];

if($name===''|| $email===''|| $upi_id===''|| $password===''){
    $_SESSION['error']="Please fill all fields.";
    header("Location: register.php"); exit;
}

$users = load_users();

// check unique email or upi_id
foreach($users as $u){
    if($u['email'] === $email){ $_SESSION['error']="Email already registered."; header("Location: register.php"); exit;}
    if($u['upi_id'] === $upi_id){ $_SESSION['error']="UPI ID already taken."; header("Location: register.php"); exit;}
}

$hashed = password_hash($password, PASSWORD_DEFAULT);
$initial_balance = 1000.00;

$new = [
    'id' => time() . rand(100,999),
    'name' => $name,
    'email' => $email,
    'mobile' => $mobile,
    'upi_id' => $upi_id,
    'password' => $hashed,
    'balance' => $initial_balance
];

$users[] = $new;
save_users($users);

// After registering we can log them in automatically or redirect to login.
// We'll redirect to login with success message and the user will login.
$_SESSION['success'] = "Account created. Login to continue.";
header("Location: index.php");
exit;
?>