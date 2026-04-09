<?php
$id = (int) $_GET['id'];

$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT p.*, dp.alat_id, dp.jumlah
    FROM peminjaman p
    LEFT JOIN detail_peminjam dp ON p.id = dp.peminjaman_id
    WHERE p.id = $id
"));
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = (int) $_GET['id'];
    $alat_id_baru = $_POST['alat_id'];
    $jumlah_baru = (int) $_POST['jumlah'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    // Validasi
    if (empty($tanggal_pinjam) || empty($tanggal_kembali) || empty($alat_id_baru)) {
        $_SESSION['error'] = "Semua field harus diisi!";
        header("Location: edit_peminjaman.php?id=$id");
        exit;
    }

    if ($tanggal_kembali < $tanggal_pinjam) {
        $_SESSION['error'] = "Tanggal kembali tidak valid!";
        header("Location: edit_peminjaman.php?id=$id");
        exit;
    }

    // 🔥 Ambil data lama
    $old = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT alat_id, jumlah 
        FROM detail_peminjam 
        WHERE peminjaman_id = $id
    "));

    mysqli_begin_transaction($conn);

    try {

        // 🔥 1. Kembalikan stok lama
        mysqli_query($conn, "
            UPDATE alat 
            SET stok = stok + {$old['jumlah']}
            WHERE id = {$old['alat_id']}
        ");

        // 🔥 2. Cek stok baru
        $cek = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT stok FROM alat WHERE id = '$alat_id_baru'
        "));

        if (!$cek || $cek['stok'] < $jumlah_baru) {
            throw new Exception("Stok tidak mencukupi!");
        }

        // 🔥 3. Update peminjaman
        mysqli_query($conn, "
            UPDATE peminjaman SET
                tanggal_pinjam = '$tanggal_pinjam',
                tanggal_kembali = '$tanggal_kembali'
            WHERE id = $id
        ");

        // 🔥 4. Update detail
        mysqli_query($conn, "
            UPDATE detail_peminjam SET
                alat_id = '$alat_id_baru',
                jumlah = '$jumlah_baru'
            WHERE peminjaman_id = $id
        ");

        // 🔥 5. Kurangi stok baru
        mysqli_query($conn, "
            UPDATE alat 
            SET stok = stok - $jumlah_baru
            WHERE id = '$alat_id_baru'
        ");

        mysqli_commit($conn);

        $_SESSION['success'] = "Data berhasil diupdate!";
        header("Location: edit_peminjaman.php?id=$id");
        exit;
    } catch (Exception $e) {

        mysqli_rollback($conn);

        $_SESSION['error'] = $e->getMessage();
        header("Location: edit_peminjaman.php?id=$id");
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
                        <h5>Ubah Data Peminjaman</h5>
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
                                        <option value="<?= $row['id'] ?>" <?= ($row['id'] == $data['alat_id']) ? 'selected' : '' ?>><?= $row['nama_alat'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah yang dipinjam</label>
                                <input type="number" class="form-control" name="jumlah" id="jumlah" required value="<?= $data['jumlah'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                                <input type="date" class="form-control" name="tanggal_pinjam" id="tanggal_pinjam" required value="<?= $data['tanggal_pinjam'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                                <input type="date" class="form-control" name="tanggal_kembali" id="tanggal_kembali" required value="<?= $data['tanggal_kembali'] ?>">
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>