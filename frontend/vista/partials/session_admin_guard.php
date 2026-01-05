<?php

require_once __DIR__ . '/session_guard.php';

if (!$isAdmin) {
    header('Location: index.php');
    exit;
}
