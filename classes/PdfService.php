<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/FormatHelper.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService extends BaseService
{
    /**
     * Fetch order with items, customer, and batch data from DB.
     */
    private function getOrderData(int $orderId): ?array
    {
        $order = $this->fetch(
            "SELECT o.id, o.order_number, o.pickup_code, o.status, o.total_amount,
                    o.profit, o.picked_up_at, o.created_at,
                    c.name AS customer_name, c.phone, c.email,
                    c.organization, c.address,
                    b.name AS batch_name, b.event_name, b.event_date
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             JOIN batches b ON b.id = o.batch_id
             WHERE o.id = ? AND o.deleted_at IS NULL",
            [$orderId]
        );

        if (!$order) {
            return null;
        }

        $items = $this->fetchAll(
            "SELECT op.quantity, op.unit_price, op.subtotal, p.name, p.sku
             FROM order_product op
             JOIN products p ON p.id = op.product_id
             WHERE op.order_id = ?",
            [$orderId]
        );

        $order['items'] = $items;
        return $order;
    }

    /**
     * Render HTML template to string using output buffering.
     */
    private function renderTemplate(array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include __DIR__ . '/../views/pdf/order-report.php';
        return ob_get_clean();
    }

    /**
     * Generate PDF as string.
     */
    public function generateOrderPdf(int $orderId): string
    {
        $order = $this->getOrderData($orderId);
        if (!$order) {
            throw new InvalidArgumentException('Pesanan tidak ditemukan.');
        }

        $html = $this->renderTemplate(['order' => $order]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Stream PDF to browser as download.
     */
    public function download(int $orderId, string $filename = ''): void
    {
        $order = $this->getOrderData($orderId);
        if (!$order) {
            throw new InvalidArgumentException('Pesanan tidak ditemukan.');
        }

        if (empty($filename)) {
            $filename = 'pesanan-' . $order['order_number'] . '.pdf';
        }

        $pdf = $this->generateOrderPdf($orderId);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }
}
