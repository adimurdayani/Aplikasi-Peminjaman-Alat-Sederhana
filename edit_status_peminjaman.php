<?php

session_start();
include 'koneksi.php';
$active = 'peminjaman';
$content = 'pages/peminjaman/status.php';
include 'layouts/layout.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
