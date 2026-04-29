<?php
/**
 * Admin View Order Page (read-only)
 */

$pageTitle   = 'Detail Pesanan';
$currentPage = 'orders';

require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    FlashMessage::set('error', 'ID pesanan tidak valid.');
    header('Location: orders.php');
    exit;
}

$order = Database::getInstance()->fetch(
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
    FlashMessage::set('error', 'Pesanan tidak ditemukan.');
    header('Location: orders.php');
    exit;
}

$items = Database::getInstance()->fetchAll(
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



<!-- Header -->
<div class="flex items-center justify-between mb-2">
    <div>
        <div class="flex items-center gap-2 text-[12px] text-gray-400 mb-3">
            <a href="orders.php" class="hover:text-primary transition-colors">Pesanan</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="text-slate-600 font-medium"><?php echo htmlspecialchars($order['order_number']); ?></span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy flex items-center gap-3">
            <?php echo htmlspecialchars($order['order_number']); ?>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold border" style="background:<?php echo $st['bg']; ?>;color:<?php echo $st['color']; ?>;border-color:<?php echo $st['border']; ?>">
                <span class="w-1.5 h-1.5 rounded-full" style="background:<?php echo $st['color']; ?>"></span>
                <?php echo $st['label']; ?>
            </span>
        </h1>
    </div>
    <div class="flex items-center gap-2">
        <a href="../api/download-pdf.php?id=<?php echo $id; ?>" class="inline-flex items-center gap-1 bg-white border border-emerald-500 text-emerald-600 text-[12px] font-semibold px-4 py-2 rounded-lg transition-colors hover:bg-emerald-50">
            <i class="ph ph-file-pdf"></i> PDF
        </a>
        <a href="edit-order.php?id=<?php echo $id; ?>" class="inline-flex items-center gap-1 bg-primary text-white text-[12px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark">
            <i class="ph ph-note-pencil"></i> Edit
        </a>
    </div>
</div>

<div class="grid grid-cols-3 gap-5">
    <!-- Main Column -->
    <div class="col-span-2">
        <!-- Item Pesanan -->
        <div class="bg-white border border-gray-100 rounded-xl p-6 mb-5 shadow-sm">
            <div class="text-[14px] font-bold text-navy mb-4 flex items-center gap-2">
                <i class="ph ph-shopping-cart text-slate-400"></i>
                Item Pesanan
            </div>
            <table class="w-full text-[12.5px] border-collapse">
                <thead>
                    <tr>
                        <th class="text-[11px] font-bold text-gray-400 uppercase px-3.5 py-2.5 text-left border-b border-gray-100">Produk</th>
                        <th class="text-[11px] font-bold text-gray-400 uppercase px-3.5 py-2.5 text-left border-b border-gray-100">Qty</th>
                        <th class="text-[11px] font-bold text-gray-400 uppercase px-3.5 py-2.5 text-left border-b border-gray-100">Harga Satuan</th>
                        <th class="text-[11px] font-bold text-gray-400 uppercase px-3.5 py-2.5 text-left border-b border-gray-100">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="px-3.5 py-3 border-b border-gray-50">
                            <div class="font-semibold text-primary"><?php echo htmlspecialchars($item['product_name']); ?></div>
                            <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($item['sku']); ?></div>
                        </td>
                        <td class="font-semibold px-3.5 py-3 border-b border-gray-50"><?php echo $item['quantity']; ?></td>
                        <td class="text-gray-500 px-3.5 py-3 border-b border-gray-50"><?php echo FormatHelper::rupiah($item['unit_price']); ?></td>
                        <td class="font-semibold text-navy px-3.5 py-3 border-b border-gray-50"><?php echo FormatHelper::rupiah($item['subtotal']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="flex justify-end pt-4 mt-2 border-t border-gray-100">
                <div class="text-right">
                    <span class="text-[12px] text-gray-400">Total</span>
                    <div class="text-[20px] font-extrabold text-primary"><?php echo FormatHelper::rupiah($order['total_amount']); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Info Pelanggan -->
        <div class="bg-white border border-gray-100 rounded-xl p-6 mb-5 shadow-sm">
            <div class="text-[14px] font-bold text-navy mb-4 flex items-center gap-2">
                <i class="ph ph-user text-slate-400"></i>
                Pelanggan
            </div>
            <div class="flex items-start py-2.5 border-b border-gray-50">
                <div class="w-[140px] text-[12px] font-semibold text-gray-400 shrink-0">Nama</div>
                <div class="text-[13px] text-navy font-medium"><?php echo htmlspecialchars($order['customer_name']); ?></div>
            </div>
            <div class="flex items-start py-2.5 border-b border-gray-50">
                <div class="w-[140px] text-[12px] font-semibold text-gray-400 shrink-0">Telepon</div>
                <div class="text-[13px] text-navy font-medium"><?php echo htmlspecialchars($order['customer_phone'] ?? '-'); ?></div>
            </div>
            <div class="flex items-start py-2.5 border-b border-gray-50">
                <div class="w-[140px] text-[12px] font-semibold text-gray-400 shrink-0">Email</div>
                <div class="text-[13px] text-navy font-medium"><?php echo htmlspecialchars($order['customer_email'] ?? '-'); ?></div>
            </div>
            <?php if ($order['organization']): ?>
            <div class="flex items-start py-2.5">
                <div class="w-[140px] text-[12px] font-semibold text-gray-400 shrink-0">Organisasi</div>
                <div class="text-[13px] text-navy font-medium"><?php echo htmlspecialchars($order['organization']); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Info Pesanan -->
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
            <div class="text-[14px] font-bold text-navy mb-4 flex items-center gap-2">
                <i class="ph ph-article-ny-times text-slate-400"></i>
                Info Pesanan
            </div>
            <div class="flex items-start py-2.5 border-b border-gray-50">
                <div class="w-[140px] text-[12px] font-semibold text-gray-400 shrink-0">Batch</div>
                <div class="text-[13px] text-navy font-medium"><?php echo htmlspecialchars($order['batch_name']); ?></div>
            </div>
            <div class="flex items-start py-2.5 border-b border-gray-50">
                <div class="w-[140px] text-[12px] font-semibold text-gray-400 shrink-0">Kode Pickup</div>
                <div class="text-[13px] text-navy font-medium font-mono"><?php echo htmlspecialchars($order['pickup_code']); ?></div>
            </div>
            <div class="flex items-start py-2.5 border-b border-gray-50">
                <div class="w-[140px] text-[12px] font-semibold text-gray-400 shrink-0">Dibuat</div>
                <div class="text-[13px] text-navy font-medium"><?php echo FormatHelper::tanggal($order['created_at']); ?></div>
            </div>
            <?php if ($order['picked_up_at']): ?>
            <div class="flex items-start py-2.5">
                <div class="w-[140px] text-[12px] font-semibold text-gray-400 shrink-0">Diambil</div>
                <div class="text-[13px] text-emerald-600 font-semibold"><?php echo FormatHelper::tanggal($order['picked_up_at']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
