<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include '../conf/config.php';
// Statistik berita
date_default_timezone_set('Asia/Jakarta');
$jml_berita = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM berita"))[0];
$jml_draft = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM berita WHERE status='draft'"))[0];
$jml_published = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM berita WHERE status='published'"))[0];
$jml_rejected = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM berita WHERE status='rejected'"))[0];
$level = $_SESSION['level'];
$user_id = $_SESSION['user_id'];
// Filter berita
$where = '';
if ($level == 'wartawan') {
    $where = "WHERE b.id_pengirim='$user_id'";
}
if ($level == 'editor') {
    $where = "WHERE b.status='draft'";
}
$query = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, u.username FROM berita b LEFT JOIN kategori k ON b.id_kategori=k.id LEFT JOIN tb_users u ON b.id_pengirim=u.id $where ORDER BY b.created_at DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Dashboard</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<?php /* Selamat datang user */ ?>
<!-- Navbar, Sidebar, dan seluruh konten dashboard copy dari index.html -->
<!-- PASTE NAVBAR DAN SIDEBAR ADMINLTE DI SINI JIKA PERLU -->
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?= $jml_berita ?></h3>
              <p>Total Berita</p>
            </div>
            <div class="icon"><i class="fas fa-newspaper"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?= $jml_draft ?></h3>
              <p>Draft</p>
            </div>
            <div class="icon"><i class="fas fa-edit"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?= $jml_published ?></h3>
              <p>Published</p>
            </div>
            <div class="icon"><i class="fas fa-check"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3><?= $jml_rejected ?></h3>
              <p>Rejected</p>
            </div>
            <div class="icon"><i class="fas fa-times"></i></div>
          </div>
        </div>
      </div>
      <!-- Daftar berita lengkap -->
      <div class="card mt-4 shadow">
  <div class="card-header bg-primary d-flex align-items-center">
    <i class="fas fa-newspaper fa-lg mr-2"></i>
    <h3 class="card-title mb-0">Daftar Berita</h3>
    <span class="ml-2 text-white-50">&nbsp;Semua berita terbaru, lengkap dengan aksi!</span>
  </div>
  <div class="card-body">
    <?php if($level=='wartawan'): ?>
    <a href="../berita_form.php" class="btn btn-success mb-3"><i class="fas fa-plus"></i> Tambah Berita</a>
    <?php endif; ?>
    <div class="table-responsive">
      <table class="table table-hover table-striped align-middle">
        <thead class="thead-dark">
          <tr>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Pengirim</th>
            <th>Status</th>
            <th>Gambar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($query)): ?>
          <tr>
            <td><i class="far fa-file-alt text-info"></i> <b><?= htmlspecialchars($row['judul']) ?></b></td>
            <td><span class="badge badge-info"><i class="fas fa-tag"></i> <?= htmlspecialchars($row['nama_kategori']) ?></span></td>
            <td>
              <span class="d-flex align-items-center">
                <span class="avatar bg-secondary text-white rounded-circle mr-2" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;font-size:1rem;">
                  <i class="fas fa-user"></i>
                </span> <?= htmlspecialchars($row['username']) ?>
              </span>
            </td>
            <td>
              <?php
                $status = $row['status'];
                $badge = 'secondary';
                if ($status == 'published') $badge = 'success';
                elseif ($status == 'draft') $badge = 'warning';
                elseif ($status == 'rejected') $badge = 'danger';
              ?>
              <span class="badge badge-<?= $badge ?> text-uppercase"><i class="fas fa-circle"></i> <?= htmlspecialchars($status) ?></span>
            </td>
            <td><?php if($row['gambar']): ?><img src="../upload/<?= htmlspecialchars($row['gambar']) ?>" width="60" class="img-thumbnail shadow-sm"><?php else: ?><span class="text-muted">-</span><?php endif; ?></td>
            <td>
              <?php if($level=='wartawan' && $row['status']=='draft' && $row['id_pengirim']==$user_id): ?>
                <a href="../berita_form.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1"><i class="fas fa-edit"></i> Edit</a>
                <a href="../berita_hapus.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Hapus berita?')"><i class="fas fa-trash"></i> Hapus</a>
              <?php endif; ?>
              <?php if($level=='editor' && $row['status']=='draft'): ?>
                <a href="../berita_approval.php?id=<?= $row['id'] ?>&aksi=publish" class="btn btn-sm btn-success mb-1"><i class="fas fa-upload"></i> Publish</a>
                <a href="../berita_approval.php?id=<?= $row['id'] ?>&aksi=reject" class="btn btn-sm btn-danger mb-1"><i class="fas fa-times"></i> Reject</a>
              <?php endif; ?>
              <a href="../berita_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info mb-1"><i class="fas fa-eye"></i> Detail</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
    </div>
  </section>
</div>
</div>
<!-- JS -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
