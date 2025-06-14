<?php
include_once 'conf/config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
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
$query = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, u.username FROM berita b LEFT JOIN tb_kategori k ON b.id_kategori=k.id LEFT JOIN tb_users u ON b.id_pengirim=u.id $where ORDER BY b.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Berita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <style>
        :root {
            --primary: #6B46C1;
            --secondary: #9B59B6;
            --accent: #00C4CC;
            --bg-gradient: linear-gradient(135deg, #E6E6FA 0%, #D3D3D3 100%);
            --card-bg: rgba(255, 255, 255, 0.9);
            --shadow: 0 10px 30px rgba(107, 70, 193, 0.15);
            --text-dark: #2D3748;
            --text-muted: #718096;
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            font-family: 'Inter', 'Poppins', sans-serif;
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            letter-spacing: 0.01em;
        }

        .container-wrapper {
            padding: 3rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .glass-card {
            background: var(--card-bg);
            border-radius: 2rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 2.5rem;
            width: 100%;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            padding: 2rem;
            margin: -2.5rem -2.5rem 2rem -2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(155, 89, 182, 0.2);
        }

        .card-header h2 {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-add {
            background: linear-gradient(90deg, #00C4CC, #1ABC9C);
            border: none;
            border-radius: 1.2rem;
            padding: 1rem 2.5rem;
            font-family: 'Poppins', sans-serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 196, 204, 0.3);
            transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-add:hover {
            background: linear-gradient(90deg, #1ABC9C, #00C4CC);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 18px rgba(0, 196, 204, 0.4);
        }

        .table-responsive {
            border-radius: 1.2rem;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin-bottom: 0;
            font-size: 1.2rem;
            width: 100%;
        }

        .table thead th {
            background: linear-gradient(90deg, #F8F9FA, #E9ECEF);
            color: var(--text-dark);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            border: none;
            padding: 1.5rem;
            text-align: center;
            font-size: 1.3rem;
            white-space: nowrap;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background: rgba(107, 70, 193, 0.05);
        }

        .table td {
            vertical-align: middle;
            padding: 1.5rem;
            text-align: center;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            font-size: 1.15rem;
            white-space: nowrap;
        }

        .img-preview {
            max-width: 120px;
            border-radius: 0.75rem;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .img-preview:hover {
            transform: scale(1.1);
        }

        .btn-action {
            border-radius: 1rem;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 500;
            margin: 0.3rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-warning {
            background: linear-gradient(90deg, #F1C40F, #FFD700);
            border: none;
            color: white;
        }

        .btn-danger {
            background: linear-gradient(90deg, #E74C3C, #C0392B);
            border: none;
            color: white;
        }

        .btn-success {
            background: linear-gradient(90deg, #27AE60, #2ECC71);
            border: none;
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .badge-status {
            font-size: 1rem;
            padding: 0.6em 1.2em;
            border-radius: 0.9rem;
            font-weight: 500;
        }

        .badge-draft {
            background: #E9ECEF;
            color: #6C757D;
        }

        .badge-published {
            background: #D4EDDA;
            color: #155724;
        }

        .badge-rejected {
            background: #F8D7DA;
            color: #721C24;
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

            .btn-add {
                font-size: 1.2rem;
                padding: 0.8rem 2rem;
            }

            .table {
                font-size: 1rem;
            }

            .table td, .table th {
                padding: 1rem;
                font-size: 1rem;
            }

            .btn-action {
                font-size: 0.95rem;
                padding: 0.6rem 1.2rem;
            }

            .img-preview {
                max-width: 80px;
            }
        }
    </style>
</head>
<body>
<div class="container-wrapper">
    <div class="glass-card">
        <div class="card-header">
            <h2><i class="fas fa-newspaper mr-2"></i>Daftar Berita</h2>
            <?php if ($level == 'wartawan'): ?>
                <a href="berita_form.php" class="btn btn-add"><i class="fas fa-plus mr-2"></i>Tambah Berita</a>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
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
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kategori'] ?: 'Tidak ada kategori') ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <span class="badge-status badge-<?= $row['status'] == 'draft' ? 'draft' : ($row['status'] == 'published' ? 'published' : 'rejected') ?>">
                                <?= htmlspecialchars(ucfirst($row['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['gambar']): ?>
                                <img src="upload/<?= htmlspecialchars($row['gambar']) ?>" class="img-preview" alt="Gambar Berita">
                            <?php else: ?>
                                <span class="text-muted">Tidak ada gambar</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($level == 'wartawan' && $row['status'] == 'draft' && $row['id_pengirim'] == $user_id): ?>
                                <a href="berita_form.php?id=<?= $row['id'] ?>" class="btn btn-action btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                <a href="berita_hapus.php?id=<?= $row['id'] ?>" class="btn btn-action btn-danger" onclick="return confirm('Hapus berita?')"><i class="fas fa-trash"></i> Hapus</a>
                            <?php endif; ?>
                            <?php if ($level == 'editor' && $row['status'] == 'draft'): ?>
                                <a href="berita_approval.php?id=<?= $row['id'] ?>&aksi=publish" class="btn btn-action btn-success"><i class="fas fa-check"></i> Publish</a>
                                <a href="berita_approval.php?id=<?= $row['id'] ?>&aksi=reject" class="btn btn-action btn-danger"><i class="fas fa-times"></i> Tolak</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($query) == 0): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada berita.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>