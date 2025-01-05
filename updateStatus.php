<?php
require 'db.php';
session_start();

if (!isset($_SESSION['uid']) || !isset($_SESSION['type'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['type'] != 'recruiter') {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION['uid'];
$type = $_SESSION['type'];

$app_id = $_GET['app_id'];
$status = $_GET['status'];

echo $status;
$conn->beginTransaction();
$stmt = $conn->prepare("UPDATE application SET Status=:status where ApplyID=:apply_id");
$stmt->execute(['status'=>$status,'apply_id' => $app_id]);
$conn->commit();
exit();
?>