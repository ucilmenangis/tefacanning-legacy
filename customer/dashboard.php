<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../classes/OrderService.php";
require_once __DIR__ . "/../classes/FormatHelper.php";
requireCustomer();

$customerId = getCustomerId();

// Customer info
$customer = db_fetch(
  "SELECT name, email, phone, organization, created_at FROM customers WHERE id = ? AND deleted_at IS NULL",
  [$customerId],
);

if (!$customer) {
  logoutCustomer();
  header("Location: login-customer.php");
  exit();
}

// Order stats via OOP
$orderService = new OrderService();
$stats = $orderService->getStats($customerId);
$totalOrders = $stats["total_orders"];
$totalSpent = $stats["total_spent"];
$pendingOrders = $stats["pending_count"];
$readyOrders = $stats["ready_count"];

// Latest open batch
$latestBatch = db_fetch(
  "SELECT b.id, b.name, b.event_name, b.event_date, b.status,
            (SELECT COUNT(*) FROM orders WHERE batch_id = b.id AND deleted_at IS NULL) as order_count
     FROM batches b
     WHERE b.deleted_at IS NULL AND b.status = 'open'
     ORDER BY b.created_at DESC LIMIT 1",
);

// Active products
$products = db_fetch_all(
  "SELECT name, sku, price FROM products WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name",
);

// Monthly order counts (last 6 months) for sparkline
$monthlyOrders = db_fetch_all(
  "SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
          COUNT(*) as total,
          COALESCE(SUM(total_amount), 0) as amount
   FROM orders
   WHERE customer_id = ? AND deleted_at IS NULL
     AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
   GROUP BY DATE_FORMAT(created_at, '%Y-%m')
   ORDER BY month ASC",
  [$customerId],
);

// Build sparkline data arrays
$sparkOrders = array_column($monthlyOrders, 'total');
$sparkAmounts = array_map(function($v) { return (float) $v; }, array_column($monthlyOrders, 'amount'));

// Monthly pending count
$sparkPending = [];
$pendingRows = db_fetch_all(
  "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total
   FROM orders WHERE customer_id = ? AND status = 'pending' AND deleted_at IS NULL
     AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
   GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month ASC",
  [$customerId],
);
$sparkPending = array_column($pendingRows, 'total');

// Monthly ready count
$readyRows = db_fetch_all(
  "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total
   FROM orders WHERE customer_id = ? AND status = 'ready' AND deleted_at IS NULL
     AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
   GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month ASC",
  [$customerId],
);
$sparkReady = array_column($readyRows, 'total');

$pageTitle = "Dashboard";
$currentPage = "dashboard";
include __DIR__ . "/../includes/header-customer.php";
?>

<!-- Page Title -->
<h1 class="text-[22px] font-bold text-navy mb-6">Dashboard</h1>

<!-- ── Welcome Card ── -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-5">
    <p class="text-[15px] font-semibold text-navy mb-0.5">
        Selamat datang, <?php echo htmlspecialchars($customer["name"]); ?>! 👋
    </p>
    <p class="text-[12px] text-gray-400 mb-3">
        Kelola pre-order sarden kaleng TEFA Anda dari sini.
    </p>
    <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 text-[11px] text-gray-400">
        <?php if ($customer["organization"]): ?>
        <span class="flex items-center gap-1.5">
            <i class="ph-bold ph-building-office text-gray-300"></i>
            <?php echo htmlspecialchars($customer["organization"]); ?>
        </span>
        <?php endif; ?>
        <span class="flex items-center gap-1.5">
            <i class="ph-bold ph-phone text-gray-300"></i>
            <?php echo htmlspecialchars($customer["phone"]); ?>
        </span>
        <span class="flex items-center gap-1.5">
            <i class="ph-bold ph-envelope text-gray-300"></i>
            <?php echo htmlspecialchars($customer["email"]); ?>
        </span>
        <span class="flex items-center gap-1.5">
            <i class="ph-bold ph-calendar-blank text-gray-300"></i>
            Member sejak <?php echo date(
              "d M Y",
              strtotime($customer["created_at"]),
            ); ?>
        </span>
    </div>
</div>

<!-- ── Stats Row ── -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

    <!-- Total Pesanan -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 overflow-hidden">
        <div class="flex items-center gap-2 mb-3">
            <i class="ph-bold ph-bag-simple text-gray-300 text-[15px]"></i>
            <span class="text-[11px] font-medium text-gray-400">Total Pesanan</span>
        </div>
        <div class="text-[32px] font-extrabold text-navy leading-none mb-2"><?php echo $totalOrders; ?></div>
        <p class="text-[11px] font-medium text-[#E02424] mb-3">Pesanan keseluruhan</p>
        <div style="height:32px;"><canvas id="spark-orders"></canvas></div>
    </div>

    <!-- Total Belanja -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 overflow-hidden">
        <div class="flex items-center gap-2 mb-3">
            <i class="ph-bold ph-currency-circle-dollar text-gray-300 text-[15px]"></i>
            <span class="text-[11px] font-medium text-gray-400">Total Belanja</span>
        </div>
        <div class="text-[22px] font-extrabold text-navy leading-none mb-2">
            <?php echo FormatHelper::rupiah($totalSpent); ?>
        </div>
        <p class="text-[11px] font-medium text-emerald-500 mb-3">Akumulasi pengeluaran</p>
        <div style="height:32px;"><canvas id="spark-spent"></canvas></div>
    </div>

    <!-- Menunggu Konfirmasi -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 overflow-hidden">
        <div class="flex items-center gap-2 mb-3">
            <i class="ph-bold ph-clock text-gray-300 text-[15px]"></i>
            <span class="text-[11px] font-medium text-gray-400">Menunggu Konfirmasi</span>
        </div>
        <div class="text-[32px] font-extrabold text-navy leading-none mb-2"><?php echo $pendingOrders; ?></div>
        <p class="text-[11px] font-medium text-amber-500 mb-3">Pesanan belum diproses</p>
        <div style="height:32px;"><canvas id="spark-pending"></canvas></div>
    </div>

    <!-- Siap Diambil -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 overflow-hidden">
        <div class="flex items-center gap-2 mb-3">
            <i class="ph-bold ph-package text-gray-300 text-[15px]"></i>
            <span class="text-[11px] font-medium text-gray-400">Siap Diambil</span>
        </div>
        <div class="text-[32px] font-extrabold text-navy leading-none mb-2"><?php echo $readyOrders; ?></div>
        <p class="text-[11px] font-medium text-gray-400 mb-3"><?php echo $readyOrders >
        0
          ? "Segera ambil pesanan"
          : "Belum ada pesanan siap"; ?></p>
        <div style="height:32px;"><canvas id="spark-ready"></canvas></div>
    </div>

</div>

<!-- ── Bottom: Batch + Products ── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    <!-- Batch Produksi Terbaru -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
            <i class="ph-bold ph-calendar-dots text-gray-300 text-base"></i>
            <h2 class="text-[13px] font-semibold text-navy">Batch Produksi Terbaru</h2>
        </div>

        <div class="p-5">
            <?php if ($latestBatch): ?>
            <div class="bg-gray-50 rounded-xl border border-gray-100 p-4">
                <?php $bs = FormatHelper::batchStatus(
                  $latestBatch["status"],
                ); ?>
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[13px] font-semibold text-navy"><?php echo htmlspecialchars(
                      $latestBatch["name"],
                    ); ?></span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold
                                 text-<?php echo $bs[
                                   "color"
                                 ]; ?>-600 bg-<?php echo $bs[
  "color"
]; ?>-50 border border-<?php echo $bs["color"]; ?>-100
                                 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 bg-<?php echo $bs[
                          "color"
                        ]; ?>-500 rounded-full"></span>
                        <?php echo $bs["label"]; ?>
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Acara</p>
                        <p class="text-[12px] font-semibold text-[#E02424]"><?php echo htmlspecialchars(
                          $latestBatch["event_name"],
                        ); ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Tanggal</p>
                        <p class="text-[12px] font-semibold text-navy"><?php echo date(
                          "d M Y",
                          strtotime($latestBatch["event_date"]),
                        ); ?></p>
                    </div>
                </div>

                <a href="preorder.php"
                   class="inline-flex items-center gap-1.5 text-[11px] font-medium text-gray-500 hover:text-[#E02424] transition-colors">
                    <i class="ph-bold ph-bag-simple text-sm"></i>
                    <?php echo $latestBatch["order_count"]; ?> pesanan masuk
                </a>
            </div>
            <?php else: ?>
            <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 text-center">
                <i class="ph-bold ph-calendar-x text-gray-300 text-2xl mb-2"></i>
                <p class="text-[12px] text-gray-400">Belum ada batch yang aktif</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Produk Tersedia -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
            <i class="ph-bold ph-storefront text-gray-300 text-base"></i>
            <h2 class="text-[13px] font-semibold text-navy">Produk Tersedia</h2>
        </div>

        <div class="divide-y divide-gray-50">
            <?php foreach ($products as $product): ?>
            <div class="flex items-center gap-3 px-5 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-gray-50 border border-gray-100
                            flex items-center justify-center flex-shrink-0">
                    <i class="ph-bold ph-gear text-gray-300 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12px] font-semibold text-[#E02424] truncate"><?php echo htmlspecialchars(
                      $product["name"],
                    ); ?></p>
                    <p class="text-[10px] text-gray-400"><?php echo htmlspecialchars(
                      $product["sku"],
                    ); ?></p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-[12px] font-bold text-navy"><?php echo FormatHelper::rupiah(
                      $product["price"],
                    ); ?></p>
                    <p class="text-[10px] text-gray-400">per kaleng</p>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($products)): ?>
            <div class="px-5 py-6 text-center">
                <i class="ph-bold ph-storefront text-gray-300 text-2xl mb-2"></i>
                <p class="text-[12px] text-gray-400">Belum ada produk tersedia</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /bottom grid -->

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
sparkline('spark-orders', <?php echo json_encode(array_map('intval', $sparkOrders)); ?>, '#E02424');
sparkline('spark-spent', <?php echo json_encode($sparkAmounts); ?>, '#10b981');
sparkline('spark-pending', <?php echo json_encode(array_map('intval', $sparkPending)); ?>, '#f59e0b');
sparkline('spark-ready', <?php echo json_encode(array_map('intval', $sparkReady)); ?>, '#6b7280');
</script>

<?php include __DIR__ . "/../includes/footer-customer.php"; ?>
