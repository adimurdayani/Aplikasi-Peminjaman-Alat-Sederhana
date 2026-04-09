<?php

session_start();
include 'koneksi.php';
$active = 'peminjaman';
$content = 'pages/peminjaman/index.php';
include 'layouts/layout.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
