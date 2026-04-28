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



<?php echo renderFlash(); ?>

<h1 class="text-[22px] font-bold text-navy mb-6">Riwayat Pesanan</h1>

<div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">

    <!-- Search -->
    <div class="p-4 flex justify-end border-b border-gray-50">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="search-input" placeholder="Cari no. pesanan..." class="border border-gray-200 rounded-lg py-2 pl-9 pr-3 text-[13px] text-gray-700 w-60 outline-none transition-colors focus:border-primary" oninput="filterTable()">
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
                        <div class="flex items-center gap-4 justify-end">
                            <a href="../api/download-pdf.php?id=<?php echo $order['id']; ?>" class="inline-flex items-center gap-1 transition-colors text-emerald-600 hover:text-emerald-700" title="Download PDF">
                                <i class="ph ph-file-pdf text-base"></i> PDF
                            </a>
                            <?php if ($order['status'] === 'pending'): ?>
                            <a href="edit-order.php?id=<?php echo $order['id']; ?>" class="inline-flex items-center gap-1 transition-colors text-amber-600 hover:text-amber-700">
                                <i class="ph ph-note-pencil text-base"></i> Edit
                            </a>
                            <form method="POST" action="" onsubmit="return confirm('Batalkan pesanan ini?')">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <?php echo csrfField(); ?>
                                <button type="submit" class="inline-flex items-center gap-1 transition-colors text-primary hover:text-dark bg-transparent border-0 cursor-pointer p-0 font-inherit text-[13px]">
                                    <i class="ph ph-trash text-base"></i> Hapus
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
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
