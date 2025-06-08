<?php
session_start();

session_unset();

// Hancurkan session
session_destroy();

// Hapus cookie jika ada
setcookie('role', '', time() - 30 * 24 * 60 * 60);
setcookie('id_s', '', time() - 30 * 24 * 60 * 60);
setcookie('role', '', time() - 30 * 24 * 60 * 60);
setcookie('id_u', '', time() - 30 * 24 * 60 * 60);
setcookie('company_name', '', time() - 30 * 24 * 60 * 60);
setcookie('full_name', '', time() - 30 * 24 * 60 * 60);
setcookie('id_c', '', time() - 30 * 24 * 60 * 60);

// Redirect ke halaman login
header("Location: login.php");
exit();
?>
