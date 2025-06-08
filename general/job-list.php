<?php
session_start();
$url = [
    'login' => '../login.php',
    'logout' => '../logout.php',
    'style' => '../style/job_list.css',
    'config' => '../config/config.php',
    'image' => '../gambar', 
    'detail' => function ($role, $id) {
        echo($role);
        echo($id);
        if($role != "company"){
            return "../pelamar/job-detail.php?id=" . urlencode($id);
        }else{
            return "../perusahaan/job-detail.php?id=" . urlencode($id);
        }
    }
];
require_once($url['config']);



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

$query = "";
if($role != "company"){
    $query = "
        SELECT 
                j.id as job_id,
                j.title, 
                c.company_name, 
                c.location, 
                c.logo,
                j.salary, 
                j.created_at
        FROM jobs j
        INNER JOIN companies c ON j.company_id = c.id
        ORDER BY j.created_at DESC
    ";
}else{
    $query = "
        SELECT 
            j.id as job_id,
            j.title, 
            c.company_name, 
            c.location, 
            c.logo,
            j.salary, 
            j.created_at, 
            COUNT(ja.id) AS total_applications 
        FROM jobs j 
        INNER JOIN companies c ON j.company_id = c.id 
        LEFT JOIN job_applications ja ON j.id = ja.job_id 
        WHERE c.user_id = ? 
        GROUP BY j.id, j.title, c.company_name, c.location, j.salary, j.created_at 
        ORDER BY j.created_at DESC
    ";
}
$stmt = $conn->prepare($query);
if($role == "company"){
    $stmt->bind_param("i", $id_u);
}
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Gagal mengambil data lowongan: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Lowongan</title>
    <link rel="stylesheet" type="text/css" href="<?= $url['style'] ?>">
    <style>
      
    </style>
</head>
<body>
    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
        <a href="<?= $url['logout'] ?>" class="logout">&larr; Logout</a>
    <?php } else { ?>
        <a href="<?= $url['login'] ?>" class="logout" style="background-color: blue;">&larr; login</a>
    <?php } ?>

    <h2>Daftar Lowongan Pekerjaan</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): 
            $detailUrl = $url['detail']($role, $row['job_id']);
        ?>
            <div class="job-card" onclick="location.href='<?= $detailUrl ?>'" style="cursor:pointer;">
                <img class="logo" src="<?= $url['image'] . '/' . $row['logo'] ?>" alt="Logo Perusahaan">

                <div class="job-info">
                    <h3 class="title"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <div class="company"><?php echo htmlspecialchars($row['company_name']); ?></div>
                    <div class="meta">
                        <span>📍 <?php echo htmlspecialchars($row['location']); ?></span>
                        <span>💰 <?php echo htmlspecialchars($row['salary']); ?></span>
                        <span>🗓 <?php echo htmlspecialchars(date("d M Y", strtotime($row['created_at']))); ?></span>
                        <?php if ($role == "company" && $row['total_applications'] > 0): ?>
                            <span>📍 <?php echo htmlspecialchars($row['total_applications']); ?> orang</span> 
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Tidak ada lowongan yang tersedia saat ini.</p>
    <?php endif; ?>
</body>
</html>
