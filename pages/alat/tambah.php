<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_alat = mysqli_real_escape_string($conn, $_POST['nama_alat']);
    $stok = (int) $_POST['stok'];

    // Validasi sederhana
    if (empty($nama_alat) || $stok < 0) {
        $_SESSION['error'] = "Data tidak valid!";
        header("Location: tambah_alat.php");
        exit;
    }

    // Insert ke database
    $query = mysqli_query($conn, "INSERT INTO alat (nama_alat, stok) VALUES ('$nama_alat', '$stok')");

    if ($query) {
        $_SESSION['success'] = "Data alat berhasil ditambahkan!";
        header("Location: alat.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan data!";
        header("Location: tambah_alat.php");
        exit;
    }
}
?>
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="alat.php">Alat</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Alat</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Tambah Alat</h5>
                        <a href="peminjaman.php" class="btn btn-secondary mb-0"> Kembali</a>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['error']; ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="nama_alat" class="form-label">Nama Alat</label>
                                <input type="text" class="form-control" name="nama_alat" id="nama_alat" placeholder="nama alat" required>
                            </div>
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok" id="stok" placeholder="stok" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>