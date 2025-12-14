<?php
session_start();
if(isset($_SESSION['user_email'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>MiniUPI - Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="card auth">
    <h1>MiniUPI</h1>

    <?php if(!empty($_SESSION['error'])){ echo "<div class='err'>".htmlspecialchars($_SESSION['error'])."</div>"; unset($_SESSION['error']); } ?>
    <?php if(!empty($_SESSION['success'])){ echo "<div class='ok'>".htmlspecialchars($_SESSION['success'])."</div>"; unset($_SESSION['success']); } ?>

    <form method="POST" action="login_action.php">
      <input name="email" type="email" placeholder="Email" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <p class="muted">New user? <a href="register.php">Create Account</a></p>
    <p class="muted"><a href="admin_login.php">Admin Login</a></p>
  </div>
</body>
</html>