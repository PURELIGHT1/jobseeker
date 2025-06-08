<?php
session_start();
require_once("../umum/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = intval($_POST['job_id']);
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $cover_letter = $_POST['cover_letter'];

    // Validasi file CV
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['application/pdf'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($_FILES['cv_file']['type'], $allowed_types)) {
            die("Format file harus PDF.");
        }

        if ($_FILES['cv_file']['size'] > $max_size) {
            die("Ukuran file maksimal 2MB.");
        }

        $cv_name = uniqid() . "_" . basename($_FILES['cv_file']['name']);
        $upload_dir = "../uploads/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $upload_path = $upload_dir . $cv_name;

        if (!move_uploaded_file($_FILES['cv_file']['tmp_name'], $upload_path)) {
            die("Gagal mengupload CV.");
        }
    } else {
        die("CV wajib diunggah.");
    }

    // Simpan lamaran ke database
    $stmt = $conn->prepare("INSERT INTO applications (job_id, full_name, email, cv_file, cover_letter, applied_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issss", $job_id, $full_name, $email, $cv_name, $cover_letter);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('✅ Lamaran berhasil dikirim!'); window.location.href='job_list.php';</script>";
    } else {
        echo "❌ Gagal mengirim lamaran.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Akses tidak valid.";
}
?>
