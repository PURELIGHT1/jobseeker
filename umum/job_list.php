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

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$query = "
    SELECT jobs.*, companies.company_name AS company_name, companies.location, companies.logo 
    FROM jobs 
    INNER JOIN companies ON jobs.company_id = companies.id
    ORDER BY jobs.created_at DESC
";

$result = $conn->query($query);

if (!$result) {
    die("Gagal mengambil data lowongan: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Lowongan</title>
    <link rel="stylesheet" type="text/css" href="../perusahaan/job_list.css">
    <style>
      
    </style>
</head>
<body>
    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
        <a href="../umum/logout.php" class="logout">&larr; Logout</a>
    <?php } else { ?>
        <a href="../umum/login.php" class="logout" style="background-color: blue;">&larr; login</a>
    <?php } ?>
    

    <h2>Daftar Lowongan Pekerjaan</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="job-card">
                <img class="logo" src="../gambar/<?php echo htmlspecialchars($row['logo']); ?>" alt="Logo Perusahaan">

                <div class="job-info">
                    <h3><a href="detail.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h3>
                    <div class="company"><?php echo htmlspecialchars($row['company_name']); ?></div>
                    <div class="meta">
                        <span>📍 <?php echo htmlspecialchars($row['location']); ?></span>
                        <span>💰 <?php echo htmlspecialchars($row['salary']); ?></span>
                        <span>🗓 <?php echo htmlspecialchars(date("d M Y", strtotime($row['created_at']))); ?></span>
                    </div>
                    
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Tidak ada lowongan yang tersedia saat ini.</p>
    <?php endif; ?>
</body>
</html>
