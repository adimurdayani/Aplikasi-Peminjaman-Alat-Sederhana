<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

// ambil user dari database (AMAN)
$stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // cek password (gunakan password_verify)
    if ($user && md5($password) === $user['password']) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Password salah!";
    }
} else {
    $_SESSION['error'] = "Username tidak ditemukan!";
}

header("Location: login.php");
exit;
