<?php
session_start();
require_once("../umum/config.php");
$loged_in = false;
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    $loged_in = true;
    $id_u = $_SESSION['id_u'];
    $loged_in = isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : false;
    $role = isset($_COOKIE['role']) ? $_COOKIE['role'] : "Cookies Habis";
    $id_umum = isset($_COOKIE['is_s']) ? $_COOKIE['id_s'] : (isset($_COOKIE['id_c']) ? $_COOKIE['id_c'] : "Cookies Habis");
    $nama = isset($_COOKIE['full_name']) 
            ? $_COOKIE['full_name'] 
            : (isset($_COOKIE['company_name']) 
                ? $_COOKIE['company_name'] 
                : "Cookies Habis");


} elseif (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_COOKIE['id_s'])) {
    // Re-set session dari cookie
    $_SESSION['id_u'] = $_COOKIE['id_u'];
    $_SESSION['full_name'] = $_COOKIE['full_name'];
    $_SESSION['id_s'] = $_COOKIE['id_s'];
    $_SESSION['role'] = $_COOKIE['role'];
    $_SESSION['logged_in'] = true;
    $loged_in = true;
} elseif (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_COOKIE['id_c'])) {
    $_SESSION['id_u'] = $_COOKIE['id_u'];
    $_SESSION['company_name'] = $_COOKIE['company_name'];
    $_SESSION['id_c'] = $_COOKIE['id_c'];
    $_SESSION['role'] = $_COOKIE['role'];
    $_SESSION['logged_in'] = true;
    $loged_in = true;

}

$lowongan = null;
if(isset($_GET['id'])){
    $id = $_GET['id'];

    $query = "SELECT j.*, c.* 
            FROM jobs j
            INNER JOIN companies c on j.company_id = c.id
            WHERE j.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id); // "i" untuk integer
    $stmt->execute();
    $result = $stmt->get_result();
    $lowongan = $result->fetch_assoc();


} else {
    die("Lowongan tidak ditemukan.");
}
// Fungsi format list
function formatList($text) {
    $lines = preg_split('/\d+\.\s*/', $text, -1, PREG_SPLIT_NO_EMPTY);
    $output = "<ol>";
    foreach ($lines as $line) {
        $output .= "<li>" . htmlspecialchars(trim($line)) . "</li>";
    }
    $output .= "</ol>";
    return $output;
}

$tgl = $lowongan['created_at'];
$hariIni = date("Y-m-d");
$selisih = floor((strtotime($hariIni) - strtotime($tgl)) / (60*60*24));
if ($selisih == 0) $label = "Diposting hari ini";
elseif ($selisih == 1) $label = "Diposting 1 hari yang lalu";
elseif ($selisih < 30) $label = "Diposting $selisih hari yang lalu";
else $label = "Diposting lebih dari sebulan yang lalu";


$hasApplied = false;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Detail Lowongan - <?= htmlspecialchars($lowongan['title']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { display: flex; gap: 40px; }
        .job-detail, .company-detail { border: 1px solid #ccc; padding: 20px; border-radius: 6px; }
        .job-detail { flex: 3; }
        .company-detail { flex: 1; background-color: #f9f9f9; }
        img.logo { max-width: 200px; margin-bottom: 15px; }
        h2, h3 { margin: 10px 0; }
        .btn-apply { background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 15px 0; }
        .btn-apply:hover { background-color: #0056b3; }
        ol { padding-left: 20px; }
    </style>
</head>
<body>
    <h1>Detail Lowongan Pekerjaan</h1>

    <div class="container">
        <div class="job-detail">
        <p>🗓 <?= $label ?></p>
        <?php if ($hasApplied) { ?>
            <button disabled class="btn_apply" style="background-color: #DDDDDD;">Anda sudah Melamar di pekerjaan ini</button>
        <?php } elseif ($loged_in && $role == 'company') { ?>
            <p></p>
        <?php } else { ?>
            <a href="../pelamar_kerja/?i<?= urlencode($id) ?>" class="btn-apply">Melamar</a>
        <?php } ?>


            <h2><?= htmlspecialchars($lowongan['title']) ?></h2>
            <h3><?= htmlspecialchars($lowongan['company_name']) ?></h3>
            <p>📍 <?= htmlspecialchars($lowongan['location']) ?></p>
            <p>💰 Gaji: <?= htmlspecialchars($lowongan['salary']) ?></p>
            <p>🗓 <?= $label ?></p>
            
            <h4>Deskripsi Pekerjaan:</h4>
            <?= $lowongan['description'] ?>

            <h4>Syarat Pekerjaan:</h4>
            <?= formatList($lowongan['requirements']) ?>

            <h4>Benefit:</h4>
            <?= formatList($lowongan['benefits']) ?>
        </div>

        <div class="company-detail">
            <img src="../gambar/<?= $lowongan['logo'] ?>" alt="Logo <?=$lowongan['company_name']?>" class="logo" />
            <h3><?= $lowongan['company_name'] ?></h3>
            <p><strong>Jenis Perusahaan:</strong> <?= $lowongan['company_type'] ?></p>
            <p><strong>Jumlah Pegawai:</strong> <?= $lowongan['employees'] ?></p>
            <p><strong>Deskripsi:</strong><br /><?=$lowongan['company_description']?></p>
            <p><a href="<?=$lowongan['company_website']?>" target="_blank">Selengkapnya tentang perusahaan</a></p>
        </div>
    </div>
</body>
</html>
