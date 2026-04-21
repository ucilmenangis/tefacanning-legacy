<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/OrderService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';
requireCustomer();

$customerId = getCustomerId();
$orderService = new OrderService();

// Handle cancel action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel') {
    $cancelId = (int) ($_POST['order_id'] ?? 0);
    if ($cancelId > 0) {
        if ($orderService->cancel($cancelId, $customerId)) {
            setFlash('success', 'Pesanan berhasil dibatalkan.');
        } else {
            setFlash('error', 'Pesanan tidak bisa dibatalkan. Hanya pesanan berstatus Menunggu yang bisa dibatalkan.');
        }
    }
    header('Location: orders.php');
    exit;
}

// Get orders
$orders = $orderService->getByCustomer($customerId);
$totalOrders = count($orders);

$pageTitle = 'Riwayat Pesanan';
$currentPage = 'orders';
include __DIR__ . '/../includes/header-customer.php';
?>

<style>
    .table-container {
        background: white; border-radius: 12px; border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,.04); overflow: hidden;
    }
    .search-input {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px 8px 36px;
        font-size: 13px; color: #374151; width: 240px; outline: none;
        transition: border-color 0.15s;
    }
    .search-input:focus { border-color: #E02424; }
    .badge-blue  { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
    .badge-amber { background: #fff7ed; color: #d97706; border: 1px solid #ffedd5; }
    .badge-green { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .badge-gray  { background: #f9fafb; color: #6b7280; border: 1px solid #e5e7eb; }
    .page-select {
        border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 8px;
        font-size: 12px; color: #64748b; background: white; outline: none;
    }
    .action-link {
        transition: color 0.15s; display: inline-flex; align-items: center; gap: 4px;
    }
    .alert-error {
        background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;
        border-radius: 8px; padding: 10px 14px; font-size: 13px;
        display: flex; align-items: center; gap: 8px;
    }
</style>

<?php echo renderFlash(); ?>

<h1 class="text-[22px] font-bold text-navy mb-6">Riwayat Pesanan</h1>

<div class="table-container">

    <!-- Search -->
    <div class="p-4 flex justify-end border-b border-gray-50">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="search-input" placeholder="Cari no. pesanan..." class="search-input" oninput="filterTable()">
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[13px]">
            <thead>
                <tr class="text-gray-400 border-b border-gray-50 bg-[#fafafa]">
                    <th class="px-6 py-4 font-semibold">No. Pesanan</th>
                    <th class="px-5 py-4 font-semibold">Batch</th>
                    <th class="px-5 py-4 font-semibold">Produk</th>
                    <th class="px-5 py-4 font-semibold">Total</th>
                    <th class="px-5 py-4 font-semibold uppercase text-[11px] tracking-wider">Status</th>
                    <th class="px-5 py-4 font-semibold">Tanggal</th>
                    <th class="px-6 py-4 font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="orders-tbody">
                <?php foreach ($orders as $order):
                    $s = FormatHelper::orderStatus($order['status']);
                ?>
                <tr class="order-row" data-search="<?php echo strtolower($order['order_number']); ?>">
                    <td class="px-6 py-5 font-bold text-navy">
                        <?php echo htmlspecialchars($order['order_number']); ?>
                    </td>
                    <td class="px-5 py-5 text-gray-500">
                        <div class="flex items-center gap-2">
                            <i class="ph-bold ph-calendar-blank text-gray-300"></i>
                            <?php echo htmlspecialchars($order['batch_name']); ?>
                        </div>
                    </td>
                    <td class="px-5 py-5 text-gray-500">
                        <?php echo $order['item_count']; ?> Item
                    </td>
                    <td class="px-5 py-5 font-bold text-[#E02424]">
                        <?php echo FormatHelper::rupiah((float) $order['total_amount']); ?>
                    </td>
                    <td class="px-5 py-5">
                        <span class="<?php echo $s['badge']; ?> px-2.5 py-1 rounded-full text-[11px] font-bold flex items-center gap-1.5 w-fit">
                            <i class="ph-fill <?php echo $s['icon']; ?>"></i>
                            <?php echo $s['label']; ?>
                        </span>
                    </td>
                    <td class="px-5 py-5 text-gray-500">
                        <?php echo FormatHelper::tanggal($order['created_at']); ?>
                    </td>
                    <td class="px-6 py-5 text-right whitespace-nowrap">
                        <?php if ($order['status'] === 'pending'): ?>
                        <div class="flex items-center gap-4 justify-end">
                            <a href="edit-order.php?id=<?php echo $order['id']; ?>" class="action-link text-[#d97706] hover:text-amber-700">
                                <i class="ph ph-note-pencil text-base"></i> Edit
                            </a>
                            <form method="POST" action="" onsubmit="return confirm('Batalkan pesanan ini?')">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <?php echo csrfField(); ?>
                                <button type="submit" class="action-link text-[#E02424] hover:text-dark bg-transparent border-0 cursor-pointer p-0 font-inherit text-[13px]">
                                    <i class="ph ph-trash text-base"></i> Hapus
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                        <i class="ph-bold ph-bag text-gray-300 text-3xl block mb-2"></i>
                        Belum ada pesanan
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="px-6 py-4 border-t border-gray-50 flex flex-wrap items-center justify-between text-[13px] text-gray-500 gap-4">
        <div id="results-count">
            Menampilkan <?php echo $totalOrders; ?> pesanan
        </div>
    </div>
</div>

<script>
    function filterTable() {
        var query = document.getElementById('search-input').value.toLowerCase();
        var rows = document.querySelectorAll('.order-row');
        var visible = 0;
        rows.forEach(function(row) {
            var match = row.getAttribute('data-search').indexOf(query) !== -1;
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('results-count').textContent =
            visible === rows.length
                ? 'Menampilkan <?php echo $totalOrders; ?> pesanan'
                : 'Menampilkan ' + visible + ' dari <?php echo $totalOrders; ?> pesanan';
    }
</script>

<?php include __DIR__ . '/../includes/footer-customer.php'; ?>
