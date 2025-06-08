<?php
session_start();
require_once("../umum/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi dan pengolahan form
    if (!isset($_POST['job_id'], $_POST['full_name'], $_POST['email'], $_POST['cover_letter'], $_FILES['cv_file'], $_POST['birth_date'], $_POST['phone_number'])) {
        die("Semua field wajib diisi.");
    }

    $job_id = intval($_POST['job_id']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $cover_letter = trim($_POST['cover_letter']);
    $birth_date = $_POST['birth_date'];         // Format YYYY-MM-DD dari input type=date
    $phone_number = trim($_POST['phone_number']);

    // Validasi file CV
    $cv_file = $_FILES['cv_file'];
    if ($cv_file['error'] !== UPLOAD_ERR_OK) {
        die("Gagal mengunggah file CV. Pastikan Anda memilih file PDF.");
    }

    $allowed_extensions = ['pdf'];
    $file_extension = strtolower(pathinfo($cv_file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        die("File CV harus berupa PDF.");
    }

    $upload_dir = "../uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $cv_path = $upload_dir . uniqid() . "-" . basename($cv_file['name']);

    if (!move_uploaded_file($cv_file['tmp_name'], $cv_path)) {
        die("Gagal menyimpan file CV.");
    }

    // Simpan ke database, termasuk birth_date dan phone_number
    $stmt = $conn->prepare("INSERT INTO job_applications (job_id, full_name, email, birth_date, phone_number, cv_path, cover_letter) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $job_id, $full_name, $email, $birth_date, $phone_number, $cv_path, $cover_letter);

    if ($stmt->execute()) {
        header("Location: apply_form.php?job_id=$job_id&success=1");
        exit;
    } else {
        die("Terjadi kesalahan saat menyimpan data lamaran.");
    }
}

if (!isset($_GET['job_id'])) {
    die("ID lowongan tidak ditemukan.");
}

$job_id = intval($_GET['job_id']);

// Ambil info lowongan
$stmt = $conn->prepare("SELECT jobs.title, companies.company_name FROM jobs JOIN companies ON jobs.company_id = companies.id WHERE jobs.id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    die("Lowongan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lamar Pekerjaan</title>
    <link rel="stylesheet" href="../pelamar_keja/apply_form.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="form-header">
        <div class="apply-container">
            <div class="form-header">
                <h2>Lamar: <?php echo htmlspecialchars($job['title']); ?> di <?php echo htmlspecialchars($job['company_name']); ?></h2>
            </div>

            <form action="apply_form.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">

                <label>Nama Lengkap:</label>
                <input type="text" name="full_name" required>

                <label>Email:</label>
                <input type="email" name="email" required>

                <label>Tanggal Lahir:</label>
                <input type="date" name="birth_date" required>

                <label>Nomor Telepon:</label>
                <input type="tel" name="phone_number" required pattern="[0-9+ -]{7,15}" title="Masukkan nomor telepon yang valid">

                <label>CV (PDF):</label>
                <input type="file" name="cv_file" accept=".pdf" required>

                <label>Surat Lamaran:</label>
                <textarea name="cover_letter" required></textarea>

                <button type="submit">Kirim Lamaran</button>
            </form>
        </div>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <script>
        Swal.fire({
            title: 'Lamaran berhasil dikirim!',
            text: 'Kami akan menghubungi Anda jika lolos seleksi.',
            icon: 'success',
            confirmButtonColor: '#e91e63',
            backdrop: `
                rgba(0,0,0,0.4)
                url("https://media.giphy.com/media/26ufdipQqU2lhNA4g/giphy.gif")
                center center
                no-repeat
            `
        }).then(() => {
            // Redirect ke halaman daftar lowongan setelah pop-up ditutup
            window.location.href = 'job_list.php';
        });
    </script>
    <?php endif; ?>
</body>
</html>
