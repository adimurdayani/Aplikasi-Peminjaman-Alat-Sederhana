<?php

session_start();
include 'koneksi.php';
$active = 'ajukan_peminjaman';
$content = 'pages/peminjaman/tambah.php';
include 'layouts/layout.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
