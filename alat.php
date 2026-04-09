<?php

session_start();
include 'koneksi.php';
$active = 'alat';
$content = 'pages/alat/index.php';
include 'layouts/layout.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
