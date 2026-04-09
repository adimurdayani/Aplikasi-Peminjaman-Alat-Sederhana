<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 🔥 Validasi
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Semua field wajib diisi!";
        header("Location: register.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Konfirmasi password tidak cocok!";
        header("Location: register.php");
        exit;
    }

    // 🔥 Cek username sudah ada atau belum
    $cek = $conn->prepare("SELECT id FROM user WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username sudah digunakan!";
        header("Location: register.php");
        exit;
    }

    // 🔥 Hash password (AMAN)
    $hashed_password = md5($password);

    // 🔥 Default role peminjam
    $role = 'peminjam';

    // 🔥 Insert user baru
    $stmt = $conn->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal registrasi!";
        header("Location: register.php");
        exit;
    }
}
