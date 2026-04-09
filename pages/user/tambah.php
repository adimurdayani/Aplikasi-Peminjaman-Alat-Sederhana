<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    // Validasi sederhana
    if (empty($username) || empty($_POST['password']) || empty($role)) {
        $_SESSION['error'] = "Semua field wajib diisi!";
        header("Location: tambah_user.php");
        exit;
    }

    // Insert ke database
    $stmt = $conn->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Data user berhasil ditambahkan!";
        header("Location: user.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan data!";
        header("Location: tambah_user.php");
        exit;
    }
}
?>
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="user.php">User</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah User</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Tambah User</h5>
                        <a href="user.php" class="btn btn-secondary mb-0"> Kembali</a>
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
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="">Pilih</option>
                                    <option value="admin">Admin</option>
                                    <option value="petugas">Petugas</option>
                                    <option value="peminjam">Peminjam</option>
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