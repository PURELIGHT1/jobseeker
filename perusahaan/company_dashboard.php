<?php
session_start();
require_once("../umum/config.php");

// Cek apakah perusahaan sudah login
if (!isset($_SESSION['company_id'])) {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

// Ambil data pekerjaan milik perusahaan
$query = "SELECT * FROM jobs WHERE company_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Perusahaan</title>
    <link rel="stylesheet" href="../assets/umum/style.css">
</head>
<body>
    <h2>Dashboard Perusahaan</h2>
    <p>Selamat datang, 
        <?php 
        echo isset($_SESSION['company_name']) 
            ? htmlspecialchars($_SESSION['company_name']) 
            : 'Perusahaan'; 
        ?>!
    </p>

    <a href="create_job.php">+ Tambah Lowongan Baru</a>
    <h3>Daftar Lowongan Anda:</h3>

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
                    <td>
                        <?php 
                        echo isset($row['location']) 
                            ? htmlspecialchars($row['location']) 
                            : 'Tidak tersedia'; 
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['salary']); ?></td>
                    <td>
                        <a href="edit_job.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="delete_job.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin hapus lowongan ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Belum ada lowongan pekerjaan yang diposting.</p>
    <?php endif; ?>

    <br><a href="logout.php">Logout</a>
</body>
</html>
