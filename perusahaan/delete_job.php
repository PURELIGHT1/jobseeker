<?php
session_start();
require_once("../umum/config.php");

if (!isset($_SESSION['company_id'])) {
    header("Location: ../umum/login.php");
    exit();
}

$company_id = $_SESSION['company_id'];
$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM jobs WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();

header("Location: company_jobs_view.php");
exit();
?>
