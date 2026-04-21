<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/OrderService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';
requireCustomer();

$customerId = getCustomerId();
$orderService = new OrderService();
$orderId = (int) ($_GET['id'] ?? 0);

$order = $orderService->getById($orderId, $customerId);

if (!$order) {
    setFlash('error', 'Pesanan tidak ditemukan.');
    header('Location: orders.php');
    exit;
}

if ($order['status'] !== 'pending') {
    setFlash('error', 'Hanya pesanan berstatus Menunggu yang dapat diedit.');
    header('Location: orders.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash('error', 'Token keamanan tidak valid.');
        header('Location: edit-order.php?id=' . $orderId);
        exit;
    }

    $items = $_POST['items'] ?? [];
    $errors = [];

    if (empty($items)) {
        $errors[] = 'Pilih minimal 1 produk.';
    }

    $validItems = [];
    foreach ($items as $item) {
        $productId = (int) ($item['product_id'] ?? 0);
        $quantity  = (int) ($item['quantity'] ?? 0);

        if ($productId <= 0 || $quantity < 100 || $quantity > 3000) {
            $errors[] = 'Jumlah harus antara 100–3000 kaleng.';
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
            'product_id' => $product['id'],
            'quantity'   => $quantity,
            'unit_price' => $product['price'],
            'subtotal'   => $product['price'] * $quantity,
        ];
    }

    if (empty($errors) && !empty($validItems)) {
        $totalAmount = array_sum(array_column($validItems, 'subtotal'));

        db()->prepare("UPDATE orders SET total_amount = ?, updated_at = NOW() WHERE id = ?")
            ->execute([$totalAmount, $orderId]);
        db()->prepare("DELETE FROM order_product WHERE order_id = ?")->execute([$orderId]);

        foreach ($validItems as $vi) {
            db_insert(
                "INSERT INTO order_product (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
                [$orderId, $vi['product_id'], $vi['quantity'], $vi['unit_price'], $vi['subtotal']]
            );
        }

        setFlash('success', 'Pesanan berhasil diperbarui.');
        header('Location: orders.php');
        exit;
    }

    $formErrors = $errors;
}

$products = db_fetch_all(
    "SELECT id, name, sku, price FROM products WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name"
);

$order = $orderService->getById($orderId, $customerId);
$s = FormatHelper::orderStatus($order['status']);

$pageTitle = 'Edit Pesanan';
$currentPage = 'orders';
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
    .section-card { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    .product-row { background: #fafafa; border: 1px solid #f1f5f9; border-radius: 10px; padding: 12px 14px; transition: outline 0.1s; }
    .product-row.selected { outline: 2px solid #E02424; }
    .icon-btn { width: 28px; height: 28px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; transition: background 0.15s, color 0.15s; color: #9ca3af; cursor: pointer; }
    .icon-btn:hover { background: #f1f5f9; }
    .icon-btn.danger:hover { background: #fef2f2; color: #E02424; }
    .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; border-radius: 8px; padding: 10px 14px; font-size: 13px; display: flex; align-items: center; gap: 8px; }
    .badge-amber { background: #fff7ed; color: #d97706; border: 1px solid #ffedd5; }
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

<a href="orders.php" class="inline-flex items-center gap-1 text-[13px] font-medium text-[#E02424] hover:text-[#9B1C1C] mb-4">
    <i class="ph-bold ph-arrow-left text-sm"></i> Kembali ke Riwayat Pesanan
</a>

<h1 class="text-[22px] font-bold text-navy mb-2">Edit Pesanan</h1>
<p class="text-[13px] text-gray-500 mb-5">
    <span class="font-semibold text-navy"><?php echo htmlspecialchars($order['order_number']); ?></span> —
    <span class="<?php echo $s['badge']; ?> px-2 py-0.5 rounded-full text-[10px] font-bold"><?php echo $s['label']; ?></span>
    — Batch: <?php echo htmlspecialchars($order['batch_name']); ?>
</p>

<form id="edit-form" method="POST" action="">
    <?php echo csrfField(); ?>
    <div id="form-items-container"></div>
</form>

<div class="section-card mb-5">
    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
        <i class="ph-bold ph-storefront text-gray-300 text-base"></i>
        <div>
            <p class="text-[13px] font-semibold text-navy leading-none">Edit Produk</p>
            <p class="text-[11px] text-[#E02424] mt-0.5">Minimal <strong>100</strong> kaleng, maksimal <strong>3000</strong> kaleng per produk.</p>
        </div>
    </div>

    <div class="p-5">
        <div class="flex items-center gap-1 mb-3">
            <button type="button" class="icon-btn danger" title="Hapus baris" onclick="deleteSelected()">
                <i class="ph-bold ph-trash text-xs"></i>
            </button>
        </div>

        <div class="grid grid-cols-12 gap-3 px-1 mb-2">
            <div class="col-span-5 text-[11px] font-semibold text-navy">Produk <span class="text-[#E02424]">*</span></div>
            <div class="col-span-4 text-[11px] font-semibold text-navy">Jumlah <span class="text-[#E02424]">*</span></div>
            <div class="col-span-3 text-[11px] font-semibold text-navy">Subtotal</div>
        </div>

        <div id="product-rows" class="space-y-2">
            <?php foreach ($order['items'] as $item): ?>
            <div class="product-row grid grid-cols-12 gap-3 items-start" onclick="selectRow(this)">
                <div class="col-span-5">
                    <select class="form-select prod-select" onchange="recalc(this)">
                        <option value="" disabled>Select an option</option>
                        <?php foreach ($products as $p): ?>
                        <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>"
                            <?php echo $p['id'] == $item['product_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-span-4">
                    <div class="relative">
                        <input type="number" value="<?php echo $item['quantity']; ?>" min="100" max="3000"
                               class="form-input prod-qty pr-14" oninput="recalc(this)">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 pointer-events-none">kaleng</span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5 ml-1">Min: 100, Max: 3000</p>
                </div>
                <div class="col-span-3">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-gray-400 pointer-events-none">Rp</span>
                        <input type="text" class="form-input prod-sub pl-8" readonly
                               value="<?php echo number_format($item['subtotal'], 0, ',', '.'); ?>">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="btn-add" onclick="addRow()"
                class="mt-3 flex items-center gap-1 text-[12px] font-semibold text-[#E02424] hover:text-[#9B1C1C] transition-colors">
            <i class="ph-bold ph-plus text-sm"></i>
            Tambah Produk
        </button>
    </div>
</div>

<div class="flex justify-between mb-8">
    <a href="orders.php" class="inline-flex items-center gap-2 text-[13px] font-medium text-gray-500 hover:text-navy">
        <i class="ph-bold ph-arrow-left text-sm"></i> Batal
    </a>
    <button type="button" onclick="submitOrder()"
            class="inline-flex items-center gap-2 bg-[#E02424] hover:bg-[#9B1C1C]
                   text-white text-[13px] font-semibold px-6 py-2.5 rounded-lg transition-colors shadow-sm">
        <i class="ph-bold ph-floppy-disk text-base"></i> Simpan Perubahan
    </button>
</div>

<script>
    var PRODUCTS = {
        <?php foreach ($products as $p): ?>
        '<?php echo $p['id']; ?>': { name: '<?php echo addslashes($p['name']); ?>', price: <?php echo $p['price']; ?> },
        <?php endforeach; ?>
    };

    var selectedRow = null;
    function selectRow(row) {
        if (selectedRow) selectedRow.classList.remove('selected');
        selectedRow = row; row.classList.add('selected');
    }

    function recalc(el) {
        var row = el.closest('.product-row');
        var sel = row.querySelector('.prod-select');
        var qty = parseInt(row.querySelector('.prod-qty').value) || 0;
        var prod = PRODUCTS[sel.value];
        var total = prod ? prod.price * qty : 0;
        row.querySelector('.prod-sub').value = total > 0 ? total.toLocaleString('id-ID') : '0';
    }

    function addRow() {
        var container = document.getElementById('product-rows');
        var div = document.createElement('div');
        div.className = 'product-row grid grid-cols-12 gap-3 items-start';
        div.onclick = function() { selectRow(div); };

        var col5 = document.createElement('div');
        col5.className = 'col-span-5';
        var sel = document.createElement('select');
        sel.className = 'form-select prod-select';
        sel.onchange = function() { recalc(sel); };
        var defOpt = document.createElement('option');
        defOpt.value = ''; defOpt.disabled = true; defOpt.selected = true;
        defOpt.textContent = 'Select an option';
        sel.appendChild(defOpt);
        Object.entries(PRODUCTS).forEach(function(entry) {
            var opt = document.createElement('option');
            opt.value = entry[0]; opt.textContent = entry[1].name;
            opt.setAttribute('data-price', entry[1].price);
            sel.appendChild(opt);
        });
        col5.appendChild(sel);
        div.appendChild(col5);

        var col4 = document.createElement('div');
        col4.className = 'col-span-4';
        var qtyWrap = document.createElement('div');
        qtyWrap.className = 'relative';
        var qtyInput = document.createElement('input');
        qtyInput.type = 'number'; qtyInput.value = '100'; qtyInput.min = '100'; qtyInput.max = '3000';
        qtyInput.className = 'form-input prod-qty pr-14';
        qtyInput.oninput = function() { recalc(qtyInput); };
        var qtySpan = document.createElement('span');
        qtySpan.className = 'absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 pointer-events-none';
        qtySpan.textContent = 'kaleng';
        qtyWrap.appendChild(qtyInput);
        qtyWrap.appendChild(qtySpan);
        col4.appendChild(qtyWrap);
        var hint = document.createElement('p');
        hint.className = 'text-[10px] text-gray-400 mt-0.5 ml-1';
        hint.textContent = 'Min: 100, Max: 3000';
        col4.appendChild(hint);
        div.appendChild(col4);

        var col3 = document.createElement('div');
        col3.className = 'col-span-3';
        var subWrap = document.createElement('div');
        subWrap.className = 'relative';
        var rpSpan = document.createElement('span');
        rpSpan.className = 'absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-gray-400 pointer-events-none';
        rpSpan.textContent = 'Rp';
        var subInput = document.createElement('input');
        subInput.type = 'text'; subInput.className = 'form-input prod-sub pl-8';
        subInput.readOnly = true; subInput.value = '0';
        subWrap.appendChild(rpSpan);
        subWrap.appendChild(subInput);
        col3.appendChild(subWrap);
        div.appendChild(col3);

        container.appendChild(div);
        selectRow(div);
    }

    function deleteSelected() {
        if (document.querySelectorAll('.product-row').length <= 1) return;
        if (selectedRow) { selectedRow.remove(); selectedRow = null; }
    }

    function submitOrder() {
        var rows = document.querySelectorAll('.product-row');
        var valid = true;
        rows.forEach(function(row) {
            if (!row.querySelector('.prod-select').value) valid = false;
        });
        if (!valid) { alert('Silakan pilih produk untuk semua baris.'); return; }

        var container = document.getElementById('form-items-container');
        container.textContent = '';
        rows.forEach(function(row, i) {
            var productId = row.querySelector('.prod-select').value;
            var quantity = row.querySelector('.prod-qty').value;
            var inputPid = document.createElement('input');
            inputPid.type = 'hidden'; inputPid.name = 'items[' + i + '][product_id]';
            inputPid.value = productId; container.appendChild(inputPid);
            var inputQty = document.createElement('input');
            inputQty.type = 'hidden'; inputQty.name = 'items[' + i + '][quantity]';
            inputQty.value = quantity; container.appendChild(inputQty);
        });
        document.getElementById('edit-form').submit();
    }
</script>

<?php include __DIR__ . '/../includes/footer-customer.php'; ?>
