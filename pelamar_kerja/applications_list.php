<?php
session_start();
require_once("../umum/config.php");

// Proteksi halaman hanya untuk pelamar
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pelamar') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Ambil data lamaran pelamar dari DB
$sql = "SELECT a.id, j.title, a.applied_at, a.status, a.cv_file 
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        WHERE a.user_id = '$user_id'
        ORDER BY a.applied_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Daftar Lamaran Saya</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>

    <section class="applications-list">
        <h2>Daftar Lamaran Saya</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>Judul Pekerjaan</th>
                        <th>Tanggal Melamar</th>
                        <th>Status</th>
                        <th>CV</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= date('d M Y', strtotime($row['applied_at'])) ?></td>
                            <td><?= ucfirst($row['status']) ?></td>
                            <td>
                                <?php if (!empty($row['cv_file']) && file_exists($row['cv_file'])): ?>
                                    <a href="<?= $row['cv_file'] ?>" target="_blank" download>Download CV</a>
                                <?php else: ?>
                                    Tidak ada CV
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Belum ada lamaran yang diajukan.</p>
        <?php endif; ?>
    </section>

    <?php include 'partials/footer.php'; ?>
</body>
</html>
