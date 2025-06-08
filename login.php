<?php
session_start();
require_once("../umum/config.php");


if (isset($_SESSION['id_u'])) {
    header("Location: ../umum/job_list.php");
    exit();
}

$email = '';
$password = '';
$role = '';
$expire = 0; // hanya selama session

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = '';
    if (isset($_POST['remember'])) {  // if remember me is checked
		$remember = $_POST['remember'];
	}

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $_SESSION['id_u'] = $data['id'];
        $_SESSION['role'] = $data['role'];

        if($data['role'] == 'jobseeker') {
            $qry = "SELECT * FROM job_seekers WHERE user_id = {$data['id']} LIMIT 1";
            $result = mysqli_query($conn, $qry);
            $hasil = mysqli_fetch_assoc($result);

            $_SESSION['id_s'] = $hasil['id'];
            $_SESSION['full_name'] = $hasil['full_name'];
            $_SESSION['logged_in'] = true;

            setcookie('id_u', $data['id'], $expire);
            setcookie('role', $data['role'], $expire);
            setcookie('id_s', $hasil['id'], $expire);
            setcookie('full_name', $hasil['full_name'], $expire);
            header("Location: ../umum/job_list.php?login=success");
        } elseif ($data['role'] == 'company') {
            $qry = "SELECT * FROM companies WHERE user_id = {$data['id']} LIMIT 1";
            $result = mysqli_query($conn, $qry);
            $hasil = mysqli_fetch_assoc($result);

            $_SESSION['id_c'] = $hasil['id'];
            $_SESSION['company_name'] = $hasil['company_name'];
            $_SESSION['logged_in'] = true;
            setcookie('id_u', $data['id'], $expire);
            setcookie('role', $data['role'], $expire);
            setcookie('id_c', $hasil['id'], $expire);
            setcookie('company_name', $hasil['company_name'], $expire);
            header("Location: ../perusahaan/job_list.php?login=success");
        } else {
            $error = "Role tidak dikenali.";
        }

        exit();
    } else {
        $error = "Email atau Password salah.";
    }
}

$error = !empty($error) ? htmlspecialchars($error) : '';

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../umum/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="post" autocomplete="off">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <div style="margin-top: 10px;">
            <a href="../umum/job_list.php" class="btn-link">Lihat Daftar Lowongan</a>
        </div>
    </div>

    <?php if (!empty($error)): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: <?= json_encode($error) ?>,
            confirmButtonColor: '#d33'
        });
    </script>
    <?php endif; ?>
</body>
</html>
