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

$stats = [
    'batch_aktif'   => ['label' => 'Batch Aktif',    'value' => $activeBatch ? $activeBatch['name'] : 'Tidak ada', 'sub' => $activeBatch ? FormatHelper::tanggal($activeBatch['event_date']) : 'Tidak ada batch aktif', 'color' => 'red'],
    'order_batch'   => ['label' => 'Order Batch Ini','value' => $activeBatch ? (string) $adminService->getBatchOrderCount($activeBatch['id']) : '0', 'sub' => 'Total pesanan di batch aktif', 'color' => 'blue'],
    'siap_ambil'    => ['label' => 'Siap Diambil',   'value' => (string) $dbStats['siap_ambil'], 'sub' => $dbStats['siap_ambil'] > 0 ? 'Pesanan siap diambil' : 'Tidak ada pesanan', 'color' => 'teal'],
    'total_pelanggan' => ['label' => 'Total Pelanggan','value' => (string) $dbStats['total_pelanggan'], 'sub' => 'Pelanggan terdaftar', 'color' => 'green'],
    'total_omset'   => ['label' => 'Total Omset',    'value' => FormatHelper::rupiah($dbStats['total_omset']), 'sub' => 'Revenue keseluruhan', 'color' => 'indigo'],
    'total_profit'  => ['label' => 'Total Profit',   'value' => FormatHelper::rupiah($dbStats['total_profit']), 'sub' => 'Keuntungan bersih', 'color' => 'red'],
];

$statusMap = [
    'processing' => ['label' => 'Processing', 'class' => 'badge-processing'],
    'pending'    => ['label' => 'Pending',    'class' => 'badge-pending'],
    'ready'      => ['label' => 'Ready',      'class' => 'badge-ready'],
    'picked_up'  => ['label' => 'Picked Up',  'class' => 'badge-pickedup'],
];
?>

<style>
/* ── Stat Cards ── */
.stat-card { background:#fff; border:1px solid #f1f5f9; border-radius:12px; padding:20px 24px; position:relative; overflow:hidden; }
.stat-wave { position:absolute; bottom:0; left:0; right:0; height:36px; opacity:.25; }

/* ── Badges ── */
.badge-processing { background:#eff6ff; color:#2563eb; border:1px solid #dbeafe; }
.badge-pending    { background:#fffbeb; color:#d97706; border:1px solid #fde68a; }
.badge-ready      { background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; }
.badge-pickedup   { background:#f9fafb; color:#6b7280; border:1px solid #e5e7eb; }

.status-badge { display:inline-flex; align-items:center; gap:4px; padding:2px 9px; border-radius:999px; font-size:11px; font-weight:600; }
.dot-status   { width:6px; height:6px; border-radius:50%; background:currentColor; flex-shrink:0; }

/* ── Tables ── */
.data-table { width:100%; text-align:left; font-size:12.5px; border-collapse:collapse; }
.data-table th { font-size:11.5px; font-weight:600; color:#9ca3af; padding:10px 14px; border-bottom:1px solid #f1f5f9; white-space:nowrap; }
.data-table td { padding:12px 14px; border-bottom:1px solid #f8fafc; color:#374151; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tbody tr:hover td { background:#fafafa; }

/* ── Sections ── */
.section-card { background:#fff; border:1px solid #f1f5f9; border-radius:12px; overflow:hidden; margin-bottom:20px; }
.section-head { padding:14px 18px; border-bottom:1px solid #f8fafc; font-size:13px; font-weight:700; color:#111827; }

/* ── Search box ── */
.mini-search { border:1px solid #e5e7eb; border-radius:7px; padding:6px 10px 6px 30px; font-size:12px; outline:none; background:#f9fafb; width:190px; transition:border-color .15s; }
.mini-search:focus { border-color:#E02424; background:#fff; }
</style>

<!-- ── Welcome Banner ── -->
<div class="bg-white border border-gray-100 rounded-xl px-6 py-4 mb-5 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-navy flex items-center justify-center text-white text-[13px] font-bold">SA</div>
        <div>
            <p class="text-[14px] font-bold text-navy">Welcome</p>
            <p class="text-[11px] text-gray-400"><?php echo htmlspecialchars(isSuperAdmin() ? 'Super Admin' : 'Teknisi'); ?></p>
        </div>
    </div>
    <a href="../auth/logout.php" class="inline-flex items-center gap-1.5 text-[12px] text-gray-400 hover:text-navy border border-gray-200 px-3 py-1.5 rounded-lg transition-colors">
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
    <div class="stat-card">
        <p class="text-[11px] font-semibold text-gray-400 flex items-center gap-1.5 mb-2">
            <i class="ph ph-chart-line text-sm" style="color:<?php echo $wc;?>"></i>
            <?php echo $s['label']; ?>
        </p>
        <p class="text-[22px] font-extrabold text-navy mb-0.5"><?php echo $s['value']; ?></p>
        <p class="text-[11px]" style="color:<?php echo $wc;?>"><?php echo $s['sub']; ?></p>
        <!-- fake sparkline wave -->
        <svg class="stat-wave" viewBox="0 0 300 36" preserveAspectRatio="none">
            <path d="M0,18 C50,5 100,30 150,15 C200,0 250,28 300,18" stroke="<?php echo $wc;?>" stroke-width="2" fill="none"/>
        </svg>
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
    <div class="stat-card">
        <p class="text-[11px] font-semibold text-gray-400 flex items-center gap-1.5 mb-2">
            <i class="ph ph-trend-up text-sm" style="color:<?php echo $wc;?>"></i>
            <?php echo $s['label']; ?>
        </p>
        <p class="text-[22px] font-extrabold text-navy mb-0.5"><?php echo $s['value']; ?></p>
        <p class="text-[11px]" style="color:<?php echo $wc;?>"><?php echo $s['sub']; ?></p>
        <svg class="stat-wave" viewBox="0 0 300 36" preserveAspectRatio="none">
            <path d="M0,22 C60,8 120,28 180,12 C240,0 270,24 300,16" stroke="<?php echo $wc;?>" stroke-width="2" fill="none"/>
        </svg>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Ringkasan Produksi Batch Aktif ── -->
<div class="section-card">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
        <p class="text-[13px] font-bold text-navy">Ringkasan Produksi Batch Aktif</p>
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="mini-search">
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Pesanan</th>
                <th>Pelanggan</th>
                <th>Status</th>
                <th>Kode Pickup</th>
                <th>Diambil</th>
                <th>Tanggal Order</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($batch_orders as $row): $st = $statusMap[$row['status']] ?? ['label'=>$row['status'],'class'=>'badge-pending']; ?>
            <tr>
                <td class="font-semibold text-navy"><?php echo htmlspecialchars($row['order_number']); ?></td>
                <td class="text-gray-500"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td>
                    <span class="status-badge <?php echo $st['class']; ?>">
                        <span class="dot-status"></span><?php echo $st['label']; ?>
                    </span>
                </td>
                <td class="font-mono text-[12px] text-gray-500"><?php echo htmlspecialchars($row['pickup_code']); ?></td>
                <td class="text-primary text-[12px] font-semibold"><?php echo $row['picked_up_at'] ? FormatHelper::tanggal($row['picked_up_at']) : 'Belum'; ?></td>
                <td class="text-gray-400"><?php echo FormatHelper::tanggal($row['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ── Ringkasan Produk Batch Aktif ── -->
<div class="section-card">
    <div class="px-5 py-3.5 border-b border-gray-50">
        <p class="text-[13px] font-bold text-navy">Ringkasan Produk – Batch Aktif</p>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>SKU</th>
                <th>Total Kuantitas <i class="ph ph-caret-down text-[10px]"></i></th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($batch_products as $prod): ?>
            <tr>
                <td class="font-semibold" style="color:#E02424"><?php echo htmlspecialchars($prod['produk']); ?></td>
                <td class="text-gray-400 font-mono text-[12px]"><?php echo htmlspecialchars($prod['sku']); ?></td>
                <td class="font-bold text-navy"><?php echo $prod['qty']; ?></td>
                <td class="text-gray-500"><?php echo $prod['satuan']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ── Pesanan Terbaru ── -->
<div class="section-card">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
        <p class="text-[13px] font-bold text-navy">Pesanan Terbaru</p>
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="mini-search">
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Pesanan</th>
                <th>Pelanggan</th>
                <th>Batch</th>
                <th>Status</th>
                <th>Total</th>
                <th>Tanggal <i class="ph ph-caret-down text-[10px]"></i></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_orders as $row): $st = $statusMap[$row['status']] ?? ['label'=>$row['status'],'class'=>'badge-pending']; ?>
            <tr>
                <td class="font-bold text-navy"><?php echo htmlspecialchars($row['order_number']); ?></td>
                <td>
                    <div class="font-semibold text-[12px]" style="color:#E02424"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                    <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($row['customer_phone'] ?? '-'); ?></div>
                </td>
                <td>
                    <span class="text-[11px] bg-blue-50 text-blue-600 border border-blue-100 px-2 py-0.5 rounded font-semibold"><?php echo htmlspecialchars($row['batch_name']); ?></span>
                </td>
                <td>
                    <span class="status-badge <?php echo $st['class']; ?>">
                        <span class="dot-status"></span><?php echo $st['label']; ?>
                    </span>
                </td>
                <td class="font-semibold" style="color:#E02424"><?php echo FormatHelper::rupiah($row['total_amount']); ?></td>
                <td class="text-primary font-semibold text-[12px]"><?php echo FormatHelper::tanggal($row['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
