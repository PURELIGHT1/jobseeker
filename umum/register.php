<?php
session_start();
require_once("../umum/config.php");

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email        = trim($_POST['email'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $role         = $_POST['role'] ?? '';
    $fullname     = trim($_POST['fullname'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $name         = ($role === 'jobseeker') ? $fullname : $company_name;

    $phone     = trim($_POST['phone'] ?? '');
    $birthdate = $_POST['birthdate'] ?? null;
    $location  = $_POST['location'] ?? null;
    $logo      = $_FILES['logo']['name'] ?? null;

    if ($role && $email && $password && $name) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hashed, $role);
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;

                if ($role === 'jobseeker') {
                    $stmt2 = $conn->prepare("INSERT INTO job_seekers (user_id, full_name, phone, birthdate) VALUES (?, ?, ?, ?)");
                    $stmt2->bind_param("isss", $user_id, $fullname, $phone, $birthdate);
                    $stmt2->execute();
                } elseif ($role === 'company') {
                    $logo_path = null;
                    if ($logo && $_FILES['logo']['error'] === 0) {
                        $ext = pathinfo($logo, PATHINFO_EXTENSION);
                        $newName = uniqid() . '.' . $ext;
                        $uploadPath = '../uploads/' . $newName;
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                            $logo_path = $newName;
                        }
                    }

                    $stmt2 = $conn->prepare("INSERT INTO companies (user_id, company_name, location, logo) VALUES (?, ?, ?, ?)");
                    $stmt2->bind_param("isss", $user_id, $company_name, $location, $logo_path);
                    $stmt2->execute();
                }

                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;

                if ($role === 'jobseeker') {
                    header("Location: ../pelamar_kerja/jobseeker_dashboard.php");
                } else {
                    header("Location: ../perusahaan/company_dashboard.php");
                }
                exit;
            } else {
                $error = "Gagal menyimpan data user.";
            }
        }
    } else {
        $error = "Silakan lengkapi semua field yang dibutuhkan.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../umum/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="wrapper">
  <div class="login-container">
    <h2>Register</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Daftar Sebagai:</label>
        <select name="role" id="roleSelect" onchange="toggleRoleFields()" required>
            <option value="">--Pilih Role--</option>
            <option value="jobseeker">Pencari Kerja</option>
            <option value="company">Perusahaan</option>
        </select>

        <div id="formFields" style="display:none;">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <div id="jobseekerFields" style="display:none">
                <label>Nama Lengkap:</label>
                <input type="text" name="fullname">

                <label>Nomor HP:</label>
                <input type="text" name="phone">

                <label>Tanggal Lahir:</label>
                <input type="date" name="birthdate">
            </div>

            <div id="companyFields" style="display:none">
                <label>Nama Perusahaan:</label>
                <input type="text" name="company_name">

                <label>Lokasi Perusahaan:</label>
                <input type="text" name="location">

                <label>Logo Perusahaan:</label>
                <input type="file" name="logo" accept="image/*">
            </div>

            <button type="submit">Daftar</button>
        </div>
    </form>
  </div>
</div>

<?php if ($error): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Registrasi Gagal',
    text: '<?= htmlspecialchars($error, ENT_QUOTES) ?>',
    confirmButtonColor: '#d33'
});
</script>
<?php endif; ?>

<script>
function toggleRoleFields() {
    const role = document.getElementById("roleSelect").value;
    const formFields = document.getElementById("formFields");
    const jobseekerFields = document.getElementById("jobseekerFields");
    const companyFields = document.getElementById("companyFields");

    if (role) {
      formFields.style.display = 'block';
    } else {
      formFields.style.display = 'none';
    }
    jobseekerFields.style.display = (role === 'jobseeker') ? 'block' : 'none';
    companyFields.style.display = (role === 'company') ? 'block' : 'none';
}
</script>
</body>
</html>
