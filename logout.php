<?php
require_once __DIR__ . '/config.php';

// Bersihkan session
$_SESSION = [];
session_destroy();

// Arahkan ke login
header('Location: login.php');
exit;
?>
