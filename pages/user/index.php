<?php
if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];
    // 🔥 Mulai transaction
    mysqli_begin_transaction($conn);

    try {

        // 🔥 Hapus peminjaman (detail ikut terhapus jika pakai ON DELETE CASCADE)
        mysqli_query($conn, "DELETE FROM user WHERE id = $id");

        // 🔥 Commit
        mysqli_commit($conn);

        $_SESSION['success'] = "Data user berhasil dihapus";
    } catch (Exception $e) {

        mysqli_rollback($conn);

        $_SESSION['error'] = "Gagal menghapus data!";
    }

    header("Location: user.php");
    exit;
}
// query utama
$query = mysqli_query($conn, "
    SELECT *
    FROM user
    ORDER BY id DESC
");

$no = 1;
?>

<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">User</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center"">
                        <h5 class=" mb-0">Peminajaman</h5>
                        <a href="user_tambah.php" class="btn btn-primary mb-0">+ Tambah Data</a>
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
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $row['username']; ?></td>
                                            <td><?= $row['role'] ?></td>
                                            <td>
                                                <a href="user_edit.php?id=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-warning text-white">
                                                    Edit
                                                </a>

                                                <a href="?hapus=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-danger text-white"
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Data tidak ditemukan</td>
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