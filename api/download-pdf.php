<?php
/**
 * PDF Download Endpoint
 *
 * Dual auth: admin can download any order, customer can only download their own.
 * Usage: api/download-pdf.php?id=5
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

startSession();

$orderId = intval($_GET['id'] ?? 0);
if (!$orderId) {
    http_response_code(400);
    die('ID pesanan tidak valid.');
}

// Check which guard is active
$adminId = getAdminId();
$customerId = getCustomerId();

if ($adminId) {
    // Admin can access any order — no ownership check
} elseif ($customerId) {
    // Customer: verify order belongs to them
    $order = db_fetch(
        "SELECT customer_id FROM orders WHERE id = ? AND deleted_at IS NULL",
        [$orderId]
    );
    if (!$order || $order['customer_id'] != $customerId) {
        http_response_code(403);
        die('Anda tidak memiliki akses ke pesanan ini.');
    }
} else {
    // Not logged in at all
    header('Location: /auth/login-customer.php');
    exit;
}

require_once __DIR__ . '/../classes/PdfService.php';

try {
    $pdfService = new PdfService();
    $pdfService->download($orderId);
} catch (InvalidArgumentException $e) {
    http_response_code(404);
    die($e->getMessage());
}
