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
$job_id = $_GET['job_id'];

$stmt = $conn->prepare("delete from jobpost where JobID=:job_id");
$stmt->execute(['job_id' => $job_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);//fetch bool
header("Location: job_posts.php");
exit();
?>