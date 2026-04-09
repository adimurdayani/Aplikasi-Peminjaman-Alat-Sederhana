<?php
if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    // 🔥 Ambil semua detail alat
    $details = mysqli_query($conn, "
        SELECT alat_id, jumlah 
        FROM detail_peminjam 
        WHERE peminjaman_id = $id
    ");

    // 🔥 Mulai transaction
    mysqli_begin_transaction($conn);

    try {

        // 🔥 Kembalikan stok
        while ($row = mysqli_fetch_assoc($details)) {
            $alat_id = $row['alat_id'];
            $jumlah = $row['jumlah'];

            mysqli_query($conn, "
                UPDATE alat 
                SET stok = stok + $jumlah
                WHERE id = '$alat_id'
            ");
        }

        // 🔥 Hapus peminjaman (detail ikut terhapus jika pakai ON DELETE CASCADE)
        mysqli_query($conn, "DELETE FROM peminjaman WHERE id = $id");

        // 🔥 Commit
        mysqli_commit($conn);

        $_SESSION['success'] = "Data peminjaman berhasil dihapus & stok dikembalikan!";
    } catch (Exception $e) {

        mysqli_rollback($conn);

        $_SESSION['error'] = "Gagal menghapus data!";
    }

    header("Location: peminjaman.php");
    exit;
}
$user_id = (int) $_SESSION['user_id'];
$role = $_SESSION['role'];

// kondisi filter
$where = "";
if ($role == 'peminjam') {
    $where = "WHERE p.user_id = '$user_id'";
}

// query utama
$query = mysqli_query($conn, "
    SELECT 
        p.id,
        p.user_id,
        p.tanggal_pinjam,
        p.tanggal_kembali,
        p.status,
        u.username,
        GROUP_CONCAT(CONCAT(a.nama_alat, ' (jumlah= ', dp.jumlah, ')') SEPARATOR ', ') as nama_alat
    FROM peminjaman p
    JOIN user u ON p.user_id = u.id
    LEFT JOIN detail_peminjam dp ON p.id = dp.peminjaman_id
    LEFT JOIN alat a ON dp.alat_id = a.id
    $where
    GROUP BY p.id
    ORDER BY p.id DESC
");

$no = 1;
?>

<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Peminjaman</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center"">
                        <h5 class=" mb-0">Peminajaman</h5>
                        <a href="tambah_peminjaman.php" class="btn btn-primary mb-0">+ Tambah Data</a>
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

                        <table class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>User</th>
                                    <th>Nama Alat</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $row['username']; ?></td>
                                            <td><?= $row['nama_alat'] ?? '-' ?></td>
                                            <td><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td><?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                                            <td>
                                                <?php if ($row['status'] == 'dipinjam'): ?>
                                                    <span class="badge bg-warning">Dipinjam</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Dikembalikan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="edit_peminjaman.php?id=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-warning text-white">
                                                    Edit
                                                </a>

                                                <?php if ($_SESSION['role'] == 'petugas'): ?>
                                                    <a href="edit_status_peminjaman.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-sm btn-info text-white">
                                                        Edit Status
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                                    <a href="?hapus=<?= $row['id']; ?>"
                                                        class="btn btn-sm btn-danger text-white"
                                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                        Hapus
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Data tidak ditemukan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>