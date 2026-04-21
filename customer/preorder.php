<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireCustomer();

$customerId = getCustomerId();
$customer = db_fetch("SELECT name FROM customers WHERE id = ? AND deleted_at IS NULL", [$customerId]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batchId = (int) ($_POST['batch_id'] ?? 0);
    $notes   = trim($_POST['notes'] ?? '');
    $items   = $_POST['items'] ?? [];

    // Validate batch
    $batch = db_fetch(
        "SELECT id, name FROM batches WHERE id = ? AND status = 'open' AND deleted_at IS NULL",
        [$batchId]
    );

    $errors = [];
    if (!$batch) {
        $errors[] = 'Batch tidak valid atau sudah ditutup.';
    }
    if (empty($items)) {
        $errors[] = 'Pilih minimal 1 produk.';
    }

    // Validate items — price from DB, never trust client
    $validItems = [];
    foreach ($items as $item) {
        $productId = (int) ($item['product_id'] ?? 0);
        $quantity  = (int) ($item['quantity'] ?? 0);

        if ($productId <= 0 || $quantity < 100 || $quantity > 3000) {
            $errors[] = "Jumlah harus antara 100–3000 kaleng.";
            continue;
        }

        $product = db_fetch(
            "SELECT id, name, price FROM products WHERE id = ? AND is_active = 1 AND deleted_at IS NULL",
            [$productId]
        );

        if (!$product) {
            $errors[] = 'Produk tidak valid.';
            continue;
        }

        $validItems[] = [
            'product_id'  => $product['id'],
            'quantity'    => $quantity,
            'unit_price'  => $product['price'],
            'subtotal'    => $product['price'] * $quantity,
        ];
    }

    if (empty($errors) && !empty($validItems)) {
        $orderNumber = 'ORD-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $pickupCode  = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $totalAmount = array_sum(array_column($validItems, 'subtotal'));

        $orderId = db_insert(
            "INSERT INTO orders (customer_id, batch_id, order_number, pickup_code, status, total_amount, profit, notes, created_at, updated_at)
             VALUES (?, ?, ?, ?, 'pending', ?, 0, ?, NOW(), NOW())",
            [$customerId, $batchId, $orderNumber, $pickupCode, $totalAmount, $notes ?: null]
        );

        foreach ($validItems as $vi) {
            db_insert(
                "INSERT INTO order_product (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
                [$orderId, $vi['product_id'], $vi['quantity'], $vi['unit_price'], $vi['subtotal']]
            );
        }

        setFlash('success', 'Pre-Order berhasil dikirim! Order: ' . $orderNumber);
        header('Location: preorder.php');
        exit;
    }

    $formErrors = $errors;
}

// Data for form
$batches = db_fetch_all(
    "SELECT id, name, event_name, event_date FROM batches WHERE status = 'open' AND deleted_at IS NULL ORDER BY created_at DESC"
);

$products = db_fetch_all(
    "SELECT id, name, sku, price FROM products WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name"
);

// Recent orders
$recentOrders = db_fetch_all(
    "SELECT o.order_number, o.status, o.total_amount, o.created_at,
            b.name as batch_name,
            (SELECT COUNT(*) FROM order_product WHERE order_id = o.id) as item_count
     FROM orders o
     JOIN batches b ON o.batch_id = b.id
     WHERE o.customer_id = ? AND o.deleted_at IS NULL
     ORDER BY o.created_at DESC LIMIT 5",
    [$customerId]
);

function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

$pageTitle = 'Pre-Order Sarden';
$currentPage = 'preorder';
include __DIR__ . '/../includes/header-customer.php';
?>

<style>
    .form-select, .form-input {
        width: 100%; border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 9px 12px; font-size: 13px; font-family: inherit;
        color: #374151; background: #fff; outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-select:focus, .form-input:focus {
        border-color: #E02424; box-shadow: 0 0 0 3px rgba(224,36,36,.08);
    }
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 12px center;
        background-size: 14px; padding-right: 36px; cursor: pointer;
    }
    .section-card {
        background: #fff; border: 1px solid #f1f5f9; border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .product-row {
        background: #fafafa; border: 1px solid #f1f5f9; border-radius: 10px;
        padding: 12px 14px; transition: outline 0.1s;
    }
    .product-row.selected { outline: 2px solid #E02424; }
    .icon-btn {
        width: 28px; height: 28px; border-radius: 6px;
        display: inline-flex; align-items: center; justify-content: center;
        transition: background 0.15s, color 0.15s;
        color: #9ca3af; cursor: pointer;
    }
    .icon-btn:hover { background: #f1f5f9; }
    .icon-btn.danger:hover { background: #fef2f2; color: #E02424; }
    #catatan-body { max-height: 0; overflow: hidden; transition: max-height 0.25s ease; }
    #catatan-body.open { max-height: 200px; }
    #catatan-caret { transition: transform 0.2s ease; }
    #catatan-caret.open { transform: rotate(180deg); }
    .badge-blue   { color: #2563eb; background: #eff6ff; border: 1px solid #dbeafe; }
    .badge-amber  { color: #d97706; background: #fffbeb; border: 1px solid #fde68a; }
    .badge-green  { color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; }
    .badge-gray   { color: #6b7280; background: #f9fafb; border: 1px solid #e5e7eb; }
    .alert-error {
        background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;
        border-radius: 8px; padding: 10px 14px; font-size: 13px;
        display: flex; align-items: center; gap: 8px;
    }
</style>

<?php echo renderFlash(); ?>

<?php if (!empty($formErrors)): ?>
<div class="alert-error mb-5">
    <i class="ph-fill ph-warning-circle text-red-500" style="font-size:16px; flex-shrink:0;"></i>
    <ul class="list-disc ml-3">
        <?php foreach ($formErrors as $err): ?>
        <li><?php echo htmlspecialchars($err); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<h1 class="text-[22px] font-bold text-navy mb-6">Pre-Order Sarden</h1>

<!-- Hidden form for POST submission -->
<form id="preorder-form" method="POST" action="">
    <?php echo csrfField(); ?>
    <input type="hidden" name="batch_id" id="form-batch-id">
    <input type="hidden" name="notes" id="form-notes">
    <div id="form-items-container"></div>
</form>

<!-- Intro Card -->
<div class="section-card p-5 mb-4">
    <p class="text-[14px] font-semibold text-navy mb-1">Pre-Order Sarden Kaleng</p>
    <p class="text-[12px] text-gray-500 mb-3">
        Selamat datang, <span class="font-semibold text-[#E02424]"><?php echo htmlspecialchars($customer['name']); ?></span>!
        Silakan pilih <span class="font-semibold">batch</span> produksi dan
        <span class="font-semibold">produk</span> yang tersedia untuk melakukan pre-order.
    </p>
    <div class="flex flex-wrap items-center gap-4 text-[11px] text-gray-500">
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-700 inline-block"></span>
            Min. 100 kaleng
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-700 inline-block"></span>
            Max. 3000 kaleng
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-700 inline-block"></span>
            Ambil di Kampus
        </span>
    </div>
</div>

<!-- Batch Selection -->
<div class="section-card mb-4">
    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
        <i class="ph-bold ph-calendar-dots text-gray-300 text-base"></i>
        <div>
            <p class="text-[13px] font-semibold text-navy leading-none">Informasi Batch</p>
            <p class="text-[11px] text-[#E02424] mt-0.5">Pilih batch produksi yang tersedia untuk pre-order.</p>
        </div>
    </div>
    <div class="p-5">
        <label class="block text-[12px] font-semibold text-navy mb-1.5">
            Batch Produksi <span class="text-[#E02424]">*</span>
        </label>
        <select id="batch-select" class="form-select">
            <option value="" disabled selected>Pilih batch produksi…</option>
            <?php foreach ($batches as $b): ?>
            <option value="<?php echo $b['id']; ?>">
                <?php echo htmlspecialchars($b['name']); ?> — <?php echo htmlspecialchars($b['event_name']); ?>
            </option>
            <?php endforeach; ?>
        </select>
        <?php if (empty($batches)): ?>
        <p class="text-[11px] text-gray-400 mt-1.5">Belum ada batch yang dibuka untuk pre-order.</p>
        <?php else: ?>
        <p class="text-[11px] text-[#E02424] mt-1.5">Pilih batch produksi yang sedang dibuka untuk pre-order.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Product Selection -->
<div class="section-card mb-4">
    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
        <i class="ph-bold ph-storefront text-gray-300 text-base"></i>
        <div>
            <p class="text-[13px] font-semibold text-navy leading-none">Pilih Produk</p>
            <p class="text-[11px] text-[#E02424] mt-0.5">
                Tambahkan produk yang ingin Anda pesan. Minimal
                <strong>100</strong> kaleng dan maksimal
                <strong>3000</strong> kaleng per produk.
            </p>
        </div>
    </div>

    <div class="p-5">
        <div class="flex items-center gap-1 mb-3">
            <button type="button" class="icon-btn" title="Ke atas" onclick="moveSelected('up')">
                <i class="ph-bold ph-arrow-up text-xs"></i>
            </button>
            <button type="button" class="icon-btn" title="Ke bawah" onclick="moveSelected('down')">
                <i class="ph-bold ph-arrow-down text-xs"></i>
            </button>
            <span class="flex-1"></span>
            <button type="button" class="icon-btn danger" title="Hapus baris" onclick="deleteSelected()">
                <i class="ph-bold ph-trash text-xs"></i>
            </button>
            <button type="button" class="icon-btn" title="Ciutkan" onclick="toggleProductSection()">
                <i class="ph-bold ph-caret-up text-xs" id="prod-caret"></i>
            </button>
        </div>

        <div class="grid grid-cols-12 gap-3 px-1 mb-2">
            <div class="col-span-5 text-[11px] font-semibold text-navy">
                Produk <span class="text-[#E02424]">*</span>
            </div>
            <div class="col-span-4 text-[11px] font-semibold text-navy">
                Jumlah (Kaleng) <span class="text-[#E02424]">*</span>
            </div>
            <div class="col-span-3 text-[11px] font-semibold text-navy">Subtotal</div>
        </div>

        <div id="product-rows" class="space-y-2">
            <div class="product-row grid grid-cols-12 gap-3 items-start" onclick="selectRow(this)">
                <div class="col-span-5">
                    <select class="form-select prod-select" onchange="recalc(this)">
                        <option value="" disabled selected>Select an option</option>
                        <?php foreach ($products as $p): ?>
                        <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>">
                            <?php echo htmlspecialchars($p['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-span-4">
                    <div class="relative">
                        <input type="number" value="100" min="100" max="3000"
                               class="form-input prod-qty pr-14" oninput="recalc(this)">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 pointer-events-none">kaleng</span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5 ml-1">Min: 100, Max: 3000</p>
                </div>
                <div class="col-span-3">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-gray-400 pointer-events-none">Rp</span>
                        <input type="text" class="form-input prod-sub pl-8" readonly value="0">
                    </div>
                </div>
            </div>
        </div>

        <button type="button" id="btn-add" onclick="addRow()"
                class="mt-3 flex items-center gap-1 text-[12px] font-semibold text-[#E02424] hover:text-[#9B1C1C] transition-colors">
            <i class="ph-bold ph-plus text-sm"></i>
            Tambah Produk
        </button>
    </div>
</div>

<!-- Catatan -->
<div class="section-card mb-5">
    <button type="button" onclick="toggleCatatan()"
            class="w-full flex items-center gap-2 px-5 py-4 text-left">
        <i class="ph-bold ph-chat-dots text-gray-300 text-base"></i>
        <span class="text-[13px] font-semibold text-navy flex-1">Catatan Tambahan</span>
        <i class="ph-bold ph-caret-down text-gray-400 text-sm" id="catatan-caret"></i>
    </button>
    <div id="catatan-body">
        <div class="px-5 pb-5">
            <textarea rows="3" id="catatan-area" class="form-input resize-none"
                      placeholder="Tambahkan catatan untuk pesanan Anda (opsional)…"></textarea>
        </div>
    </div>
</div>

<!-- Submit -->
<div class="flex justify-end mb-8">
    <button type="button" id="btn-submit" onclick="submitOrder()"
            class="inline-flex items-center gap-2 bg-[#E02424] hover:bg-[#9B1C1C]
                   text-white text-[13px] font-semibold px-6 py-2.5 rounded-lg
                   transition-colors shadow-sm">
        <i class="ph-bold ph-paper-plane-tilt text-base"></i>
        Kirim Pre-Order
    </button>
</div>

<!-- Riwayat Pesanan -->
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <i class="ph-bold ph-clock-counter-clockwise text-gray-300 text-base"></i>
        <h2 class="text-[14px] font-semibold text-navy">Riwayat Pesanan Anda</h2>
    </div>

    <?php if (empty($recentOrders)): ?>
    <div class="section-card p-6 text-center">
        <i class="ph-bold ph-bag text-gray-300 text-2xl mb-2"></i>
        <p class="text-[12px] text-gray-400">Belum ada pesanan</p>
    </div>
    <?php else: ?>
    <div class="space-y-3">
        <?php foreach ($recentOrders as $order):
            $statusMap = [
                'pending'    => ['label' => 'Menunggu',    'class' => 'badge-amber'],
                'processing' => ['label' => 'Diproses',    'class' => 'badge-blue'],
                'ready'      => ['label' => 'Siap Diambil','class' => 'badge-green'],
                'picked_up'  => ['label' => 'Diambil',     'class' => 'badge-gray'],
            ];
            $s = $statusMap[$order['status']] ?? ['label' => $order['status'], 'class' => 'badge-gray'];
        ?>
        <div class="section-card px-5 py-4 flex items-center gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2.5 mb-1 flex-wrap">
                    <span class="text-[13px] font-semibold text-navy"><?php echo htmlspecialchars($order['order_number']); ?></span>
                    <span class="<?php echo $s['class']; ?> inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded-full">
                        <?php echo $s['label']; ?>
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[11px] text-gray-400">
                    <span>Batch: <?php echo htmlspecialchars($order['batch_name']); ?></span>
                    <span class="text-gray-200">•</span>
                    <span><?php echo $order['item_count']; ?> produk</span>
                    <span class="text-gray-200">•</span>
                    <span><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></span>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-[13px] font-bold text-navy"><?php echo formatRupiah($order['total_amount']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
    const PRODUCTS = {
        <?php foreach ($products as $p): ?>
        '<?php echo $p['id']; ?>': { name: '<?php echo addslashes($p['name']); ?>', price: <?php echo $p['price']; ?> },
        <?php endforeach; ?>
    };

    function toggleCatatan() {
        document.getElementById('catatan-body').classList.toggle('open');
        document.getElementById('catatan-caret').classList.toggle('open');
    }

    let prodVisible = true;
    function toggleProductSection() {
        prodVisible = !prodVisible;
        document.getElementById('product-rows').style.display = prodVisible ? '' : 'none';
        document.getElementById('btn-add').style.display = prodVisible ? '' : 'none';
        document.getElementById('prod-caret').style.transform = prodVisible ? '' : 'rotate(180deg)';
    }

    let selectedRow = null;
    function selectRow(row) {
        if (selectedRow) selectedRow.classList.remove('selected');
        selectedRow = row;
        row.classList.add('selected');
    }

    function recalc(el) {
        const row = el.closest('.product-row');
        const sel = row.querySelector('.prod-select');
        const qty = parseInt(row.querySelector('.prod-qty').value) || 0;
        const sub = row.querySelector('.prod-sub');
        const prod = PRODUCTS[sel.value];
        const total = prod ? prod.price * qty : 0;
        sub.value = total > 0 ? total.toLocaleString('id-ID') : '0';
    }

    function addRow() {
        const container = document.getElementById('product-rows');
        const optionsHtml = Object.entries(PRODUCTS).map(([id, p]) =>
            '<option value="' + id + '" data-price="' + p.price + '">' + p.name + '</option>'
        ).join('');

        const div = document.createElement('div');
        div.className = 'product-row grid grid-cols-12 gap-3 items-start';
        div.onclick = function() { selectRow(div); };
        div.innerHTML = '<div class="col-span-5"><select class="form-select prod-select" onchange="recalc(this)">' +
            '<option value="" disabled selected>Select an option</option>' + optionsHtml + '</select></div>' +
            '<div class="col-span-4"><div class="relative">' +
            '<input type="number" value="100" min="100" max="3000" class="form-input prod-qty pr-14" oninput="recalc(this)">' +
            '<span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 pointer-events-none">kaleng</span>' +
            '</div><p class="text-[10px] text-gray-400 mt-0.5 ml-1">Min: 100, Max: 3000</p></div>' +
            '<div class="col-span-3"><div class="relative">' +
            '<span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-gray-400 pointer-events-none">Rp</span>' +
            '<input type="text" class="form-input prod-sub pl-8" readonly value="0"></div></div>';
        container.appendChild(div);
        selectRow(div);
    }

    function deleteSelected() {
        if (document.querySelectorAll('.product-row').length <= 1) return;
        if (selectedRow) { selectedRow.remove(); selectedRow = null; }
    }

    function moveSelected(dir) {
        if (!selectedRow) return;
        const container = document.getElementById('product-rows');
        if (dir === 'up' && selectedRow.previousElementSibling) {
            container.insertBefore(selectedRow, selectedRow.previousElementSibling);
        } else if (dir === 'down' && selectedRow.nextElementSibling) {
            container.insertBefore(selectedRow.nextElementSibling, selectedRow);
        }
    }

    function submitOrder() {
        const batchSel = document.getElementById('batch-select');
        if (!batchSel.value) {
            alert('Silakan pilih batch produksi terlebih dahulu.');
            return;
        }
        const rows = document.querySelectorAll('.product-row');
        let valid = true;
        rows.forEach(function(row) {
            if (!row.querySelector('.prod-select').value) valid = false;
        });
        if (!valid) {
            alert('Silakan pilih produk untuk semua baris.');
            return;
        }

        document.getElementById('form-batch-id').value = batchSel.value;
        document.getElementById('form-notes').value = document.getElementById('catatan-area').value;

        var container = document.getElementById('form-items-container');
        container.textContent = '';
        rows.forEach(function(row, i) {
            var productId = row.querySelector('.prod-select').value;
            var quantity  = row.querySelector('.prod-qty').value;
            var inputPid = document.createElement('input');
            inputPid.type = 'hidden';
            inputPid.name = 'items[' + i + '][product_id]';
            inputPid.value = productId;
            container.appendChild(inputPid);
            var inputQty = document.createElement('input');
            inputQty.type = 'hidden';
            inputQty.name = 'items[' + i + '][quantity]';
            inputQty.value = quantity;
            container.appendChild(inputQty);
        });

        var btn = document.getElementById('btn-submit');
        btn.textContent = 'Mengirim...';
        btn.disabled = true;
        document.getElementById('preorder-form').submit();
    }
</script>

<?php include __DIR__ . '/../includes/footer-customer.php'; ?>
