<?php
include 'conf/config.php';
session_start();

// Fetch categories for filter
$kategori = mysqli_query($koneksi, "SELECT * FROM tb_kategori ORDER BY nama_kategori");

// Handle search
$search_results = [];
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;
$search_performed = ($search_keyword !== '' || $search_kategori > 0);
if ($search_performed) {
    $where = [];
    if ($search_keyword !== '') {
        $escaped = mysqli_real_escape_string($koneksi, $search_keyword);
        $where[] = "(b.judul LIKE '%$escaped%' OR b.isi LIKE '%$escaped%')";
    }
    if ($search_kategori > 0) {
        $where[] = "b.id_kategori = $search_kategori";
    }
    $where[] = "b.status = 'publish'";
    $where_sql = implode(' AND ', $where);
    $q_search = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, u.username FROM berita b LEFT JOIN tb_kategori k ON b.id_kategori=k.id LEFT JOIN tb_users u ON b.id_pengirim=u.id WHERE $where_sql ORDER BY b.created_at DESC LIMIT 12");
    while ($row = mysqli_fetch_assoc($q_search)) {
        $search_results[] = $row;
    }
}

if (!isset($_GET['id'])) {
    header('Location: berita_list.php');
    exit;
}
$id = intval($_GET['id']);
$q = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, u.username FROM berita b LEFT JOIN tb_kategori k ON b.id_kategori=k.id LEFT JOIN tb_users u ON b.id_pengirim=u.id WHERE b.id='$id'");
if (!$data = mysqli_fetch_assoc($q)) {
    echo '<div class="alert alert-danger">Berita tidak ditemukan.</div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Berita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <style>
        :root {
            --primary: #2E3192; /* Deep Blue */
            --secondary: #00C4CC; /* Cyan */
            --accent: #FF6B6B; /* Coral */
            --bg-gradient: linear-gradient(135deg, #0F1C3E 0%, #1A2A6C 100%); /* Futuristic gradient */
            --card-bg: rgba(15, 28, 62, 0.9); /* Dark transparent card */
            --shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            --text-light: #E0E7FF;
            --text-muted: #A0B0D0;
            --border-glow: 0 0 10px rgba(0, 196, 204, 0.5);
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            font-family: 'Inter', 'Poppins', sans-serif;
            color: var(--text-light);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            animation: bgPulse 15s infinite alternate;
        }

        @keyframes bgPulse {
            0% { background-position: 0% 0%; }
            100% { background-position: 100% 100%; }
        }

        .container-wrapper {
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .search-form {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: float 3s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .search-form .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-light);
            border-radius: 0.75rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .search-form .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(0, 196, 204, 0.3);
        }

        .search-form .btn-primary {
            background: var(--secondary);
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .search-form .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 196, 204, 0.4);
        }

        .glass-card {
            background: var(--card-bg);
            border-radius: 2rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 3rem;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }

        .card-header {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-top-left-radius: 1.8rem;
            border-top-right-radius: 1.8rem;
            padding: 2rem;
            margin: -3rem -3rem 2.5rem -3rem;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 196, 204, 0.2);
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 107, 107, 0.2) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
            z-index: 0;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .card-header h2 {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--text-light);
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }

        .card-body {
            padding: 0;
        }

        .meta-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .meta-item {
            font-size: 1.2rem;
            color: var(--text-light);
            line-height: 1.8;
        }

        .meta-item b {
            color: var(--secondary);
            font-weight: 600;
        }

        .img-preview {
            max-width: 100%;
            max-height: 400px;
            border-radius: 1.2rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
            margin: 2.5rem 0;
            display: block;
            margin-left: auto;
            margin-right: auto;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .img-preview:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 196, 204, 0.3);
        }

        .content {
            font-size: 1.3rem;
            line-height: 1.8;
            color: var(--text-light);
            margin-bottom: 3rem;
            white-space: pre-wrap;
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-back {
            background: linear-gradient(90deg, var(--accent), var(--secondary));
            border: none;
            border-radius: 1.2rem;
            padding: 1rem 2.5rem;
            font-family: 'Poppins', sans-serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-light);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: block;
            width: fit-content;
            margin-left: auto;
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 196, 204, 0.4);
        }

        .search-results h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--secondary);
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .search-results .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .search-results .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 196, 204, 0.3);
        }

        .search-results .card-title a {
            transition: color 0.3s ease;
        }

        .search-results .card-title a:hover {
            color: var(--accent);
        }

        .search-results .btn-outline-primary {
            border-color: var(--secondary);
            color: var(--secondary);
            transition: all 0.3s ease;
        }

        .search-results .btn-outline-primary:hover {
            background: var(--secondary);
            color: var(--text-light);
            border-color: var(--secondary);
        }

        @media (max-width: 768px) {
            .container-wrapper {
                padding: 1.5rem;
            }

            .glass-card {
                padding: 1.5rem;
            }

            .card-header h2 {
                font-size: 2rem;
            }

            .meta-container {
                grid-template-columns: 1fr;
                gap: 1rem;
                padding: 1rem;
            }

            .meta-item {
                font-size: 1rem;
            }

            .img-preview {
                max-height: 250px;
                margin: 1.5rem 0;
            }

            .content {
                font-size: 1.1rem;
                padding: 1.5rem;
                margin-bottom: 2rem;
            }

            .btn-back {
                font-size: 1.2rem;
                padding: 0.8rem 2rem;
            }

            .search-form {
                padding: 1.5rem;
            }

            .search-results .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container-wrapper">
    <!-- Search Form -->
    <form class="search-form mb-4" method="get" action="berita_detail.php">
        <div class="form-row align-items-end">
            <div class="col-md-5 mb-2">
                <label for="search" class="font-weight-bold" style="color: var(--text-light);">Cari Berita</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Kata kunci..." value="<?= htmlspecialchars($search_keyword) ?>">
            </div>
            <div class="col-md-4 mb-2">
                <label for="kategori" class="font-weight-bold" style="color: var(--text-light);">Kategori</label>
                <select class="form-control" id="kategori" name="kategori">
                    <option value="0">Semua Kategori</option>
                    <?php
                    mysqli_data_seek($kategori, 0); // Reset pointer
                    while ($row = mysqli_fetch_assoc($kategori)):
                    ?>
                        <option value="<?= $row['id'] ?>" <?= $search_kategori == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars($row['nama_kategori']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-2"></i>Cari</button>
            </div>
        </div>
        <!-- Keep detail page id in URL if present -->
        <?php if (isset($_GET['id'])): ?>
            <input type="hidden" name="id" value="<?= intval($_GET['id']) ?>">
        <?php endif; ?>
    </form>

    <div class="glass-card">
        <div class="card-header">
            <h2><i class="fas fa-newspaper mr-2"></i><?= htmlspecialchars($data['judul']) ?></h2>
        </div>
        <div class="card-body">
            <div class="meta-container">
                <div class="meta-item"><b>Kategori:</b> <?= htmlspecialchars($data['nama_kategori'] ?: 'Tidak ada kategori') ?></div>
                <div class="meta-item"><b>Penulis:</b> <?= htmlspecialchars($data['username']) ?></div>
                <div class="meta-item"><b>Status:</b> <?= htmlspecialchars(ucfirst($data['status'])) ?></div>
                <div class="meta-item"><b>Tanggal:</b> <?= htmlspecialchars(date('d F Y H:i', strtotime($data['created_at']))) ?> WIB</div>
            </div>
            <?php if ($data['gambar']): ?>
                <img src="upload/<?= htmlspecialchars($data['gambar']) ?>" class="img-preview" alt="Gambar Berita">
            <?php endif; ?>
            <div class="content"><?= nl2br(htmlspecialchars($data['isi'])) ?></div>
            <a href="berita_list.php" class="btn-back"><i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar</a>
        </div>
    </div>

    <!-- Search Results -->
    <?php if ($search_performed): ?>
        <div class="search-results mt-5">
            <h4 class="mb-4"><i class="fas fa-list mr-2"></i>Hasil Pencarian Berita</h4>
            <?php if (count($search_results) === 0): ?>
                <div class="alert alert-warning" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">Tidak ada berita ditemukan untuk pencarian Anda.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($search_results as $berita): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <?php if ($berita['gambar']): ?>
                                    <img src="upload/<?= htmlspecialchars($berita['gambar']) ?>" class="card-img-top" alt="Gambar Berita" style="height:180px; object-fit:cover; border-top-left-radius:1rem; border-top-right-radius:1rem;">
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title" style="font-family:'Poppins',sans-serif; font-weight:600; color:var(--secondary);">
                                        <a href="berita_detail.php?id=<?= $berita['id'] ?>" style="color:inherit; text-decoration:none;">
                                            <?= htmlspecialchars($berita['judul']) ?>
                                        </a>
                                    </h5>
                                    <div class="mb-2"><span class="badge badge-info"><i class="fas fa-tag"></i> <?= htmlspecialchars($berita['nama_kategori'] ?: 'Tanpa Kategori') ?></span></div>
                                    <div class="mb-2 text-muted" style="font-size:0.95rem;">
                                        <i class="fas fa-user mr-1"></i> <?= htmlspecialchars($berita['username']) ?>
                                         | 
                                        <i class="far fa-clock mr-1"></i> <?= date('d M Y', strtotime($berita['created_at'])) ?>
                                    </div>
                                    <div class="mb-2" style="color:var(--text-muted); font-size:1rem;">
                                        <?= htmlspecialchars(mb_strimwidth(strip_tags($berita['isi']), 0, 90, '...')) ?>
                                    </div>
                                    <a href="berita_detail.php?id=<?= $berita['id'] ?>" class="btn btn-outline-primary mt-auto"><i class="fas fa-arrow-right mr-1"></i>Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
