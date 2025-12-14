<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: admin_login.php"); exit; }

$users = json_decode(file_get_contents('users.json'), true) ?: [];
$txs = json_decode(file_get_contents('transactions.json'), true) ?: [];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin - MiniUPI</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h2>Admin Dashboard</h2>
      <p><a href="admin_logout.php">Logout Admin</a></p>
      <h3>Users</h3>
      <table border="0" cellpadding="8">
        <tr><th>UPI</th><th>Name</th><th>Email</th><th>Mobile</th><th>Balance</th></tr>
        <?php foreach($users as $u){ echo "<tr><td>".htmlspecialchars($u['upi_id'])."</td><td>".htmlspecialchars($u['name'])."</td><td>".htmlspecialchars($u['email'])."</td><td>".htmlspecialchars($u['mobile'])."</td><td>₹ ".number_format($u['balance'],2)."</td></tr>"; } ?>
      </table>

      <h3>All Transactions (latest first)</h3>
      <table border="0" cellpadding="6">
        <tr><th>Date</th><th>From</th><th>To</th><th>Amount</th><th>Note</th></tr>
        <?php foreach(array_reverse($txs) as $t){ echo "<tr><td>".htmlspecialchars($t['datetime'])."</td><td>".htmlspecialchars($t['from_upi'])."</td><td>".htmlspecialchars($t['to_upi'])."</td><td>₹ ".number_format($t['amount'],2)."</td><td>".htmlspecialchars($t['note'])."</td></tr>"; } ?>
      </table>
    </div>
  </div>
</body>
</html>