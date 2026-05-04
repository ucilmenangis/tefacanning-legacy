<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../classes/FormatHelper.php';
require_once __DIR__ . '/../classes/ProductService.php';
Auth::customer()->requireAuth();

$customerId = Auth::customer()->getId();
$customer = Database::getInstance()->fetch("SELECT name FROM customers WHERE id = ? AND deleted_at IS NULL", [$customerId]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batchId = (int) ($_POST['batch_id'] ?? 0);
    $notes   = trim($_POST['notes'] ?? '');
    $items   = $_POST['items'] ?? [];

    // Validate batch
    $batch = Database::getInstance()->fetch(
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
    $productService = new ProductService();
    foreach ($items as $item) {
        $productId = (int) ($item['product_id'] ?? 0);
        $quantity  = (int) ($item['quantity'] ?? 0);

        if ($productId <= 0 || $quantity < 100 || $quantity > 3000) {
            $errors[] = "Jumlah harus antara 100–3000 kaleng.";
            continue;
        }

        $product = Database::getInstance()->fetch(
            "SELECT id, name, price, stock FROM products WHERE id = ? AND is_active = 1 AND deleted_at IS NULL",
            [$productId]
        );

        if (!$product) {
            $errors[] = 'Produk tidak valid.';
            continue;
        }

        if ($product['stock'] < $quantity) {
            $errors[] = 'Stok ' . $product['name'] . ' tidak cukup (sisa: ' . $product['stock'] . ' kaleng).';
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

        $pdo = Database::getInstance()->getPdo();
        $pdo->beginTransaction();

        try {
            // Deduct stock atomically for each item
            foreach ($validItems as $vi) {
                if (!$productService->deductStock($vi['product_id'], $vi['quantity'])) {
                    throw new RuntimeException('Stok produk tidak cukup.');
                }
            }

            $orderId = Database::getInstance()->insert(
                "INSERT INTO orders (customer_id, batch_id, order_number, pickup_code, status, total_amount, profit, notes, created_at, updated_at)
                 VALUES (?, ?, ?, ?, 'pending', ?, 0, ?, NOW(), NOW())",
                [$customerId, $batchId, $orderNumber, $pickupCode, $totalAmount, $notes ?: null]
            );

            foreach ($validItems as $vi) {
                Database::getInstance()->insert(
                    "INSERT INTO order_product (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
                    [$orderId, $vi['product_id'], $vi['quantity'], $vi['unit_price'], $vi['subtotal']]
                );
            }

            $pdo->commit();
            FlashMessage::set('success', 'Pre-Order berhasil dikirim! Order: ' . $orderNumber);
            header('Location: preorder.php');
            exit;
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errors[] = 'Gagal membuat pesanan: ' . $e->getMessage();
        }
    }

    $formErrors = $errors;
}

// Data for form
$batches = Database::getInstance()->fetchAll(
    "SELECT id, name, event_name, event_date FROM batches WHERE status = 'open' AND deleted_at IS NULL ORDER BY created_at DESC"
);

$products = Database::getInstance()->fetchAll(
    "SELECT id, name, sku, price, stock FROM products WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name"
);

// Recent orders
$recentOrders = Database::getInstance()->fetchAll(
    "SELECT o.order_number, o.status, o.total_amount, o.created_at,
            b.name as batch_name,
            (SELECT COUNT(*) FROM order_product WHERE order_id = o.id) as item_count
     FROM orders o
     JOIN batches b ON o.batch_id = b.id
     WHERE o.customer_id = ? AND o.deleted_at IS NULL
     ORDER BY o.created_at DESC LIMIT 5",
    [$customerId]
);

$pageTitle = 'Pre-Order Sarden';
$currentPage = 'preorder';
include __DIR__ . '/../includes/header-customer.php';
?>

<style>
    /* KEEP — JS-toggled accordion animation */
    #catatan-body { max-height: 0; overflow: hidden; transition: max-height 0.25s ease; }
    #catatan-body.open { max-height: 200px; }
    #catatan-caret { transition: transform 0.2s ease; }
    #catatan-caret.open { transform: rotate(180deg); }
    /* KEEP — JS-toggled selected state */
    .product-row.selected { outline: 2px solid #E02424; outline-offset: -2px; background-color: #fef2f2; }
</style>



<?php echo FlashMessage::render(); ?>

<?php if (!empty($formErrors)): ?>
<div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-3.5 py-2.5 text-[13px] flex items-center gap-2 mb-5">
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
    <?php echo CsrfService::field(); ?>
    <input type="hidden" name="batch_id" id="form-batch-id">
    <input type="hidden" name="notes" id="form-notes">
    <div id="form-items-container"></div>
</form>

<!-- Intro Card -->
<div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5 mb-4">
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
<div class="bg-white border border-gray-100 rounded-xl shadow-sm mb-4">
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
        <select id="batch-select" class="w-full border border-gray-200 rounded-lg py-2 px-3 text-[13px] text-gray-700 bg-white outline-none appearance-none cursor-pointer transition-colors focus:border-primary" style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:14px;padding-right:36px">
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
<div class="bg-white border border-gray-100 rounded-xl shadow-sm mb-4">
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
            <button type="button" class="w-7 h-7 rounded-md inline-flex items-center justify-center text-gray-400 hover:bg-gray-100 transition-colors cursor-pointer" title="Ke atas" onclick="moveSelected('up')">
                <i class="ph-bold ph-arrow-up text-xs"></i>
            </button>
            <button type="button" class="w-7 h-7 rounded-md inline-flex items-center justify-center text-gray-400 hover:bg-gray-100 transition-colors cursor-pointer" title="Ke bawah" onclick="moveSelected('down')">
                <i class="ph-bold ph-arrow-down text-xs"></i>
            </button>
            <span class="flex-1"></span>
            <button type="button" class="w-7 h-7 rounded-md inline-flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-primary transition-colors cursor-pointer" title="Hapus baris" onclick="deleteSelected()">
                <i class="ph-bold ph-trash text-xs"></i>
            </button>
            <button type="button" class="w-7 h-7 rounded-md inline-flex items-center justify-center text-gray-400 hover:bg-gray-100 transition-colors cursor-pointer" title="Ciutkan" onclick="toggleProductSection()">
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
                    <select class="w-full border border-gray-200 rounded-lg py-2 px-3 text-[13px] text-gray-700 bg-white outline-none appearance-none cursor-pointer focus:border-primary prod-select" onchange="recalc(this)">
                        <option value="" disabled selected>Pilih produk…</option>
                        <?php foreach ($products as $p): ?>
                        <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>" <?php echo $p['stock'] < 100 ? 'disabled' : ''; ?>>
                            <?php echo htmlspecialchars($p['name']); ?> (Stok: <?php echo $p['stock']; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-span-4">
                    <div class="relative">
                        <input type="number" value="100" min="100" max="3000"
                               class="w-full border border-gray-200 rounded-lg py-2 px-3 text-[13px] text-gray-700 bg-white outline-none focus:border-primary prod-qty pr-14" oninput="recalc(this)">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 pointer-events-none">kaleng</span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5 ml-1">Min: 100, Max: 3000</p>
                </div>
                <div class="col-span-3">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-gray-400 pointer-events-none">Rp</span>
                        <input type="text" class="w-full border border-gray-200 rounded-lg py-2 px-3 pl-8 text-[13px] text-gray-700 bg-white outline-none prod-sub" readonly value="0">
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
<div class="bg-white border border-gray-100 rounded-xl shadow-sm mb-5">
    <button type="button" onclick="toggleCatatan()"
            class="w-full flex items-center gap-2 px-5 py-4 text-left">
        <i class="ph-bold ph-chat-dots text-gray-300 text-base"></i>
        <span class="text-[13px] font-semibold text-navy flex-1">Catatan Tambahan</span>
        <i class="ph-bold ph-caret-down text-gray-400 text-sm" id="catatan-caret"></i>
    </button>
    <div id="catatan-body">
        <div class="px-5 pb-5">
            <textarea rows="3" id="catatan-area" class="w-full border border-gray-200 rounded-lg py-2 px-3 text-[13px] text-gray-700 bg-white outline-none resize-none focus:border-primary"
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
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 text-center">
        <i class="ph-bold ph-bag text-gray-300 text-2xl mb-2"></i>
        <p class="text-[12px] text-gray-400">Belum ada pesanan</p>
    </div>
    <?php else: ?>
    <div class="space-y-3">
        <?php foreach ($recentOrders as $order):
            $s = FormatHelper::orderStatus($order['status']);
        ?>
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm px-5 py-4 flex items-center gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2.5 mb-1 flex-wrap">
                    <span class="text-[13px] font-semibold text-navy"><?php echo htmlspecialchars($order['order_number']); ?></span>
                    <span class="<?php echo $s['badge']; ?> inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded-full">
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
                <p class="text-[13px] font-bold text-navy"><?php echo FormatHelper::rupiah((float) $order['total_amount']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
    const PRODUCTS = {
        <?php foreach ($products as $p): ?>
        '<?php echo $p['id']; ?>': { name: '<?php echo addslashes($p['name']); ?>', price: <?php echo $p['price']; ?>, stock: <?php echo $p['stock']; ?> },
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
            '<option value="' + id + '" data-price="' + p.price + '"' + (p.stock < 100 ? ' disabled' : '') + '>' + p.name + ' (Stok: ' + p.stock + ')</option>'
        ).join('');

        const div = document.createElement('div');
        div.className = 'product-row flex items-center gap-3 px-4 py-3 border border-gray-100 rounded-lg cursor-pointer transition-colors hover:bg-gray-50';
        div.onclick = function() { selectRow(div); };
        div.innerHTML = '<div class="col-span-5 flex-1"><select class="w-full border border-gray-200 rounded-lg py-2 px-3 text-[13px] text-gray-700 bg-white outline-none appearance-none cursor-pointer focus:border-primary prod-select" style="background-image:url(\'data:image/svg+xml,%3Csvg xmlns=\\\'http://www.w3.org/2000/svg\\\' viewBox=\\\'0 0 16 16\\\'%3E%3Cpath fill=\\\'none\\\' stroke=\\\'%239ca3af\\\' stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M2 5l6 6 6-6\\\'/%3E%3C/svg%3E\');background-repeat:no-repeat;background-position:right 12px center;background-size:14px;padding-right:36px" onchange="recalc(this)">' +
            '<option value="" disabled selected>Pilih produk…</option>' + optionsHtml + '</select></div>' +
            '<div class="col-span-4 w-32 shrink-0"><div class="relative">' +
            '<input type="number" value="100" min="100" max="3000" class="w-full border border-gray-200 rounded-lg py-2 px-3 text-[13px] text-gray-700 bg-white outline-none focus:border-primary prod-qty pr-14" oninput="recalc(this)">' +
            '<span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 pointer-events-none">kaleng</span>' +
            '</div></div>' +
            '<div class="col-span-3 w-32 shrink-0"><div class="relative">' +
            '<span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-gray-400 pointer-events-none">Rp</span>' +
            '<input type="text" class="w-full border border-gray-200 rounded-lg py-2 px-3 pl-8 text-[13px] text-gray-700 bg-white outline-none prod-sub" readonly value="0"></div></div>';
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
        let stockOk = true;
        rows.forEach(function(row) {
            const sel = row.querySelector('.prod-select');
            if (!sel.value) { valid = false; return; }
            const qty = parseInt(row.querySelector('.prod-qty').value) || 0;
            const prod = PRODUCTS[sel.value];
            if (prod && qty > prod.stock) {
                alert('Stok ' + prod.name + ' tidak cukup (sisa: ' + prod.stock + ' kaleng).');
                stockOk = false;
            }
        });
        if (!valid) {
            alert('Silakan pilih produk untuk semua baris.');
            return;
        }
        if (!stockOk) return;

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
