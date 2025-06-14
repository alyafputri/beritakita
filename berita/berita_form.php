<?php
include_once 'conf/config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'wartawan') {
    header('Location: index.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch categories
$kategori = mysqli_query($koneksi, "SELECT * FROM tb_kategori ORDER BY nama_kategori");

// Edit news if ID is provided
$judul = $isi = $id_kategori = $gambar = '';
$edit = false;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $q = mysqli_query($koneksi, "SELECT * FROM berita WHERE id='$id' AND id_pengirim='$user_id'");
    if ($data = mysqli_fetch_assoc($q)) {
        $judul = $data['judul'];
        $isi = $data['isi'];
        $id_kategori = $data['id_kategori'];
        $gambar = $data['gambar'];
        $edit = true;
    }
}

// Process form submission
if (isset($_POST['simpan'])) {
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    $id_kategori = intval($_POST['id_kategori']);
    $gambar_name = $gambar;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'upload/' . $gambar_name);
    }
    if ($edit) {
        $sql = "UPDATE berita SET judul='$judul', isi='$isi', id_kategori='$id_kategori', gambar='$gambar_name' WHERE id='$id' AND id_pengirim='$user_id'";
    } else {
        $sql = "INSERT INTO berita (judul, isi, id_kategori, gambar, id_pengirim, status) VALUES ('$judul', '$isi', '$id_kategori', '$gambar_name', '$user_id', 'draft')";
    }
    if (mysqli_query($koneksi, $sql)) {
        header('Location: berita_list.php');
        exit;
    } else {
        $error = 'Gagal menyimpan berita.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit ? 'Edit' : 'Tambah' ?> Berita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <style>
        :root {
            --primary: #6C63FF;
            --secondary: #8E2DE2;
            --accent: #FFD166;
            --bg-gradient: linear-gradient(135deg, #E6E6FA 0%, #F0F0FF 100%);
            --card-bg: rgba(255, 255, 255, 0.85);
            --shadow: 0 12px 40px rgba(108, 99, 255, 0.2);
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
        }

        .container-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .news-card {
            background: var(--card-bg);
            border-radius: 2rem;
            box-shadow: var(--shadow);
            max-width: 800px;
            width: 100%;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 3rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 50px rgba(108, 99, 255, 0.25);
        }

        .card-header {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-top-left-radius: 1.8rem;
            border-top-right-radius: 1.8rem;
            padding: 2rem;
            margin: -3rem -3rem 2.5rem -3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(142, 45, 226, 0.2);
        }

        .card-header h3 {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        }

        .badge-status {
            background: var(--accent);
            color: var(--text-dark);
            font-weight: 600;
            padding: 0.7rem 1.5rem;
            border-radius: 1.2rem;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(255, 209, 102, 0.3);
        }

        .card-body {
            padding: 0;
        }

        .form-group {
            margin-bottom: 2.5rem;
        }

        .form-group label {
            font-weight: 600;
            font-size: 1.3rem;
            color: var(--primary);
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .form-control, .custom-select {
            border: 2px solid rgba(108, 99, 255, 0.2);
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            font-size: 1.2rem;
            background: rgba(255, 255, 255, 0.9);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus, .custom-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(108, 99, 255, 0.15);
            outline: none;
        }

        textarea.form-control {
            min-height: 180px;
            resize: vertical;
            line-height: 1.6;
        }

        .custom-file-input {
            border-radius: 1rem;
            cursor: pointer;
        }

        .custom-file-label {
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(108, 99, 255, 0.2);
            padding: 1rem 1.5rem;
            font-size: 1.2rem;
            color: var(--text-muted);
        }

        .custom-file-label::after {
            background: var(--primary);
            color: white;
            border-radius: 0 1rem 1rem 0;
            padding: 1rem 1.5rem;
        }

        .img-preview {
            max-width: 200px;
            border-radius: 1.2rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .img-preview:hover {
            transform: scale(1.07);
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 1rem;
            padding: 1rem 2.5rem;
            font-weight: 600;
            color: white;
            font-size: 1.3rem;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            transform: translateY(-3px);
        }

        .btn-secondary {
            background: linear-gradient(90deg, #FF9A8B, var(--secondary));
            border: none;
            border-radius: 1rem;
            padding: 1rem 2.5rem;
            font-weight: 600;
            color: white;
            font-size: 1.3rem;
        }

        .btn-secondary:hover {
            background: linear-gradient(90deg, var(--secondary), #FF9A8B);
            transform: translateY(-3px);
        }

        .alert {
            border-radius: 1rem;
            padding: 1.2rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 2rem;
        }

        .form-text {
            font-size: 1rem;
            color: var(--text-muted);
            margin-top: 0.7rem;
        }

        hr {
            border-top: 3px dashed var(--secondary);
            margin: 2.5rem 0;
        }

        .is-invalid {
            border-color: #DC3545 !important;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.2) !important;
        }

        @media (max-width: 768px) {
            .news-card {
                margin: 1rem;
                padding: 2rem;
            }

            .card-header h3 {
                font-size: 2rem;
            }

            .badge-status {
                font-size: 1rem;
                padding: 0.6rem 1.2rem;
            }

            .form-group label {
                font-size: 1.1rem;
            }

            .form-control, .custom-select, .custom-file-label {
                font-size: 1rem;
                padding: 0.8rem 1.2rem;
            }

            .btn-primary, .btn-secondary {
                font-size: 1.1rem;
                padding: 0.8rem 2rem;
            }

            .img-preview {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>
<div class="container-wrapper">
    <div class="news-card">
        <div class="card-header">
            <h3><i class="fas fa-edit mr-2"></i><?= $edit ? 'Edit' : 'Tambah' ?> Berita</h3>
            <span class="badge-status"><?= $edit ? 'Edit Mode' : 'Tambah Baru' ?></span>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            <form action="" method="post" enctype="multipart/form-data" autocomplete="off">
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Judul Berita</label>
                    <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($judul) ?>" required placeholder="Masukkan judul berita">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-tags"></i> Kategori</label>
                    <select name="id_kategori" id="id_kategori" class="form-control custom-select" required>
                        <option value="" disabled hidden <?= $id_kategori == '' ? 'selected' : '' ?>>Pilih Kategori</option>
                        <?php
                        // Reset pointer if previously fetched
                        if ($kategori instanceof mysqli_result && $kategori->num_rows > 0) mysqli_data_seek($kategori, 0);
                        while ($row = mysqli_fetch_assoc($kategori)): ?>
                            <option value="<?= $row['id'] ?>" <?= $id_kategori == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars($row['nama_kategori']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div id="kategoriError" class="form-text text-danger" style="display:none;"><i class="fas fa-exclamation-triangle"></i> Silakan pilih kategori!</div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Isi Berita</label>
                    <textarea name="isi" class="form-control" rows="6" required placeholder="Tulis isi berita di sini..."><?= htmlspecialchars($isi) ?></textarea>
                    <small class="form-text"><i class="fas fa-info-circle mr-1"></i>Gunakan bahasa yang jelas dan informatif.</small>
                </div>
                <hr>
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Gambar</label>
                    <?php if ($gambar): ?>
                        <div>
                            <img src="upload/<?= htmlspecialchars($gambar) ?>" class="img-preview">
                        </div>
                    <?php endif; ?>
                    <div class="custom-file">
                        <input type="file" name="gambar" class="custom-file-input" id="gambarInput">
                        <label class="custom-file-label" for="gambarInput">Pilih file gambar...</label>
                    </div>
                    <small class="form-text"><i class="fas fa-info-circle mr-1"></i>Format: jpg, png, max 2MB.</small>
                </div>
                <div class="d-flex justify-content-center gap-4 mt-4">
                    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Simpan</button>
                    <a href="berita_list.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<script>
$(function () {
    bsCustomFileInput.init();

    // Validasi kategori sebelum submit
    $('form').on('submit', function(e) {
        var kategori = $('#id_kategori').val();
        if (!kategori) {
            $('#kategoriError').show();
            $('#id_kategori').addClass('is-invalid');
            $('#id_kategori').focus();
            e.preventDefault();
            return false;
        } else {
            $('#kategoriError').hide();
            $('#id_kategori').removeClass('is-invalid');
        }
    });
    $('#id_kategori').on('change', function() {
        if ($(this).val()) {
            $('#kategoriError').hide();
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
</body>
</html>