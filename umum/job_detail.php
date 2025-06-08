<?php
session_start();
require_once("../umum/config.php");

$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil detail lowongan
$sql = "SELECT jobs.*, companies.company_name, companies.description AS company_desc, companies.logo 
        FROM jobs 
        JOIN companies ON jobs.company_id = companies.id 
        WHERE jobs.id = $job_id";
$result = mysqli_query($conn, $sql);
$job = mysqli_fetch_assoc($result);

if (!$job) {
    echo "Lowongan tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Detail Lowongan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>

    <section class="job-detail">
        <h2><?= $job['title'] ?></h2>
        <img src="<?= $job['logo'] ?>" alt="Logo Perusahaan" width="100">
        <p><strong>Perusahaan:</strong> <?= $job['company_name'] ?></p>
        <p><strong>Kategori:</strong> <?= $job['category'] ?></p>
        <p><strong>Jenis:</strong> <?= $job['type'] ?></p>
        <p><strong>Gaji:</strong> <?= $job['salary'] ?></p>
        <p><strong>Deadline:</strong> <?= $job['deadline'] ?></p>
        <p><strong>Deskripsi:</strong><br><?= nl2br($job['description']) ?></p>
        <p><strong>Tentang Perusahaan:</strong><br><?= nl2br($job['company_desc']) ?></p>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'pelamar'): ?>
            <a href="apply.php?job_id=<?= $job['id'] ?>" class="btn">Lamar Sekarang</a>
        <?php else: ?>
            <a href="login.php" class="btn">Login untuk Melamar</a>
        <?php endif; ?>
    </section>

    <?php include 'partials/footer.php'; ?>
</body>
</html>
