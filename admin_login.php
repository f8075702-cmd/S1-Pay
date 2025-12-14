<?php
session_start();
if(isset($_SESSION['admin'])) header("Location: admin_dashboard.php");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login - MiniUPI</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="card auth">
    <h2>Admin Login</h2>
    <?php if(!empty($_SESSION['error'])){ echo "<div class='err'>".htmlspecialchars($_SESSION['error'])."</div>"; unset($_SESSION['error']); } ?>
    <form method="POST" action="admin_action.php">
      <input name="username" placeholder="Username" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p class="muted"><a href="index.php">Back</a></p>
  </div>
</body>
</html>