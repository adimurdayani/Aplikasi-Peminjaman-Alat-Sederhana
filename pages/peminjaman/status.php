<?php
$id = (int) $_GET['id'];

// ambil data peminjaman
$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM peminjaman WHERE id = $id
"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $status = $_POST['status'];

    // validasi
    if (empty($status)) {
        $_SESSION['error'] = "Status harus dipilih!";
        header("Location: pengembalian.php?id=$id");
        exit;
    }

    // 🔥 Ambil detail alat
    $details = mysqli_query($conn, "
        SELECT alat_id, jumlah 
        FROM detail_peminjam 
        WHERE peminjaman_id = $id
    ");

    mysqli_begin_transaction($conn);

    try {

        // 🔥 CEK: hanya proses jika dari dipinjam → kembali
        if ($data['status'] == 'dipinjam' && $status == 'kembali') {

            while ($row = mysqli_fetch_assoc($details)) {

                mysqli_query($conn, "
                    UPDATE alat 
                    SET stok = stok + {$row['jumlah']}
                    WHERE id = {$row['alat_id']}
                ");
            }
        }

        // 🔥 Update status saja
        mysqli_query($conn, "
            UPDATE peminjaman 
            SET status = '$status'
            WHERE id = $id
        ");

        mysqli_commit($conn);

        $_SESSION['success'] = "Status berhasil diperbarui!";
        header("Location: peminjaman.php");
        exit;
    } catch (Exception $e) {

        mysqli_rollback($conn);

        $_SESSION['error'] = "Terjadi kesalahan!";
        header("Location: pengembalian.php?id=$id");
        exit;
    }
}
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
                        <h5>Ubah Status Peminjaman</h5>
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
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="">Pilih</option>
                                    <option value="dipinjam" <?= ($data['status'] == 'dipinjam') ? 'selected' : '' ?>>Dipinjam</option>
                                    <option value="kembali" <?= ($data['status'] == 'kembali') ? 'selected' : '' ?>>Dikembalikan</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>