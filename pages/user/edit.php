<?php
$id = (int) $_GET['id'];

// 🔥 Ambil data lama (untuk ditampilkan di form)
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE id = $id"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $role = $_POST['role'];
    $password_input = $_POST['password'];

    // Validasi
    if (empty($username) || empty($role)) {
        $_SESSION['error'] = "Username dan role wajib diisi!";
        header("Location: edit_user.php?edit=$id");
        exit;
    }

    // 🔥 Jika password diisi → update password
    if (!empty($password_input)) {
        $password = password_hash($password_input, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE user SET username=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $password, $role, $id);
    } else {
        // 🔥 Jika password kosong → jangan ubah password
        $stmt = $conn->prepare("UPDATE user SET username=?, role=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $role, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data user berhasil diupdate!";
        header("Location: user.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal update data!";
        header("Location: edit_user.php?edit=$id");
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
                <li class="breadcrumb-item active" aria-current="page">Edit User</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Edit User</h5>
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
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username"
                                    value="<?= $user['username']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password (kosongkan jika tidak diubah)</label>
                                <input type="password" class="form-control" name="password">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="">Pilih</option>
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="petugas" <?= $user['role'] == 'petugas' ? 'selected' : ''; ?>>Petugas</option>
                                    <option value="peminjam" <?= $user['role'] == 'peminjam' ? 'selected' : ''; ?>>Peminjam</option>
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