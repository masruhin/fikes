<?php
session_start();
require_once __DIR__ . '/database.php';

function is_login() {
    return isset($_SESSION['admin_id']);
}

function wajib_login() {
    if (!is_login()) {
        header('Location: login.php');
        exit;
    }
}

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
