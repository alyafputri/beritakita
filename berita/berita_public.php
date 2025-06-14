<?php
include 'conf/config.php';
// Ambil 1 berita headline
$headlineQ = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, u.username FROM tb_berita b LEFT JOIN tb_kategori k ON b.id_kategori=k.id LEFT JOIN tb_users u ON b.id_user=u.id WHERE b.status='publish' ORDER BY b.created_at DESC LIMIT 1");
$headline = mysqli_fetch_assoc($headlineQ);

// Ambil 3 berita utama lain
$utamaQ = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, u.username FROM tb_berita b LEFT JOIN tb_kategori k ON b.id_kategori=k.id LEFT JOIN tb_users u ON b.id_user=u.id WHERE b.status='publish' AND b.id != '{$headline['id']}' ORDER BY b.created_at DESC LIMIT 3");

// Ambil 9 berita lain
$lainQ = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, u.username FROM tb_berita b LEFT JOIN tb_kategori k ON b.id_kategori=k.id LEFT JOIN tb_users u ON b.id_user=u.id WHERE b.status='publish' AND b.id != '{$headline['id']}' ORDER BY b.created_at DESC LIMIT 9 OFFSET 3");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Publik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <style>
        .headline-img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 1.2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }
        .headline-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2D3748;
            margin-top: 1rem;
        }
        .headline-meta {
            color: #6C757D;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        .headline-summary {
            font-size: 1.15rem;
            margin-bottom: 1.2rem;
        }
        .card-utama {
            height: 100%;
            border-radius: 1rem;
            box-shadow: 0 3px 14px rgba(0,0,0,0.07);
        }
        .card-utama-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 1rem 1rem 0 0;
        }
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .card-text {
            font-size: 0.97rem;
        }
        .card-meta {
            color: #888;
            font-size: 0.93rem;
        }
        @media (max-width: 768px) {
            .headline-title { font-size: 1.3rem; }
            .headline-img { max-height: 220px; }
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <!-- Headline -->
    <?php if ($headline): ?>
    <div class="row mb-4">
        <div class="col-md-8">
            <?php if ($headline['gambar']): ?>
                <img src="upload/<?= htmlspecialchars($headline['gambar']) ?>" class="headline-img mb-3" alt="<?= htmlspecialchars($headline['judul']) ?>">
            <?php endif; ?>
            <div class="headline-title"><?= htmlspecialchars($headline['judul']) ?></div>
            <div class="headline-meta">
                <i class="fas fa-folder"></i> <?= htmlspecialchars($headline['nama_kategori']) ?> &nbsp; | &nbsp;
                <i class="fas fa-user"></i> <?= htmlspecialchars($headline['username']) ?> &nbsp; | &nbsp;
                <i class="fas fa-clock"></i> <?= date('d M Y', strtotime($headline['created_at'])) ?>
            </div>
            <div class="headline-summary">
                <?= nl2br(htmlspecialchars(substr(strip_tags($headline['isi']),0,220))) ?>...
            </div>
            <a href="berita_detail.php?id=<?= $headline['id'] ?>" class="btn btn-primary btn-lg"><i class="fas fa-book-open mr-2"></i>Baca Selengkapnya</a>
        </div>
        <div class="col-md-4">
            <h5 class="mb-3">Berita Utama Lainnya</h5>
            <?php while($row = mysqli_fetch_assoc($utamaQ)): ?>
            <div class="card card-utama mb-3">
                <?php if($row['gambar']): ?>
                    <img src="upload/<?= htmlspecialchars($row['gambar']) ?>" class="card-utama-img" alt="<?= htmlspecialchars($row['judul']) ?>">
                <?php endif; ?>
                <div class="card-body pb-2">
                    <a href="berita_detail.php?id=<?= $row['id'] ?>" class="card-title d-block mb-1"><?= htmlspecialchars($row['judul']) ?></a>
                    <div class="card-meta mb-1"><i class="fas fa-folder"></i> <?= htmlspecialchars($row['nama_kategori']) ?></div>
                    <div class="card-text text-truncate"><?= nl2br(htmlspecialchars(substr(strip_tags($row['isi']),0,70))) ?>...</div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
    <!-- Berita Grid -->
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($lainQ)): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <?php if($row['gambar']): ?>
                    <img src="upload/<?= htmlspecialchars($row['gambar']) ?>" class="card-img-top" style="height:170px;object-fit:cover;">
                <?php endif; ?>
                <div class="card-body">
                    <a href="berita_detail.php?id=<?= $row['id'] ?>" class="card-title mb-2 d-block"><?= htmlspecialchars($row['judul']) ?></a>
                    <div class="card-meta mb-1"><i class="fas fa-folder"></i> <?= htmlspecialchars($row['nama_kategori']) ?> | <i class="fas fa-user"></i> <?= htmlspecialchars($row['username']) ?></div>
                    <div class="card-text mb-2"><?= nl2br(htmlspecialchars(substr(strip_tags($row['isi']),0,90))) ?>...</div>
                    <a href="berita_detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-book-reader mr-1"></i>Baca</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($lainQ) == 0): ?>
        <div class="col-12 text-center text-muted">Belum ada berita lain.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
