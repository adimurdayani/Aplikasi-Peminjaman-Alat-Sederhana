<?php

session_start();
include 'koneksi.php';
$active = 'user';
$content = 'pages/user/edit.php';
include 'layouts/layout.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
