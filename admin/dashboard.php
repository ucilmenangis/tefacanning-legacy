<?php
$pageTitle   = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$adminService = new AdminService();

// ── Batch filter logic ──
$selectedBatchId = isset($_GET['batch_id']) && $_GET['batch_id'] !== '' ? (int) $_GET['batch_id'] : null;
$allBatches = $adminService->getAllBatchesForDropdown();

if ($selectedBatchId) {
    // Filtered by specific batch
    $batchInfo = null;
    foreach ($allBatches as $b) {
        if ($b['id'] === $selectedBatchId) { $batchInfo = $b; break; }
    }
    $dbStats = $adminService->getDashboardStatsByBatch($selectedBatchId);
    $batch_orders = $adminService->getBatchOrders($selectedBatchId);
    $batch_products = $adminService->getBatchProducts($selectedBatchId);
    $recent_orders = $adminService->getRecentOrdersByBatch(5, $selectedBatchId);

    $stats = [
        'batch_aktif'   => ['label' => 'Batch', 'value' => $batchInfo ? $batchInfo['name'] : 'Tidak ditemukan', 'sub' => $batchInfo ? ucfirst($batchInfo['status']) : '-', 'color' => 'red'],
        'order_batch'   => ['label' => 'Total Pesanan', 'value' => (string) ($dbStats['order_batch'] ?? 0), 'sub' => 'Pesanan di batch ini', 'color' => 'blue'],
        'siap_ambil'    => ['label' => 'Siap Diambil',   'value' => (string) $dbStats['siap_ambil'], 'sub' => $dbStats['siap_ambil'] > 0 ? 'Pesanan siap diambil' : 'Tidak ada pesanan', 'color' => 'teal'],
        'total_pelanggan' => ['label' => 'Pelanggan Batch','value' => (string) $dbStats['total_pelanggan'], 'sub' => 'Pelanggan di batch ini', 'color' => 'green'],
        'total_omset'   => ['label' => 'Omset Batch',    'value' => FormatHelper::rupiah($dbStats['total_omset']), 'sub' => 'Revenue batch ini', 'color' => 'indigo'],
        'total_profit'  => ['label' => 'Profit Batch',   'value' => FormatHelper::rupiah($dbStats['total_profit']), 'sub' => 'Keuntungan batch ini', 'color' => 'red'],
    ];
} else {
    // All batches (total)
    $activeBatch = $adminService->getActiveBatch();
    $dbStats = $adminService->getDashboardStats();
    $batch_orders = $adminService->getAllBatchOrders();
    $batch_products = $adminService->getAllBatchProducts();
    $recent_orders = $adminService->getRecentOrders(5);

    $stats = [
        'batch_aktif'   => ['label' => 'Batch Aktif',    'value' => $activeBatch ? $activeBatch['name'] : 'Tidak ada', 'sub' => $activeBatch ? FormatHelper::tanggal($activeBatch['event_date']) : 'Tidak ada batch aktif', 'color' => 'red'],
        'order_batch'   => ['label' => 'Order Batch Ini','value' => $activeBatch ? (string) $adminService->getBatchOrderCount($activeBatch['id']) : '0', 'sub' => 'Total pesanan di batch aktif', 'color' => 'blue'],
        'siap_ambil'    => ['label' => 'Siap Diambil',   'value' => (string) $dbStats['siap_ambil'], 'sub' => $dbStats['siap_ambil'] > 0 ? 'Pesanan siap diambil' : 'Tidak ada pesanan', 'color' => 'teal'],
        'total_pelanggan' => ['label' => 'Total Pelanggan','value' => (string) $dbStats['total_pelanggan'], 'sub' => 'Pelanggan terdaftar', 'color' => 'green'],
        'total_omset'   => ['label' => 'Total Omset',    'value' => FormatHelper::rupiah($dbStats['total_omset']), 'sub' => 'Revenue keseluruhan', 'color' => 'indigo'],
        'total_profit'  => ['label' => 'Total Profit',   'value' => FormatHelper::rupiah($dbStats['total_profit']), 'sub' => 'Keuntungan bersih', 'color' => 'red'],
    ];
}

// Monthly data for sparkline charts (last 6 months)
$sparkOrders = Database::getInstance()->fetchAll("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total FROM orders WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month ASC");
$sparkOmset = Database::getInstance()->fetchAll("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COALESCE(SUM(total_amount), 0) as amount FROM orders WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month ASC");
$sparkReady = Database::getInstance()->fetchAll("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total FROM orders WHERE status = 'ready' AND deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month ASC");
$sparkCustomers = Database::getInstance()->fetchAll("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total FROM customers WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month ASC");
$sparkProfit = Database::getInstance()->fetchAll("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COALESCE(SUM(total_amount), 0) as amount FROM orders WHERE status = 'picked_up' AND deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month ASC");

$statusMap = [
    'processing' => ['label' => 'Processing', 'class' => 'bg-amber-50 text-amber-600 border border-amber-100'],
    'pending'    => ['label' => 'Pending',    'class' => 'bg-slate-50 text-slate-500 border border-slate-100'],
    'ready'      => ['label' => 'Ready',      'class' => 'bg-emerald-50 text-emerald-600 border border-emerald-100'],
    'picked_up'  => ['label' => 'Picked Up',  'class' => 'bg-blue-50 text-blue-600 border border-blue-100'],
];

// Section titles
$productionTitle = $selectedBatchId ? 'Ringkasan Produksi – ' . ($batchInfo['name'] ?? 'Batch') : 'Ringkasan Produksi – Semua Batch';
$productTitle = $selectedBatchId ? 'Ringkasan Produk – ' . ($batchInfo['name'] ?? 'Batch') : 'Ringkasan Produk – Semua Batch';

include __DIR__ . '/../includes/header-admin.php';
?>



<!-- ── Welcome Banner + Batch Filter ── -->
<div class="bg-white border border-gray-100 rounded-xl px-4 sm:px-6 py-4 mb-5 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 shadow-sm">
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-navy flex items-center justify-center text-white text-[13px] font-bold">SA</div>
        <div>
            <p class="text-[14px] font-bold text-navy">Welcome</p>
            <p class="text-[11px] text-gray-400"><?php echo htmlspecialchars(Auth::admin()->isSuperAdmin() ? 'Super Admin' : 'Teknisi'); ?></p>
        </div>
    </div>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <!-- Batch Filter -->
        <form method="GET" action="" class="flex flex-col sm:flex-row sm:items-center gap-2">
            <label class="text-[12px] text-gray-400 font-medium">Batch:</label>
            <select name="batch_id" onchange="this.form.submit()"
                class="border border-gray-200 rounded-lg py-1.5 px-3 text-[12px] outline-none bg-white appearance-none cursor-pointer pr-7 transition-colors focus:border-primary">
                <option value="" <?php echo !$selectedBatchId ? 'selected' : ''; ?>>Semua Batch (Total)</option>
                <?php foreach ($allBatches as $b): ?>
                    <option value="<?php echo $b['id']; ?>" <?php echo $selectedBatchId === $b['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($b['name']); ?> (<?php echo ucfirst($b['status']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="../auth/logout.php?type=admin" class="inline-flex items-center gap-1.5 text-[12px] text-gray-400 hover:text-navy border border-gray-200 px-3 py-1.5 rounded-lg transition-colors">
            <i class="ph ph-sign-out"></i> Sign out
        </a>
    </div>
</div>

<!-- ── Stat Cards Row 1 ── -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
    <?php
    $row1 = ['batch_aktif','order_batch','siap_ambil'];
    $waveColors = ['red'=>'#E02424','blue'=>'#2563eb','teal'=>'#0d9488','green'=>'#059669','indigo'=>'#4f46e5'];
    foreach ($row1 as $key):
        $s = $stats[$key];
        $wc = $waveColors[$s['color']] ?? '#E02424';
    ?>
<div class="bg-white border border-gray-100 rounded-xl p-5 relative overflow-hidden">
        <p class="text-[11px] font-semibold text-gray-400 flex items-center gap-1.5 mb-2">
            <i class="ph ph-chart-line text-sm" style="color:<?php echo $wc;?>"></i>
            <?php echo $s['label']; ?>
        </p>
        <p class="text-[22px] font-extrabold text-navy mb-0.5"><?php echo $s['value']; ?></p>
        <p class="text-[11px]" style="color:<?php echo $wc;?>"><?php echo $s['sub']; ?></p>
        <div class="hidden sm:block" style="height:32px; margin-top:8px;"><canvas id="spark-<?php echo $key; ?>"></canvas></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Stat Cards Row 2 ── -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <?php
    $row2 = ['total_pelanggan','total_omset','total_profit'];
    foreach ($row2 as $key):
        $s = $stats[$key];
        $wc = $waveColors[$s['color']] ?? '#E02424';
    ?>
<div class="bg-white border border-gray-100 rounded-xl p-5 relative overflow-hidden">
        <p class="text-[11px] font-semibold text-gray-400 flex items-center gap-1.5 mb-2">
            <i class="ph ph-trend-up text-sm" style="color:<?php echo $wc;?>"></i>
            <?php echo $s['label']; ?>
        </p>
        <p class="text-[22px] font-extrabold text-navy mb-0.5"><?php echo $s['value']; ?></p>
        <p class="text-[11px]" style="color:<?php echo $wc;?>"><?php echo $s['sub']; ?></p>
        <div class="hidden sm:block" style="height:32px; margin-top:8px;"><canvas id="spark-<?php echo $key; ?>"></canvas></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Ringkasan Produksi ── -->
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden mb-5">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
        <p class="text-[13px] font-bold text-navy"><?php echo $productionTitle; ?></p>
    </div>
    <?php if (empty($batch_orders)): ?>
        <div class="py-10 text-center text-gray-400 text-[13px]">
            <i class="ph ph-package text-2xl block mb-2 text-gray-300"></i>
            Tidak ada pesanan
        </div>
    <?php else: ?>
    <div class="overflow-x-auto">
    <table class="w-full text-left text-[12.5px] border-collapse">
        <thead>
            <tr>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">No. Pesanan</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Pelanggan</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Status</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Kode Pickup</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Diambil</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Tanggal Order</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($batch_orders as $row): $st = $statusMap[$row['status']] ?? ['label'=>$row['status'],'class'=>'badge-pending']; ?>
            <tr>
                <td class="font-semibold text-navy px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo htmlspecialchars($row['order_number']); ?></td>
                <td class="text-gray-500 px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td class="px-3.5 py-3 border-b border-gray-50 align-middle">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold <?php echo $st['class']; ?>">
                        <span class="w-1.5 h-1.5 rounded-full bg-current flex-shrink-0"></span><?php echo $st['label']; ?>
                    </span>
                </td>
                <td class="font-mono text-[12px] text-gray-500 px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo htmlspecialchars($row['pickup_code']); ?></td>
                <td class="text-primary text-[12px] font-semibold px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo $row['picked_up_at'] ? FormatHelper::tanggal($row['picked_up_at']) : 'Belum'; ?></td>
                <td class="text-gray-400 px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo FormatHelper::tanggal($row['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<!-- ── Ringkasan Produk ── -->
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden mb-5">
    <div class="px-5 py-3.5 border-b border-gray-50">
        <p class="text-[13px] font-bold text-navy"><?php echo $productTitle; ?></p>
    </div>
    <?php if (empty($batch_products)): ?>
        <div class="py-10 text-center text-gray-400 text-[13px]">
            <i class="ph ph-package text-2xl block mb-2 text-gray-300"></i>
            Tidak ada produk
        </div>
    <?php else: ?>
    <div class="overflow-x-auto">
    <table class="w-full text-left text-[12.5px] border-collapse">
        <thead>
            <tr>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Produk</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">SKU</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Total Kuantitas</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Satuan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($batch_products as $prod): ?>
            <tr>
                <td class="font-semibold text-primary px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo htmlspecialchars($prod['produk']); ?></td>
                <td class="text-gray-400 font-mono text-[12px] px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo htmlspecialchars($prod['sku']); ?></td>
                <td class="font-bold text-navy px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo $prod['qty']; ?></td>
                <td class="text-gray-500 px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo $prod['satuan']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<!-- ── Pesanan Terbaru ── -->
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden mb-5">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
        <p class="text-[13px] font-bold text-navy"><?php echo $selectedBatchId ? 'Pesanan Batch Ini' : 'Pesanan Terbaru'; ?></p>
    </div>
    <?php if (empty($recent_orders)): ?>
        <div class="py-10 text-center text-gray-400 text-[13px]">
            <i class="ph ph-bag text-2xl block mb-2 text-gray-300"></i>
            Tidak ada pesanan
        </div>
    <?php else: ?>
    <div class="overflow-x-auto">
    <table class="w-full text-left text-[12.5px] border-collapse">
        <thead>
            <tr>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">No. Pesanan</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Pelanggan</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Batch</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Status</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Total</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_orders as $row): $st = $statusMap[$row['status']] ?? ['label'=>$row['status'],'class'=>'badge-pending']; ?>
            <tr>
                <td class="font-bold text-navy px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo htmlspecialchars($row['order_number']); ?></td>
                <td class="px-3.5 py-3 border-b border-gray-50 align-middle">
                    <div class="font-semibold text-[12px] text-primary"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                    <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($row['customer_phone'] ?? '-'); ?></div>
                </td>
                <td class="px-3.5 py-3 border-b border-gray-50 align-middle">
                    <span
    class="inline-flex items-center whitespace-nowrap text-1xl bg-blue-50 text-blue-600 border border-blue-100 px-2.5 py-1 rounded-md font-semibold">
    <?php echo htmlspecialchars($row['batch_name']); ?>
<!-- </span> fix it for small text responsive -->
                </td>
                <td class="px-3.5 py-3 border-b border-gray-50 align-middle">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold <?php echo $st['class']; ?>">
                        <span class="w-1.5 h-1.5 rounded-full bg-current flex-shrink-0"></span><?php echo $st['label']; ?>
                    </span>
                </td>
                <td class="font-semibold text-primary px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo FormatHelper::rupiah($row['total_amount']); ?></td>
                <td class="text-primary font-semibold text-[12px] px-3.5 py-3 border-b border-gray-50 align-middle"><?php echo FormatHelper::tanggal($row['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
function sparkline(id, data, color) {
    if (!data.length) data = [0];
    new Chart(document.getElementById(id), {
        type: 'line',
        data: {
            labels: data.map(function() { return ''; }),
            datasets: [{
                data: data,
                borderColor: color,
                borderWidth: 2,
                fill: true,
                backgroundColor: color + '15',
                pointRadius: 0,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } },
            elements: { line: { borderCapStyle: 'round' } }
        }
    });
}
<?php
$sparkOrdersData = array_map('intval', array_column($sparkOrders, 'total'));
$sparkOmsetData = array_map('floatval', array_column($sparkOmset, 'amount'));
$sparkReadyData = array_map('intval', array_column($sparkReady, 'total'));
$sparkCustomersData = array_map('intval', array_column($sparkCustomers, 'total'));
$sparkProfitData = array_map('floatval', array_column($sparkProfit, 'amount'));
?>
// Row 1
sparkline('spark-batch_aktif', <?php echo json_encode($sparkOrdersData); ?>, '#E02424');
sparkline('spark-order_batch', <?php echo json_encode($sparkOrdersData); ?>, '#2563eb');
sparkline('spark-siap_ambil', <?php echo json_encode($sparkReadyData); ?>, '#0d9488');
// Row 2
sparkline('spark-total_pelanggan', <?php echo json_encode($sparkCustomersData); ?>, '#059669');
sparkline('spark-total_omset', <?php echo json_encode($sparkOmsetData); ?>, '#4f46e5');
sparkline('spark-total_profit', <?php echo json_encode($sparkProfitData); ?>, '#E02424');
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
