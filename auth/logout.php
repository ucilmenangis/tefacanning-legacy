<?php
require_once __DIR__ . '/../includes/auth.php';

$type = $_GET['type'] ?? '';

if ($type === 'admin') {
    Auth::admin()->logout();
    header('Location: login-admin.php');
} else {
    Auth::customer()->logout();
    header('Location: login-customer.php');
}
exit;
