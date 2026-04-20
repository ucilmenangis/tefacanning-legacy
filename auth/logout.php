<?php
require_once __DIR__ . '/../includes/auth.php';

$type = $_GET['type'] ?? '';

if ($type === 'admin') {
    logoutAdmin();
    header('Location: login-admin.php');
} else {
    logoutCustomer();
    header('Location: login-customer.php');
}
exit;
