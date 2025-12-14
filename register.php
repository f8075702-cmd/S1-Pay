<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register - MiniUPI</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="card auth">
    <h2>Create Account</h2>

    <form method="POST" action="register_action.php">
      <input name="name" type="text" placeholder="Full name" required>
      <input name="email" type="email" placeholder="Email" required>
      <input name="mobile" type="text" placeholder="Mobile (eg: 9876543210)" required>
      <input name="upi_id" type="text" placeholder="Choose UPI ID (eg: name@miniupi)" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>

    <p class="muted"><a href="index.php">Back to Login</a></p>
  </div>
</body>
</html>