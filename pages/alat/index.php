<?php

// PROSES HAPUS
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    $delete = mysqli_query($conn, "DELETE FROM alat WHERE id = $id");

    if ($delete) {
        $_SESSION['success'] = "Data berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus data!";
    }

    header("Location: alat.php");
    exit;
}
$query = mysqli_query($conn, "SELECT * FROM alat");
$no = 1;

?>
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Alat</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center"">
                        <h5 class=" mb-0">Alat</h5>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <a href="tambah_alat.php" class="btn btn-primary mb-0">+ Tambah Alat</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?= $_SESSION['success']; ?>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>
                        <table class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Alat</th>
                                    <th>Stok</th>
                                    <?php if ($_SESSION['role'] == 'admin'): ?>
                                        <th>Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $row['nama_alat']; ?></td>
                                            <td><?= $row['stok']; ?></td>
                                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                                <td>
                                                    <a href="edit_alat.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>

                                                    <a href="?hapus=<?= $row['id']; ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                        Hapus
                                                    </a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?php if ($_SESSION['role'] == 'admin'): ?>3<?php endif; ?>" class="text-center">Data tidak ditemukan</td>
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