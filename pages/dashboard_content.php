<?php
// Total user
$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM user"))['total'];

// Total alat
$total_alat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM alat"))['total'];

// Total peminjaman
$total_peminjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman"))['total'];

// Data peminjaman terbaru
$peminjaman = mysqli_query($conn, "
    SELECT p.*, u.username
    FROM peminjaman p
    JOIN user u ON p.user_id = u.id
    ORDER BY p.id DESC
    LIMIT 5
");
?>
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <h5 class="card-header">Dashboard</h5>
                <div class="card-body">

                    <!-- 🔥 Statistik -->
                    <div class="row mb-4">

                        <div class="col-md-4">
                            <div class="card text-white bg-primary shadow">
                                <div class="card-body">
                                    <h6>Total User</h6>
                                    <h2><?= $total_user; ?></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card text-white bg-success shadow">
                                <div class="card-body">
                                    <h6>Total Alat</h6>
                                    <h2><?= $total_alat; ?></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card text-white bg-warning shadow">
                                <div class="card-body">
                                    <h6>Total Peminjaman</h6>
                                    <h2><?= $total_peminjaman; ?></h2>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- 🔥 Tabel Peminjaman Terbaru -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h6 class="mb-0">Peminjaman Terbaru</h6>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>User</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php while ($row = mysqli_fetch_assoc($peminjaman)): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $row['username']; ?></td>
                                            <td><?= $row['tanggal_pinjam']; ?></td>
                                            <td><?= $row['tanggal_kembali']; ?></td>
                                            <td>
                                                <?php if ($row['status'] == 'dipinjam'): ?>
                                                    <span class="badge bg-warning">Dipinjam</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Dikembalikan</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>