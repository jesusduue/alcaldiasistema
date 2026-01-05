<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$currentUser = $_SESSION['user'] ?? null;

if (!$currentUser) {
    header('Location: login.php');
    exit;
}

$isAdmin = !empty($currentUser['is_admin']);
