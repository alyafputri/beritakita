<?php
include 'conf/config.php';
// Proses form lupa password
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    if (empty($email)) {
        $error = "Email harus diisi.";
    } else {
        // Cek email di database
        $cek = mysqli_query($koneksi, "SELECT * FROM tb_users WHERE email='$email'");
        if (mysqli_num_rows($cek) == 1) {
            $user = mysqli_fetch_assoc($cek);
            // Generate token reset
            $token = bin2hex(random_bytes(16));
            // Simpan token ke database
            mysqli_query($koneksi, "UPDATE tb_users SET reset_token='$token' WHERE email='$email'");
            // Simulasi kirim link reset (tampilkan di halaman)
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
            $success = "Link reset password: <a href='$reset_link'>$reset_link</a>";
        } else {
            $error = "Email tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Lupa</b> Password
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
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <a href="index.php" class="text-center">Login</a>
                    </div>
                    <div class="col-4">
                        <button type="submit" name="submit" class="btn btn-primary btn-block">Kirim</button>
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
