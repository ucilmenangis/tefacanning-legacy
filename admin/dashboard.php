<?php
$pageTitle   = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
include __DIR__ . '/../includes/header-admin.php';

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$adminService = new AdminService();
$dbStats = $adminService->getDashboardStats();
$activeBatch = $adminService->getActiveBatch();
$batch_orders = $activeBatch ? $adminService->getBatchOrders($activeBatch['id']) : [];
$batch_products = $activeBatch ? $adminService->getBatchProducts($activeBatch['id']) : [];
$recent_orders = $adminService->getRecentOrders(5);

// Monthly data for sparkline charts (last 6 months)
$sparkOrders = db_fetch_all(
    "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total
     FROM orders WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY month ORDER BY month ASC"
);
$sparkOmset = db_fetch_all(
    "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COALESCE(SUM(total_amount), 0) as amount
     FROM orders WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY month ORDER BY month ASC"
);
$sparkReady = db_fetch_all(
    "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total
     FROM orders WHERE status = 'ready' AND deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY month ORDER BY month ASC"
);
$sparkCustomers = db_fetch_all(
    "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total
     FROM customers WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY month ORDER BY month ASC"
);
$sparkProfit = db_fetch_all(
    "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COALESCE(SUM(profit), 0) as amount
     FROM orders WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY month ORDER BY month ASC"
);

$stats = [
    'batch_aktif'   => ['label' => 'Batch Aktif',    'value' => $activeBatch ? $activeBatch['name'] : 'Tidak ada', 'sub' => $activeBatch ? FormatHelper::tanggal($activeBatch['event_date']) : 'Tidak ada batch aktif', 'color' => 'red'],
    'order_batch'   => ['label' => 'Order Batch Ini','value' => $activeBatch ? (string) $adminService->getBatchOrderCount($activeBatch['id']) : '0', 'sub' => 'Total pesanan di batch aktif', 'color' => 'blue'],
    'siap_ambil'    => ['label' => 'Siap Diambil',   'value' => (string) $dbStats['siap_ambil'], 'sub' => $dbStats['siap_ambil'] > 0 ? 'Pesanan siap diambil' : 'Tidak ada pesanan', 'color' => 'teal'],
    'total_pelanggan' => ['label' => 'Total Pelanggan','value' => (string) $dbStats['total_pelanggan'], 'sub' => 'Pelanggan terdaftar', 'color' => 'green'],
    'total_omset'   => ['label' => 'Total Omset',    'value' => FormatHelper::rupiah($dbStats['total_omset']), 'sub' => 'Revenue keseluruhan', 'color' => 'indigo'],
    'total_profit'  => ['label' => 'Total Profit',   'value' => FormatHelper::rupiah($dbStats['total_profit']), 'sub' => 'Keuntungan bersih', 'color' => 'red'],
];

$statusMap = [
    'processing' => ['label' => 'Processing', 'class' => 'bg-amber-50 text-amber-600 border border-amber-100'],
    'pending'    => ['label' => 'Pending',    'class' => 'bg-slate-50 text-slate-500 border border-slate-100'],
    'ready'      => ['label' => 'Ready',      'class' => 'bg-emerald-50 text-emerald-600 border border-emerald-100'],
    'picked_up'  => ['label' => 'Picked Up',  'class' => 'bg-blue-50 text-blue-600 border border-blue-100'],
];
?>



<!-- ── Welcome Banner ── -->
<div class="bg-white border border-gray-100 rounded-xl px-6 py-4 mb-5 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-navy flex items-center justify-center text-white text-[13px] font-bold">SA</div>
        <div>
            <p class="text-[14px] font-bold text-navy">Welcome</p>
            <p class="text-[11px] text-gray-400"><?php echo htmlspecialchars(isSuperAdmin() ? 'Super Admin' : 'Teknisi'); ?></p>
        </div>
    </div>
    <a href="../auth/logout.php?type=admin" class="inline-flex items-center gap-1.5 text-[12px] text-gray-400 hover:text-navy border border-gray-200 px-3 py-1.5 rounded-lg transition-colors">
        <i class="ph ph-sign-out"></i> Sign out
    </a>
</div>

<!-- ── Stat Cards Row 1 ── -->
<div class="grid grid-cols-3 gap-4 mb-4">
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
        <div style="height:32px; margin-top:8px;"><canvas id="spark-<?php echo $key; ?>"></canvas></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Stat Cards Row 2 ── -->
<div class="grid grid-cols-3 gap-4 mb-6">
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
        <div style="height:32px; margin-top:8px;"><canvas id="spark-<?php echo $key; ?>"></canvas></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Ringkasan Produksi Batch Aktif ── -->
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden mb-5">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
        <p class="text-[13px] font-bold text-navy">Ringkasan Produksi Batch Aktif</p>
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="border border-gray-200 rounded-lg py-1.5 pl-[30px] pr-3 text-[12px] outline-none bg-gray-50 w-[190px] transition-colors focus:border-primary focus:bg-white">
        </div>
    </div>
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

<!-- ── Ringkasan Produk Batch Aktif ── -->
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden mb-5">
    <div class="px-5 py-3.5 border-b border-gray-50">
        <p class="text-[13px] font-bold text-navy">Ringkasan Produk – Batch Aktif</p>
    </div>
    <table class="w-full text-left text-[12.5px] border-collapse">
        <thead>
            <tr>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Produk</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">SKU</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Total Kuantitas <i class="ph ph-caret-down text-[10px]"></i></th>
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

<!-- ── Pesanan Terbaru ── -->
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden mb-5">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
        <p class="text-[13px] font-bold text-navy">Pesanan Terbaru</p>
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="border border-gray-200 rounded-lg py-1.5 pl-[30px] pr-3 text-[12px] outline-none bg-gray-50 w-[190px] transition-colors focus:border-primary focus:bg-white">
        </div>
    </div>
    <table class="w-full text-left text-[12.5px] border-collapse">
        <thead>
            <tr>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">No. Pesanan</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Pelanggan</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Batch</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Status</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Total</th>
                <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap">Tanggal <i class="ph ph-caret-down text-[10px]"></i></th>
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
                    <span class="text-[11px] bg-blue-50 text-blue-600 border border-blue-100 px-2 py-0.5 rounded font-semibold"><?php echo htmlspecialchars($row['batch_name']); ?></span>
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
// Build sparkline data arrays from monthly queries
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
