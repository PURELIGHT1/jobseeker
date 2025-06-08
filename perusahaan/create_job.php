<?php
session_start();
require_once("../umum/config.php");

if (!isset($_SESSION['company_id'])) {
    header("Location: ../umum/login.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $location    = $_POST['location'] ?? '';
    $salary      = $_POST['salary'] ?? '';
    $category    = $_POST['category'] ?? '';
    $job_type    = $_POST['job_type'] ?? '';
    $deadline    = $_POST['deadline'] ?? '';

    if ($title && $description && $location && $salary && $category && $job_type && $deadline) {
        $stmt = $conn->prepare("INSERT INTO jobs (title, description, location, salary_range, category, job_type, application_deadline, company_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisisi", $title, $description, $location, $salary, $category, $job_type, $deadline, $_SESSION['company_id']);
        if ($stmt->execute()) {
            header("Location: company_jobs_view.php");
            exit();
        } else {
            $error = "Gagal menyimpan data lowongan.";
        }
    } else {
        $error = "Semua field wajib diisi.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buat Lowongan</title>
    <link rel="stylesheet" href="../pelamar_kerja/apply_form.css">
</head>
<body>
    <div class="form-container">
        <h1>Buat Lowongan Baru</h1>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="POST">
            <label>Judul Pekerjaan</label>
            <input type="text" name="title" required>

            <label>Deskripsi</label>
            <textarea name="description" required></textarea>

            <label>Lokasi</label>
            <input type="text" name="location" required>

            <label>Gaji</label>
            <input type="number" name="salary" required>

            <label>Kategori Pekerjaan</label>
            <input type="text" name="category" required>

            <label>Jenis Pekerjaan</label>
            <input type="text" name="job_type" required>

            <label>Tanggal Batas Lamaran</label>
            <input type="date" name="deadline" required>

            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>
