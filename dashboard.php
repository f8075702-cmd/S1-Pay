<?php
session_start();
if(!isset($_SESSION['user_email'])) { header("Location: index.php"); exit; }

function load_users(){ $u = json_decode(file_get_contents('users.json'), true); return is_array($u)?$u:[];}
function load_transactions(){ $t = json_decode(file_get_contents('transactions.json'), true); return is_array($t)?$t:[]; }

$users = load_users();
$current = null;
foreach($users as $u) if($u['email'] === $_SESSION['user_email']) $current = $u;
if(!$current){ header("Location: logout.php"); exit; }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - MiniUPI</title>
  <link rel="stylesheet" href="style.css">
  <!-- html5-qrcode for scanning -->
  <script src="https://unpkg.com/html5-qrcode@2.4.3/minified/html5-qrcode.min.js"></script>
  <!-- QRCode library for generating QR -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
  <div class="container">
    <div class="card small">
      <h3>Welcome, <?php echo htmlspecialchars($current['name']); ?></h3>
      <p><strong>UPI:</strong> <?php echo htmlspecialchars($current['upi_id']); ?></p>
      <p><strong>Balance:</strong> ₹ <?php echo number_format($current['balance'],2); ?></p>
      <p><a href="logout.php">Logout</a></p>

      <h4>Your UPI QR</h4>
      <div id="userQr" style="width:180px; height:180px; padding:10px; background:#fff;"></div>
      <small class="muted">Show this QR to receive money</small>
    </div>

    <div class="card">
      <h3>Send Money</h3>
      <form id="sendForm">
        <input name="to_upi" id="to_upi" placeholder="Receiver UPI (eg: someone@miniupi)" required>
        <div style="display:flex;gap:8px;">
          <input name="amount" id="amount" type="number" step="0.01" placeholder="Amount (₹)" required>
          <button type="button" id="scanBtn">Scan QR</button>
        </div>
        <input name="note" id="note" placeholder="Note (optional)">
        <button type="submit">Send</button>
      </form>
      <div id="sendMsg"></div>

      <div id="qrScanner" style="margin-top:12px; display:none;"></div>
    </div>

    <div class="card">
      <h3>Recent Transactions</h3>
      <table id="txTable" border="0" cellpadding="6">
        <thead><tr><th>Date</th><th>Type</th><th>Counterparty</th><th>Amount</th><th>Note</th></tr></thead>
        <tbody>
        <?php
        $txs = array_reverse(load_transactions());
        $count=0;
        foreach($txs as $t){
            if($t['from_upi'] === $current['upi_id'] || $t['to_upi'] === $current['upi_id']){
                $count++;
                echo "<tr>";
                echo "<td>".htmlspecialchars($t['datetime'])."</td>";
                $type = ($t['from_upi'] === $current['upi_id']) ? 'Sent' : 'Received';
                echo "<td>".$type."</td>";
                $counter = ($type==='Sent')?htmlspecialchars($t['to_upi']):htmlspecialchars($t['from_upi']);
                echo "<td>".$counter."</td>";
                $amt = ($type==='Sent')?("-₹ ".number_format($t['amount'],2)):("₹ ".number_format($t['amount'],2));
                echo "<td>".$amt."</td>";
                echo "<td>".htmlspecialchars($t['note'])."</td>";
                echo "</tr>";
            }
            if($count>=10) break;
        }
        if($count===0) echo "<tr><td colspan='5'>No transactions yet.</td></tr>";
        ?>
        </tbody>
      </table>
    </div>
  </div>

<script>
// Generate user QR
(function(){
    const upi = <?php echo json_encode($current['upi_id']); ?>;
    // Generate simple text QR (contains the UPI id). You can customize to include amount or UPI deeplink later.
    new QRCode(document.getElementById("userQr"), { text: upi, width: 160, height: 160 });
})();

// Scan QR logic
let scanner = null;
document.getElementById('scanBtn').addEventListener('click', function(){
    const sc = document.getElementById('qrScanner');
    if(sc.style.display === 'block'){
        // stop
        if(scanner){ scanner.stop().then(()=>{ sc.style.display='none'; scanner=null; }).catch(()=>{}); }
        return;
    }
    sc.style.display = 'block';
    scanner = new Html5Qrcode("qrScanner");
    scanner.start(
        { facingMode: "environment" },
        { fps:10, qrbox:250 },
        qrMessage => {
            // Fill the receiver UPI input
            document.getElementById('to_upi').value = qrMessage;
            // stop scanner
            scanner.stop().then(()=>{
                sc.style.display='none';
                scanner = null;
            }).catch(()=>{});
        },
        error => {
            // ignore scan errors
        }
    ).catch(err=>{
        alert("Camera not accessible or permission denied.");
        sc.style.display='none';
    });
});

// handle send with fetch to send_action.php
document.getElementById('sendForm').addEventListener('submit', async function(e){
    e.preventDefault();
    let to_upi = document.getElementById('to_upi').value.trim();
    let amount = document.getElementById('amount').value;
    let note = document.getElementById('note').value;

    let res = await fetch('send_action.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({to_upi, amount, note})
    });
    let data = await res.json();
    let el = document.getElementById('sendMsg');
    el.innerText = data.message;
    el.style.color = data.success ? 'green' : 'red';
    if(data.success) {
        setTimeout(()=> location.reload(), 1100);
    }
});
</script>
</body>
</html>