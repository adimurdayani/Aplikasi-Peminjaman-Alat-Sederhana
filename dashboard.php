<?php
session_start();
include  'koneksi.php';
$active = 'dashboard';
$content = "pages/dashboard_content.php";
include "layouts/layout.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
