<?

session_start();
?>
<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand" href="#">Peminjaman</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <?php if ($_SESSION['role'] == 'admin'): ?>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'dashboard') ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'alat') ? 'active' : '' ?>" href="alat.php">Alat</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'peminjaman') ? 'active' : '' ?>" href="peminjaman.php">Peminjaman</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'ajukan_peminjaman') ? 'active' : '' ?>" href="tambah_peminjaman.php">Ajukan Pinjaman</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'user') ? 'active' : '' ?>" href="user.php">User</a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'petugas'): ?>


                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'dashboard') ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'alat') ? 'active' : '' ?>" href="alat.php">Alat</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'peminjaman') ? 'active' : '' ?>" href="peminjaman.php">Peminjaman</a>
                        </li>

                    <?php elseif ($_SESSION['role'] == 'peminjam'): ?>


                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'dashboard') ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'alat') ? 'active' : '' ?>" href="alat.php">Alat</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'peminjaman') ? 'active' : '' ?>" href="peminjaman.php">Peminjaman</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'ajukan_peminjaman') ? 'active' : '' ?>" href="tambah_peminjaman.php">Ajukan Pinjaman</a>
                        </li>


                    <?php endif; ?>

                    <!-- menu umum -->
                    <li class="nav-item">
                        <a class="nav-link" onclick="confirm('Apakah anda yakin ingin keluar dari aplikasi?')" href="logout.php">Keluar</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
</header>