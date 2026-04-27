<?php
/**
 * Admin View Order Page (read-only)
 */

$pageTitle   = 'Detail Pesanan';
$currentPage = 'orders';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    setFlash('error', 'ID pesanan tidak valid.');
    header('Location: orders.php');
    exit;
}

$order = db_fetch(
    "SELECT o.id, o.order_number, o.pickup_code, o.status, o.total_amount, o.profit,
            o.picked_up_at, o.created_at, o.updated_at,
            c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email,
            c.organization, c.address,
            b.name AS batch_name, b.event_name, b.event_date
     FROM orders o
     JOIN customers c ON c.id = o.customer_id
     JOIN batches b ON b.id = o.batch_id
     WHERE o.id = ? AND o.deleted_at IS NULL",
    [$id]
);

if (!$order) {
    setFlash('error', 'Pesanan tidak ditemukan.');
    header('Location: orders.php');
    exit;
}

$items = db_fetch_all(
    "SELECT op.quantity, op.unit_price, op.subtotal, p.name AS product_name, p.sku
     FROM order_product op
     JOIN products p ON p.id = op.product_id
     WHERE op.order_id = ?",
    [$id]
);

$statusMap = [
    'processing' => ['label' => 'Processing', 'bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#dbeafe'],
    'pending'    => ['label' => 'Pending',    'bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'],
    'ready'      => ['label' => 'Ready',      'bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'],
    'picked_up'  => ['label' => 'Picked Up',  'bg' => '#f9fafb', 'color' => '#6b7280', 'border' => '#e5e7eb'],
];
$st = $statusMap[$order['status']] ?? ['label' => $order['status'], 'bg' => '#f9fafb', 'color' => '#6b7280', 'border' => '#e5e7eb'];

include __DIR__ . '/../includes/header-admin.php';
?>

<style>
    .breadcrumb { font-size: 12px; color: #94a3b8; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .breadcrumb a { color: #94a3b8; text-decoration: none; }
    .breadcrumb a:hover { color: #E02424; }
    .breadcrumb .active { color: #475569; font-weight: 500; }

    .card { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 24px; margin-bottom: 20px; }
    .card-title { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

    .info-row { display: flex; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f8fafc; }
    .info-row:last-child { border-bottom: none; }
    .info-label { width: 140px; font-size: 12px; font-weight: 600; color: #94a3b8; flex-shrink: 0; }
    .info-value { font-size: 13px; color: #1e293b; font-weight: 500; }

    .status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; border: 1px solid; }

    .btn-edit { background: #E02424; color: #fff; font-size: 12px; font-weight: 700; padding: 8px 16px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .btn-edit:hover { background: #9B1C1C; }
    .btn-secondary { background: #fff; border: 1px solid #e2e8f0; color: #64748b; font-size: 12px; font-weight: 600; padding: 8px 16px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .btn-secondary:hover { background: #f8fafc; color: #1e293b; }

    .items-table { width: 100%; font-size: 12.5px; border-collapse: collapse; }
    .items-table th { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; padding: 10px 14px; text-align: left; border-bottom: 1px solid #f1f5f9; }
    .items-table td { padding: 12px 14px; border-bottom: 1px solid #f8fafc; color: #374151; }
    .items-table tr:last-child td { border-bottom: none; }
</style>

<!-- Header -->
<div class="flex items-center justify-between mb-2">
    <div>
        <div class="breadcrumb">
            <a href="orders.php">Pesanan</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="active"><?php echo htmlspecialchars($order['order_number']); ?></span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy flex items-center gap-3">
            <?php echo htmlspecialchars($order['order_number']); ?>
            <span class="status-badge" style="background:<?php echo $st['bg']; ?>;color:<?php echo $st['color']; ?>;border-color:<?php echo $st['border']; ?>">
                <span class="w-1.5 h-1.5 rounded-full" style="background:<?php echo $st['color']; ?>"></span>
                <?php echo $st['label']; ?>
            </span>
        </h1>
    </div>
    <div class="flex items-center gap-2">
        <a href="../api/download-pdf.php?id=<?php echo $id; ?>" class="btn-secondary" style="color:#059669;border-color:#059669;">
            <i class="ph ph-file-pdf"></i> PDF
        </a>
        <a href="edit-order.php?id=<?php echo $id; ?>" class="btn-edit">
            <i class="ph ph-note-pencil"></i> Edit
        </a>
    </div>
</div>

<div class="grid grid-cols-3 gap-5">
    <!-- Main Column -->
    <div class="col-span-2">
        <!-- Item Pesanan -->
        <div class="card shadow-sm">
            <div class="card-title">
                <i class="ph ph-shopping-cart text-slate-400"></i>
                Item Pesanan
            </div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="font-semibold" style="color:#E02424"><?php echo htmlspecialchars($item['product_name']); ?></div>
                            <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($item['sku']); ?></div>
                        </td>
                        <td class="font-semibold"><?php echo $item['quantity']; ?></td>
                        <td class="text-gray-500"><?php echo FormatHelper::rupiah($item['unit_price']); ?></td>
                        <td class="font-semibold text-navy"><?php echo FormatHelper::rupiah($item['subtotal']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="flex justify-end pt-4 mt-2 border-t border-gray-100">
                <div class="text-right">
                    <span class="text-[12px] text-gray-400">Total</span>
                    <div class="text-[20px] font-extrabold" style="color:#E02424"><?php echo FormatHelper::rupiah($order['total_amount']); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Info Pelanggan -->
        <div class="card shadow-sm">
            <div class="card-title">
                <i class="ph ph-user text-slate-400"></i>
                Pelanggan
            </div>
            <div class="info-row">
                <div class="info-label">Nama</div>
                <div class="info-value"><?php echo htmlspecialchars($order['customer_name']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Telepon</div>
                <div class="info-value"><?php echo htmlspecialchars($order['customer_phone'] ?? '-'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value"><?php echo htmlspecialchars($order['customer_email'] ?? '-'); ?></div>
            </div>
            <?php if ($order['organization']): ?>
            <div class="info-row">
                <div class="info-label">Organisasi</div>
                <div class="info-value"><?php echo htmlspecialchars($order['organization']); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Info Pesanan -->
        <div class="card shadow-sm">
            <div class="card-title">
                <i class="ph ph-article-ny-times text-slate-400"></i>
                Info Pesanan
            </div>
            <div class="info-row">
                <div class="info-label">Batch</div>
                <div class="info-value"><?php echo htmlspecialchars($order['batch_name']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Kode Pickup</div>
                <div class="info-value font-mono"><?php echo htmlspecialchars($order['pickup_code']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Dibuat</div>
                <div class="info-value"><?php echo FormatHelper::tanggal($order['created_at']); ?></div>
            </div>
            <?php if ($order['picked_up_at']): ?>
            <div class="info-row">
                <div class="info-label">Diambil</div>
                <div class="info-value text-emerald-600 font-semibold"><?php echo FormatHelper::tanggal($order['picked_up_at']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
