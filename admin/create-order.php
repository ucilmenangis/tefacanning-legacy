<?php
/**
 * Admin Create Order Page
 */

$pageTitle   = 'Buat Pesanan';
$currentPage = 'orders';

require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$adminService = new AdminService();
$activityLogService = new ActivityLogService();

// Build JS-safe product data for dynamic add row
$products = Database::getInstance()->fetchAll(
    "SELECT id, name, sku, price FROM products WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC"
);
$productJson = json_encode(array_map(function($p) {
    return ['id' => (int)$p['id'], 'name' => $p['name'], 'price' => (float)$p['price']];
}, $products), JSON_UNESCAPED_UNICODE);

// ── POST Handler ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfService::verify()) {
        FlashMessage::set('error', 'Token CSRF tidak valid.');
        header('Location: create-order.php');
        exit;
    }

    $customerId = intval($_POST['customer_id'] ?? 0);
    $batchId    = intval($_POST['batch_id'] ?? 0);
    $productIds = $_POST['products'] ?? [];
    $quantities = $_POST['qty'] ?? [];

    if (!$customerId || !$batchId || empty($productIds)) {
        FlashMessage::set('error', 'Pelanggan, batch, dan minimal 1 produk wajib diisi.');
        header('Location: create-order.php');
        exit;
    }

    // Generate order number and pickup code
    $orderNumber = 'ORD-' . strtoupper(bin2hex(random_bytes(4)));
    $pickupCode  = strtoupper(bin2hex(random_bytes(3)));

    // Calculate total from DB prices (never trust form)
    $totalAmount = 0;
    $orderItems = [];
    foreach ($productIds as $i => $productId) {
        $productId = intval($productId);
        $qty = max(1, intval($quantities[$i] ?? 1));
        $price = $adminService->verifyProductPrice($productId);
        if ($price <= 0) continue;
        $subtotal = $price * $qty;
        $totalAmount += $subtotal;
        $orderItems[] = [$productId, $qty, $price, $subtotal];
    }

    if (empty($orderItems)) {
        FlashMessage::set('error', 'Tidak ada produk valid yang dipilih.');
        header('Location: create-order.php');
        exit;
    }

    // Insert order
    $orderId = Database::getInstance()->insert(
        "INSERT INTO orders (customer_id, batch_id, order_number, pickup_code, status, total_amount, profit, created_at, updated_at)
         VALUES (?, ?, ?, ?, 'pending', ?, 0, NOW(), NOW())",
        [$customerId, $batchId, $orderNumber, $pickupCode, $totalAmount]
    );

    // Insert order items
    foreach ($orderItems as $item) {
        Database::getInstance()->insert(
            "INSERT INTO order_product (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
            [$orderId, $item[0], $item[1], $item[2], $item[3]]
        );
    }

    $activityLogService->log('created', 'App\Models\Order', $orderId, 'created', [
        'order_number' => $orderNumber,
        'total' => $totalAmount,
    ]);

    FlashMessage::set('success', 'Pesanan berhasil dibuat.');
    header('Location: view-order.php?id=' . $orderId);
    exit;
}

// Fetch data for form
$customers = Database::getInstance()->fetchAll(
    "SELECT id, name, phone FROM customers WHERE deleted_at IS NULL ORDER BY name ASC"
);
$batches = Database::getInstance()->fetchAll(
    "SELECT id, name, event_name, event_date FROM batches WHERE status = 'open' AND deleted_at IS NULL ORDER BY created_at DESC"
);

include __DIR__ . '/../includes/header-admin.php';
?>



<!-- Header -->
<div class="mb-2">
    <div class="text-[12px] text-gray-400 mb-3 flex items-center gap-2">
        <a href="orders.php" class="text-gray-400 hover:text-primary transition-colors">Pesanan</a>
        <i class="ph ph-caret-right text-[10px]"></i>
        <span class="text-gray-600 font-medium">Buat Pesanan Baru</span>
    </div>
    <h1 class="text-[24px] font-extrabold text-navy">Buat Pesanan</h1>
</div>

<form action="create-order.php" method="POST">
    <?php echo CsrfService::field(); ?>

    <!-- Informasi Pesanan -->
    <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
        <div class="text-[14px] font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="ph ph-article-ny-times text-lg text-slate-400"></i>
            Informasi Pesanan
        </div>
        <span class="text-[11px] text-gray-400 font-medium -mt-3 mb-5 block">Pilih pelanggan dan batch</span>

        <div class="grid grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <label class="text-[12px] font-semibold text-gray-600 mb-1.5 block">Pelanggan<span class="text-primary ml-0.5">*</span></label>
                <div class="relative">
                    <select name="customer_id" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 appearance-none cursor-pointer pr-9" required>
                        <option value="" disabled selected>Pilih Pelanggan</option>
                        <?php foreach ($customers as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name'] . ($c['phone'] ? ' — ' . $c['phone'] : '')); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="ph ph-caret-down absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
            </div>
            <div>
                <label class="text-[12px] font-semibold text-gray-600 mb-1.5 block">Batch<span class="text-primary ml-0.5">*</span></label>
                <div class="relative">
                    <select name="batch_id" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 appearance-none cursor-pointer pr-9" required>
                        <option value="" disabled selected>Pilih Batch</option>
                        <?php foreach ($batches as $b): ?>
                        <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['name'] . ' — ' . $b['event_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="ph ph-caret-down absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block italic">Hanya batch dengan status Open yang tersedia</span>
            </div>
        </div>
    </div>

    <!-- Item Pesanan -->
    <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
        <div class="text-[14px] font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="ph ph-shopping-cart text-lg text-slate-400"></i>
            Item Pesanan
        </div>
        <span class="text-[11px] text-gray-400 font-medium -mt-3 mb-5 block">Tambahkan produk ke pesanan</span>

        <div id="product-list-container">
            <table class="w-full text-[12.5px]" style="border-collapse:separate;border-spacing:0 12px;margin-top:-12px;">
                <thead>
                    <tr>
                        <th class="text-[11px] font-bold text-gray-400 uppercase text-left px-3.5" style="width: 45%;">Produk<span class="text-primary ml-0.5">*</span></th>
                        <th class="text-[11px] font-bold text-gray-400 uppercase text-left px-3.5" style="width: 10%;">Qty<span class="text-primary ml-0.5">*</span></th>
                        <th class="text-[11px] font-bold text-gray-400 uppercase text-left px-3.5" style="width: 20%;">Harga</th>
                        <th class="text-[11px] font-bold text-gray-400 uppercase text-left px-3.5" style="width: 20%;">Subtotal</th>
                        <th class="text-[11px] font-bold text-gray-400 uppercase text-left px-3.5" style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody id="product-tbody">
                    <tr>
                        <td class="bg-white border-t border-b border-l border-gray-100 py-4 px-3.5 rounded-l-lg">
                            <div class="relative">
                                <select name="products[]" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 appearance-none cursor-pointer pr-9 product-select" required>
                                    <option value="" disabled selected>Pilih Produk</option>
                                    <?php foreach ($products as $p): ?>
                                    <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="ph ph-caret-down absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            </div>
                        </td>
                        <td class="bg-white border-t border-b border-gray-100 py-4 px-3.5">
                            <input type="number" name="qty[]" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 text-center qty-input" value="1" required min="1">
                        </td>
                        <td class="bg-white border-t border-b border-gray-100 py-4 px-3.5">
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-[12px] text-gray-400 font-semibold pointer-events-none">Rp</span>
                                <input type="text" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 pl-9 text-right price-display" value="0" disabled>
                            </div>
                        </td>
                        <td class="bg-white border-t border-b border-gray-100 py-4 px-3.5">
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-[12px] text-gray-400 font-semibold pointer-events-none">Rp</span>
                                <input type="text" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 pl-9 text-right font-semibold subtotal-display" value="0" disabled>
                            </div>
                        </td>
                        <td class="bg-white border-t border-b border-r border-gray-100 py-4 px-3.5 rounded-r-lg text-right">
                            <button type="button" class="text-red-400 transition-colors bg-transparent border-0 cursor-pointer hover:text-red-500" onclick="removeItem(this)">
                                <i class="ph ph-trash text-lg"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button type="button" class="inline-flex items-center gap-1.5 bg-white border border-gray-200 text-gray-600 text-[12px] font-semibold px-3.5 py-2 rounded-lg cursor-pointer transition-colors hover:bg-gray-50 mt-1" id="add-product-btn">
            <i class="ph ph-plus"></i> Tambah Produk
        </button>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-3 mt-4">
        <button type="submit" class="bg-primary text-white text-[13px] font-bold px-6 py-2.5 rounded-lg transition-all hover:bg-dark hover:-translate-y-px shadow-sm shadow-red-100">Buat Pesanan</button>
        <a href="orders.php" class="bg-white border border-gray-200 text-gray-500 text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors hover:bg-gray-50 hover:text-gray-800">Cancel</a>
    </div>
</form>

<script>
    var PRODUCTS = <?php echo $productJson; ?>;

    function fmt(n) {
        return new Intl.NumberFormat('id-ID').format(n);
    }

    function buildProductOptions() {
        var opts = '<option value="" disabled selected>Pilih Produk</option>';
        for (var i = 0; i < PRODUCTS.length; i++) {
            opts += '<option value="' + PRODUCTS[i].id + '" data-price="' + PRODUCTS[i].price + '">' + PRODUCTS[i].name + '</option>';
        }
        return opts;
    }

    function createRow() {
        var tr = document.createElement('tr');
        var td1 = document.createElement('td');
        td1.className = 'bg-white border-t border-b border-l border-gray-100 py-4 px-3.5 rounded-l-lg';
        var wrap = document.createElement('div');
        wrap.className = 'relative';
        var sel = document.createElement('select');
        sel.name = 'products[]';
        sel.className = 'w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 appearance-none cursor-pointer pr-9 product-select';
        sel.required = true;
        sel.innerHTML = buildProductOptions();
        var caret = document.createElement('i');
        caret.className = 'ph ph-caret-down absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none';
        wrap.appendChild(sel);
        wrap.appendChild(caret);
        td1.appendChild(wrap);

        var td2 = document.createElement('td');
        td2.className = 'bg-white border-t border-b border-gray-100 py-4 px-3.5';
        var qtyInput = document.createElement('input');
        qtyInput.type = 'number';
        qtyInput.name = 'qty[]';
        qtyInput.className = 'w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 text-center qty-input';
        qtyInput.value = '1';
        qtyInput.required = true;
        qtyInput.min = '1';
        td2.appendChild(qtyInput);

        var td3 = document.createElement('td');
        td3.className = 'bg-white border-t border-b border-gray-100 py-4 px-3.5';
        var ig3 = document.createElement('div');
        ig3.className = 'relative flex items-center';
        var pre3 = document.createElement('span');
        pre3.className = 'absolute left-3.5 text-[12px] text-gray-400 font-semibold pointer-events-none';
        pre3.textContent = 'Rp';
        var inp3 = document.createElement('input');
        inp3.type = 'text';
        inp3.className = 'w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 pl-9 text-right price-display';
        inp3.value = '0';
        inp3.disabled = true;
        ig3.appendChild(pre3);
        ig3.appendChild(inp3);
        td3.appendChild(ig3);

        var td4 = document.createElement('td');
        td4.className = 'bg-white border-t border-b border-gray-100 py-4 px-3.5';
        var ig4 = document.createElement('div');
        ig4.className = 'relative flex items-center';
        var pre4 = document.createElement('span');
        pre4.className = 'absolute left-3.5 text-[12px] text-gray-400 font-semibold pointer-events-none';
        pre4.textContent = 'Rp';
        var inp4 = document.createElement('input');
        inp4.type = 'text';
        inp4.className = 'w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 pl-9 text-right font-semibold subtotal-display';
        inp4.value = '0';
        inp4.disabled = true;
        ig4.appendChild(pre4);
        ig4.appendChild(inp4);
        td4.appendChild(ig4);

        var td5 = document.createElement('td');
        td5.className = 'bg-white border-t border-b border-r border-gray-100 py-4 px-3.5 rounded-r-lg text-right';
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'text-red-400 transition-colors bg-transparent border-0 cursor-pointer hover:text-red-500';
        var icon = document.createElement('i');
        icon.className = 'ph ph-trash text-lg';
        btn.appendChild(icon);
        btn.addEventListener('click', function() { removeItem(this); });
        td5.appendChild(btn);

        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);
        tr.appendChild(td4);
        tr.appendChild(td5);
        return tr;
    }

    function updateRow(tr) {
        var sel = tr.querySelector('.product-select');
        var qty = parseInt(tr.querySelector('.qty-input').value) || 1;
        var opt = sel.options[sel.selectedIndex];
        var price = parseFloat(opt.getAttribute('data-price')) || 0;
        var subtotal = price * qty;
        tr.querySelector('.price-display').value = fmt(price);
        tr.querySelector('.subtotal-display').value = fmt(subtotal);
    }

    document.getElementById('product-tbody').addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select') || e.target.classList.contains('qty-input')) {
            updateRow(e.target.closest('tr'));
        }
    });
    document.getElementById('product-tbody').addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input')) {
            updateRow(e.target.closest('tr'));
        }
    });

    function removeItem(btn) {
        if (document.querySelectorAll('#product-tbody tr').length > 1) {
            btn.closest('tr').remove();
        } else {
            alert('Pesanan harus memiliki minimal satu produk.');
        }
    }

    document.getElementById('add-product-btn').addEventListener('click', function() {
        document.getElementById('product-tbody').appendChild(createRow());
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
