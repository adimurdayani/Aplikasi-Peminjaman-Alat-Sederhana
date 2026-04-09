<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $alat_id = $_POST['alat_id'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $jumlah = $_POST['jumlah']; // sementara 1

    // Validasi dasar
    if (empty($tanggal_pinjam) || empty($tanggal_kembali) || empty($alat_id)) {
        $_SESSION['error'] = "Semua field harus diisi!";
        header("Location: tambah_peminjaman.php");
        exit;
    }

    if ($tanggal_kembali < $tanggal_pinjam) {
        $_SESSION['error'] = "Tanggal kembali tidak boleh sebelum tanggal pinjam!";
        header("Location: tambah_peminjaman.php");
        exit;
    }

    // 🔥 CEK STOK
    $cek = mysqli_query($conn, "SELECT stok FROM alat WHERE id = '$alat_id'");
    $data = mysqli_fetch_assoc($cek);

    if (!$data || $data['stok'] < $jumlah) {
        $_SESSION['error'] = "Stok alat tidak mencukupi!";
        header("Location: tambah_peminjaman.php");
        exit;
    }

    // 🔥 TRANSACTION START
    mysqli_begin_transaction($conn);

    try {

        // 1. Insert peminjaman
        mysqli_query($conn, "
            INSERT INTO peminjaman (user_id, tanggal_pinjam, tanggal_kembali, status) 
            VALUES ('$user_id', '$tanggal_pinjam', '$tanggal_kembali', 'dipinjam')
        ");

        $peminjaman_id = mysqli_insert_id($conn);

        // 2. Insert detail
        mysqli_query($conn, "
            INSERT INTO detail_peminjam (peminjaman_id, alat_id, jumlah)
            VALUES ('$peminjaman_id', '$alat_id', '$jumlah')
        ");

        // 3. 🔥 KURANGI STOK
        mysqli_query($conn, "
            UPDATE alat 
            SET stok = stok - $jumlah
            WHERE id = '$alat_id'
        ");

        // 🔥 COMMIT
        mysqli_commit($conn);

        $_SESSION['success'] = "Peminjaman berhasil & stok diperbarui!";
        header("Location: tambah_peminjaman.php");
        exit;
    } catch (Exception $e) {

        // 🔥 ROLLBACK jika gagal
        mysqli_rollback($conn);

        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: tambah_peminjaman.php");
        exit;
    }
}
$alats = mysqli_query($conn, "
    SELECT
        a.id,
        a.nama_alat,
        a.stok,
        COALESCE(SUM(dp.jumlah), 0) as dipinjam,
        (a.stok - COALESCE(SUM(dp.jumlah), 0)) as tersedia
    FROM alat a
    LEFT JOIN detail_peminjam dp ON a.id = dp.alat_id
    LEFT JOIN peminjaman p ON dp.peminjaman_id = p.id AND p.status = 'dipinjam'
    GROUP BY a.id
");
?>
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Peminjaman</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Ajukan Peminjaman</h5>
                        <a href="peminjaman.php" class="btn btn-secondary mb-0"> Kembali</a>
                    </div>
                    <div class="card-body">

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?= $_SESSION['success']; ?>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['error']; ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="alat_id" class="form-label">Tanggal Pinjam</label>
                                <select name="alat_id" id="alat_id" class="form-control">
                                    <option value="">Pilih</option>
                                    <?php while ($row = mysqli_fetch_assoc($alats)): ?>
                                        <option value="<?= $row['id'] ?>" <?= ($row['tersedia'] <= 0) ? 'disabled' : '' ?>><?= $row['nama_alat'] ?> (Tersedia: <?= $row['tersedia'] ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah yang dipinjam</label>
                                <input type="number" class="form-control" name="jumlah" id="jumlah" required>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                                <input type="date" class="form-control" name="tanggal_pinjam" id="tanggal_pinjam" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                                <input type="date" class="form-control" name="tanggal_kembali" id="tanggal_kembali" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>