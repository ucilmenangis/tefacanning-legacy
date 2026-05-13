<?php

/**
 * FonnteService — WhatsApp notifications via Fonnte API.
 *
 * Methods:
 *   sendMessage($phone, $message)                   — raw send
 *   sendOrderConfirmation($orderId)                  — trigger 2: admin creates order -> WA to customer
 *   sendNewOrderToOwner($orderId)                    — trigger 1: customer pre-orders -> WA to owner
 *   sendReadyForPickup(int $batchId)                 — trigger 3: batch -> ready -> WA to all customers in batch
 *   sendResetCode(string $phone, string $code, int $minutes) — send password reset OTP via WA
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseService.php';

class FonnteService extends BaseService
{
    private string $token;
    private string $device;
    private string $ownerPhone;
    private string $apiUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        parent::__construct();
        $this->token = $_ENV['FONNTE_TOKEN'] ?? '';
        $this->device = $_ENV['FONNTE_DEVICE'] ?? '';
        $this->ownerPhone = $_ENV['FONNTE_OWNER_PHONE'] ?? '';
    }

    /**
     * Send WhatsApp message via Fonnte API using cURL.
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (empty($this->token) || empty($phone)) {
            error_log('Fonnte: Token or phone empty. Skipping.');
            return false;
        }

        $payload = [
            'target' => $phone,
            'message' => $message,
        ];

        if (!empty($this->device)) {
            $payload['device'] = $this->device;
        }

        try {
            $ch = curl_init($this->apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->token,
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                error_log("Fonnte: Message sent to {$phone}");
                return true;
            }

            error_log("Fonnte: Failed (HTTP {$httpCode}) to {$phone} - {$response}");
            return false;
        } catch (\Exception $e) {
            error_log('Fonnte: Exception - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Trigger 2: Admin creates order -> send confirmation to customer.
     */
    public function sendOrderConfirmation(int $orderId): bool
    {
        $order = $this->fetch(
            "SELECT o.order_number, o.total_amount, o.pickup_code,
                    c.name AS customer_name, c.phone AS customer_phone,
                    b.name AS batch_name, b.event_name
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             JOIN batches b ON b.id = o.batch_id
             WHERE o.id = ?",
            [$orderId]
        );

        if (!$order || empty($order['customer_phone'])) {
            return false;
        }

        $items = $this->fetchAll(
            "SELECT p.name, op.quantity, op.unit_price
             FROM order_product op
             JOIN products p ON p.id = op.product_id
             WHERE op.order_id = ?",
            [$orderId]
        );

        $itemLines = '';
        foreach ($items as $item) {
            $itemLines .= "- {$item['name']} x{$item['quantity']} @ Rp " . number_format($item['unit_price'], 0, ',', '.') . "\n";
        }

        $message = "🛒 *Konfirmasi Pesanan TEFA Canning*\n\n"
            . "Halo {$order['customer_name']},\n\n"
            . "Pesanan Anda telah dikonfirmasi!\n\n"
            . "📋 *No. Pesanan:* {$order['order_number']}\n"
            . "📦 *Batch:* {$order['batch_name']}\n"
            . "📅 *Event:* {$order['event_name']}\n\n"
            . "*Detail Pesanan:*\n{$itemLines}\n"
            . "💰 *Total:* Rp " . number_format($order['total_amount'], 0, ',', '.') . "\n\n"
            . "🔑 *Kode Pickup:* {$order['pickup_code']}\n"
            . "_(Simpan kode ini untuk pengambilan barang)_\n\n"
            . "Terima kasih telah berbelanja di TEFA Canning Polije! 🙏";

        return $this->sendMessage($order['customer_phone'], $message);
    }

    /**
     * Trigger 1: Customer submits pre-order -> notify owner.
     */
    public function sendNewOrderToOwner(int $orderId): bool
    {
        if (empty($this->ownerPhone)) {
            error_log('Fonnte: FONNTE_OWNER_PHONE not set. Skipping owner notification.');
            return false;
        }

        $order = $this->fetch(
            "SELECT o.order_number, o.total_amount,
                    c.name AS customer_name, c.phone AS customer_phone,
                    b.name AS batch_name
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             JOIN batches b ON b.id = o.batch_id
             WHERE o.id = ?",
            [$orderId]
        );

        if (!$order) {
            return false;
        }

        $items = $this->fetchAll(
            "SELECT p.name, op.quantity
             FROM order_product op
             JOIN products p ON p.id = op.product_id
             WHERE op.order_id = ?",
            [$orderId]
        );

        $itemLines = '';
        foreach ($items as $item) {
            $itemLines .= "- {$item['name']} x{$item['quantity']}\n";
        }

        $message = "📥 *Pesanan Baru Masuk!*\n\n"
            . "*No. Pesanan:* {$order['order_number']}\n"
            . "*Pelanggan:* {$order['customer_name']}\n"
            . "*Kontak:* {$order['customer_phone']}\n"
            . "*Batch:* {$order['batch_name']}\n\n"
            . "*Detail Pesanan:*\n{$itemLines}\n"
            . "*Total:* Rp " . number_format($order['total_amount'], 0, ',', '.') . "\n\n"
            . "Silakan cek panel admin untuk detail lebih lanjut.";

        return $this->sendMessage($this->ownerPhone, $message);
    }

    /**
     * Trigger 3: Batch status -> "ready" -> notify all customers in batch.
     * Returns count of messages sent.
     */
    public function sendReadyForPickup(int $batchId): int
    {
        $orders = $this->fetchAll(
            "SELECT DISTINCT o.order_number, o.pickup_code, o.total_amount,
                    c.name AS customer_name, c.phone AS customer_phone,
                    b.name AS batch_name
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             JOIN batches b ON b.id = o.batch_id
             WHERE o.batch_id = ? AND o.deleted_at IS NULL AND c.phone IS NOT NULL AND c.phone != ''",
            [$batchId]
        );

        $sent = 0;
        foreach ($orders as $order) {
            $message = "✅ *Pesanan Siap Diambil!*\n\n"
                . "Halo {$order['customer_name']},\n\n"
                . "Pesanan Anda sudah siap untuk diambil!\n\n"
                . "📋 *No. Pesanan:* {$order['order_number']}\n"
                . "📦 *Batch:* {$order['batch_name']}\n"
                . "🔑 *Kode Pickup:* {$order['pickup_code']}\n\n"
                . "📍 Silakan datang ke lokasi TEFA Canning Polije\n"
                . "⏰ Jam operasional: Senin - Jumat, 08:00 - 16:00 WIB\n\n"
                . "Jangan lupa bawa kode pickup Anda! 🙏";

            if ($this->sendMessage($order['customer_phone'], $message)) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * Send password reset OTP code via WhatsApp.
     */
    public function sendResetCode(string $phone, string $code, int $minutes = 15): bool
    {
        $message = "🔑 *Reset Password TEFA Canning*\n\n"
            . "Kode OTP Anda: *{$code}*\n\n"
            . "Kode berlaku selama {$minutes} menit.\n"
            . "Jangan bagikan kode ini kepada siapapun.\n\n"
            . "Jika Anda tidak meminta reset password, abaikan pesan ini.\n\n"
            . "— TEFA Canning Polije";

        return $this->sendMessage($phone, $message);
    }
}
