<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function check_auth($role = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    if ($role && (!isset($_SESSION['role']) || $_SESSION['role'] !== $role)) {
        header("Location: ../logout.php");
        exit();
    }
}
?>
