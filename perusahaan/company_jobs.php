<?php
session_start();
require_once("../umum/config.php");

// Cek apakah perusahaan sudah login
if (!isset($_SESSION['company_id'])) {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

// Ambil semua lowongan milik perusahaan
$query = "SELECT * FROM jobs WHERE company_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lowongan Saya</title>
    <link rel="stylesheet" href="../assets/umum/style.css">
</head>
<body>
    <h2>Daftar Lowongan Anda</h2>
    <a href="company_dashboard.php">Kembali ke Dashboard</a><br><br>
    <a href="create_job.php">+ Tambah Lowongan Baru</a>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Judul</th>
                <th>Lokasi</th>
                <th>Gaji</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['salary']); ?></td>
                    <td>
                        <a href="edit_job.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="delete_job.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus lowongan ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Anda belum memiliki lowongan pekerjaan.</p>
    <?php endif; ?>
</body>
</html>
