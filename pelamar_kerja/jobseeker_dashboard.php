<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../umum/config.php");

// Cek apakah pencari kerja sudah login
if (!isset($_SESSION['jobseeker_id'])) {
    header("Location: ../umum/login.php");
    exit();
}

$jobseeker_id = $_SESSION['jobseeker_id'];

// Ambil data user
$query = "SELECT full_name FROM job_seekers WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $jobseeker_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Data pengguna tidak ditemukan.");
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Pencari Kerja</title>
    <link rel="stylesheet" href="../pelamar_kerja/jobseeker_dashboard.css">

    <style>
        /* body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 80px auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 10px;
            text-align: center;
        }

        h2 {
            color: #2c3e50;
        }

        p {
            font-size: 16px;
            margin-bottom: 30px;
        }

        a {
            display: inline-block;
            margin: 10px;
            padding: 12px 20px;
            background-color:rgb(219, 52, 122);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }

        a:hover {
            background-color:rgb(219, 52, 122);
            color: white;
        } */
    </style>
</head>
<body>
    <div class="container">
        <h2>Selamat datang, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>

        <a href="job_list.php">Lihat Daftar Lowongan</a>
        <a href="../umum/logout.php">Logout</a>
    </div>
</body>
</html>
