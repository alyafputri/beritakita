<?php
include 'conf/config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}
$user_id = $_SESSION['user_id'];
// Ambil data user
$query = mysqli_query($koneksi, "SELECT * FROM tb_users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($query);
// Proses update profil
if (isset($_POST['update'])) {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi = trim($_POST['konfirmasi']);
    $update_query = "UPDATE tb_users SET nama_lengkap='$nama_lengkap', email='$email'";
    if (!empty($password_baru)) {
        if ($password_baru !== $konfirmasi) {
            $error = "Konfirmasi password tidak cocok.";
        } else {
            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $update_query .= ", password='$password_hash'";
        }
    }
    $update_query .= " WHERE id='$user_id'";
    if (!isset($error)) {
        $simpan = mysqli_query($koneksi, $update_query);
        if ($simpan) {
            $success = "Profil berhasil diperbarui.";
            // Refresh data user
            $query = mysqli_query($koneksi, "SELECT * FROM tb_users WHERE id='$user_id'");
            $user = mysqli_fetch_assoc($query);
        } else {
            $error = "Gagal memperbarui profil.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Edit</b> Profil
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-id-card"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password_baru" class="form-control" placeholder="Password Baru (opsional)">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="konfirmasi" class="form-control" placeholder="Konfirmasi Password Baru">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" name="update" class="btn btn-primary btn-block">Update Profil</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>
