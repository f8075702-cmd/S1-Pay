<?php
session_start();
header('Content-Type: application/json');
if(!isset($_SESSION['user_email'])) { echo json_encode(['success'=>false,'message'=>'Not logged in']); exit; }

$input = json_decode(file_get_contents('php://input'), true);
$to_upi = trim($input['to_upi'] ?? '');
$amount = floatval($input['amount'] ?? 0);
$note = trim($input['note'] ?? '');

if($to_upi === '' || $amount <= 0){
    echo json_encode(['success'=>false,'message'=>'Invalid details']); exit;
}

$uFile = 'users.json';
$tFile = 'transactions.json';

// load users with lock
$fp = fopen($uFile, 'c+');
if(!$fp){ echo json_encode(['success'=>false,'message'=>'Server error']); exit; }
flock($fp, LOCK_EX);
$data = stream_get_contents($fp);
$users = json_decode($data, true);
if(!is_array($users)) $users = [];

// find sender and receiver
$senderIndex = null; $receiverIndex = null;
foreach($users as $i => $u){
    if($u['email'] === $_SESSION['user_email']) $senderIndex = $i;
    if($u['upi_id'] === $to_upi) $receiverIndex = $i;
}

if($receiverIndex === null){
    flock($fp, LOCK_UN); fclose($fp);
    echo json_encode(['success'=>false,'message'=>'Receiver UPI not found']); exit;
}

if($senderIndex === null){
    flock($fp, LOCK_UN); fclose($fp);
    echo json_encode(['success'=>false,'message'=>'Sender not found']); exit;
}

if($users[$senderIndex]['balance'] < $amount){
    flock($fp, LOCK_UN); fclose($fp);
    echo json_encode(['success'=>false,'message'=>'Insufficient balance']); exit;
}

// perform transfer
$users[$senderIndex]['balance'] = round($users[$senderIndex]['balance'] - $amount, 2);
$users[$receiverIndex]['balance'] = round($users[$receiverIndex]['balance'] + $amount, 2);

// rewrite users
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($users, JSON_PRETTY_PRINT));
flock($fp, LOCK_UN);
fclose($fp);

// append transaction (no locking for simplicity, but it's fine for demo)
$tx = [
    'id' => time().rand(1000,9999),
    'from_upi' => $users[$senderIndex]['upi_id'],
    'to_upi' => $users[$receiverIndex]['upi_id'],
    'amount' => $amount,
    'note' => $note,
    'datetime' => date('Y-m-d H:i:s')
];

$tdata = json_decode(file_get_contents($tFile), true);
if(!is_array($tdata)) $tdata = [];
$tdata[] = $tx;
file_put_contents($tFile, json_encode($tdata, JSON_PRETTY_PRINT));

echo json_encode(['success'=>true,'message'=>'Transfer successful']);
exit;
?>