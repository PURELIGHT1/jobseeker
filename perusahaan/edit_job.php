<?php
session_start();
require_once("../umum/config.php");

if (!isset($_SESSION['company_id'])) {
    header("Location: ../umum/login.php");
    exit();
}

$company_id = $_SESSION['company_id'];
$id = intval($_GET['id'] ?? 0);

// Ambil data lowongan untuk diedit
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    die("Lowongan tidak ditemukan atau Anda tidak berhak mengaksesnya.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $location    = $_POST['location'] ?? '';
    $salary      = $_POST['salary'] ?? '';
    $category    = $_POST['category'] ?? '';
    $job_type    = $_POST['job_type'] ?? '';
    $deadline    = $_POST['deadline'] ?? '';

    if ($title && $description && $location && $salary && $category && $job_type && $deadline) {
        $stmt = $conn->prepare("UPDATE jobs SET title=?, description=?, location=?, salary=?, category=?, job_type=?, application_deadline=? WHERE id=? AND company_id=?");
        $stmt->bind_param("sssisssii", $title, $description, $location, $salary, $category, $job_type, $deadline, $id, $company_id);
        if ($stmt->execute()) {
            header("Location: company_dashboard.php");
            exit();
        } else {
            $error = "Gagal memperbarui data lowongan.";
        }
    } else {
        $error = "Semua field wajib diisi.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Lowongan</title>
    <link rel="stylesheet" href="../pelamar_kerja/apply_form.css">
</head>
<body>
    <div class="form-container">
        <h1>Edit Lowongan</h1>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="POST">
            <label>Judul Pekerjaan</label>
            <input type="text" name="title" value="<?= htmlspecialchars($job['title']) ?>" required>

            <label>Deskripsi</label>
            <textarea name="description" required><?= htmlspecialchars($job['description']) ?></textarea>

            <label>Lokasi</label>
            <input type="text" name="location" value="<?= htmlspecialchars($job['location'] ?? '') ?>" required>

            <label>Gaji</label>
            <input type="number" name="salary" value="<?= htmlspecialchars($job['salary']) ?>" required>

            <label>Kategori Pekerjaan</label>
            <input type="text" name="category" value="<?= htmlspecialchars($job['category']) ?>" required>

            <label>Jenis Pekerjaan</label>
            <input type="text" name="job_type" value="<?= htmlspecialchars($job['type']) ?>" required>

            <label>Tanggal Batas Lamaran</label>
            <input type="date" name="deadline" value="<?= htmlspecialchars($job['deadline']) ?>" required>

            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
