<?php
/**
 * Admin Create Order Page
 */

$pageTitle   = 'Buat Pesanan';
$currentPage = 'orders';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$adminService = new AdminService();
$activityLogService = new ActivityLogService();

// Build JS-safe product data for dynamic add row
$products = db_fetch_all(
    "SELECT id, name, sku, price FROM products WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC"
);
$productJson = json_encode(array_map(function($p) {
    return ['id' => (int)$p['id'], 'name' => $p['name'], 'price' => (float)$p['price']];
}, $products), JSON_UNESCAPED_UNICODE);

// ── POST Handler ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash('error', 'Token CSRF tidak valid.');
        header('Location: create-order.php');
        exit;
    }

    $customerId = intval($_POST['customer_id'] ?? 0);
    $batchId    = intval($_POST['batch_id'] ?? 0);
    $productIds = $_POST['products'] ?? [];
    $quantities = $_POST['qty'] ?? [];

    if (!$customerId || !$batchId || empty($productIds)) {
        setFlash('error', 'Pelanggan, batch, dan minimal 1 produk wajib diisi.');
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
        setFlash('error', 'Tidak ada produk valid yang dipilih.');
        header('Location: create-order.php');
        exit;
    }

    // Insert order
    $orderId = db_insert(
        "INSERT INTO orders (customer_id, batch_id, order_number, pickup_code, status, total_amount, profit, created_at, updated_at)
         VALUES (?, ?, ?, ?, 'pending', ?, 0, NOW(), NOW())",
        [$customerId, $batchId, $orderNumber, $pickupCode, $totalAmount]
    );

    // Insert order items
    foreach ($orderItems as $item) {
        db_insert(
            "INSERT INTO order_product (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
            [$orderId, $item[0], $item[1], $item[2], $item[3]]
        );
    }

    $activityLogService->log('created', 'App\Models\Order', $orderId, 'created', [
        'order_number' => $orderNumber,
        'total' => $totalAmount,
    ]);

    setFlash('success', 'Pesanan berhasil dibuat.');
    header('Location: view-order.php?id=' . $orderId);
    exit;
}

// Fetch data for form
$customers = db_fetch_all(
    "SELECT id, name, phone FROM customers WHERE deleted_at IS NULL ORDER BY name ASC"
);
$batches = db_fetch_all(
    "SELECT id, name, event_name, event_date FROM batches WHERE status = 'open' AND deleted_at IS NULL ORDER BY created_at DESC"
);

include __DIR__ . '/../includes/header-admin.php';
?>

<style>
    .card { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
    .card-title { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .card-subtitle { font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: -12px; margin-bottom: 20px; display: block; }

    .label { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .label .required { color: #E02424; margin-left: 2px; }

    .input { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #1e293b; background: #fff; transition: all 0.2s; outline: none; }
    .input:focus { border-color: #E02424; box-shadow: 0 0 0 3px rgba(224, 36, 36, 0.05); }

    .select-wrapper { position: relative; }
    .select-wrapper::after { content: "\e8d3"; font-family: "Phosphor"; position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #94a3b8; pointer-events: none; }
    .select { appearance: none; cursor: pointer; padding-right: 36px !important; }

    .table-items { width: 100%; font-size: 12.5px; border-collapse: separate; border-spacing: 0 12px; margin-top: -12px; }
    .table-items th { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; text-align: left; padding: 0 14px; }
    .table-items td { background: #fff; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; padding: 16px 14px; }
    .table-items td:first-child { border-left: 1px solid #f1f5f9; border-radius: 10px 0 0 10px; }
    .table-items td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 10px 10px 0; }

    .btn-save { background: #E02424; color: #fff; font-size: 13px; font-weight: 700; padding: 10px 24px; border-radius: 8px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-save:hover { background: #9B1C1C; transform: translateY(-1px); }

    .btn-cancel { background: #fff; border: 1px solid #e2e8f0; color: #64748b; font-size: 13px; font-weight: 600; padding: 10px 20px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .btn-cancel:hover { background: #f8fafc; color: #1e293b; }

    .btn-add { display: inline-flex; align-items: center; gap: 6px; background: #fff; border: 1px solid #e2e8f0; color: #475569; font-size: 12px; font-weight: 600; padding: 8px 14px; border-radius: 8px; cursor: pointer; transition: all 0.2s; margin-top: 4px; }
    .btn-add:hover { background: #f8fafc; border-color: #cbd5e1; }

    .btn-delete-item { color: #f87171; transition: color 0.2s; background: none; border: none; cursor: pointer; }
    .btn-delete-item:hover { color: #ef4444; }

    .breadcrumb { font-size: 12px; color: #94a3b8; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .breadcrumb a { color: #94a3b8; text-decoration: none; }
    .breadcrumb a:hover { color: #E02424; }
    .breadcrumb .active { color: #475569; font-weight: 500; }

    .input-group { position: relative; display: flex; align-items: center; }
    .input-prefix { position: absolute; left: 14px; font-size: 12px; color: #94a3b8; font-weight: 600; pointer-events: none; }
    .input-with-prefix { padding-left: 36px !important; }
</style>

<!-- Header -->
<div class="mb-2">
    <div class="breadcrumb">
        <a href="orders.php">Pesanan</a>
        <i class="ph ph-caret-right text-[10px]"></i>
        <span class="active">Buat Pesanan Baru</span>
    </div>
    <h1 class="text-[24px] font-extrabold text-navy">Buat Pesanan</h1>
</div>

<form action="create-order.php" method="POST">
    <?php echo csrfField(); ?>

    <!-- Informasi Pesanan -->
    <div class="card shadow-sm">
        <div class="card-title">
            <i class="ph ph-article-ny-times text-lg text-slate-400"></i>
            Informasi Pesanan
        </div>
        <span class="card-subtitle">Pilih pelanggan dan batch</span>

        <div class="grid grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <label class="label">Pelanggan<span class="required">*</span></label>
                <div class="select-wrapper">
                    <select name="customer_id" class="input select" required>
                        <option value="" disabled selected>Pilih Pelanggan</option>
                        <?php foreach ($customers as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name'] . ($c['phone'] ? ' — ' . $c['phone'] : '')); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="label">Batch<span class="required">*</span></label>
                <div class="select-wrapper">
                    <select name="batch_id" class="input select" required>
                        <option value="" disabled selected>Pilih Batch</option>
                        <?php foreach ($batches as $b): ?>
                        <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['name'] . ' — ' . $b['event_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block italic">Hanya batch dengan status Open yang tersedia</span>
            </div>
        </div>
    </div>

    <!-- Item Pesanan -->
    <div class="card shadow-sm">
        <div class="card-title">
            <i class="ph ph-shopping-cart text-lg text-slate-400"></i>
            Item Pesanan
        </div>
        <span class="card-subtitle">Tambahkan produk ke pesanan</span>

        <div id="product-list-container">
            <table class="table-items">
                <thead>
                    <tr>
                        <th style="width: 45%;">Produk<span class="required">*</span></th>
                        <th style="width: 10%;">Qty<span class="required">*</span></th>
                        <th style="width: 20%;">Harga</th>
                        <th style="width: 20%;">Subtotal</th>
                        <th style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody id="product-tbody">
                    <tr>
                        <td>
                            <div class="select-wrapper">
                                <select name="products[]" class="input select product-select" required>
                                    <option value="" disabled selected>Pilih Produk</option>
                                    <?php foreach ($products as $p): ?>
                                    <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <input type="number" name="qty[]" class="input text-center qty-input" value="1" required min="1">
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-prefix">Rp</span>
                                <input type="text" class="input input-with-prefix text-right price-display" value="0" disabled>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-prefix">Rp</span>
                                <input type="text" class="input input-with-prefix text-right font-semibold subtotal-display" value="0" disabled>
                            </div>
                        </td>
                        <td class="text-right">
                            <button type="button" class="btn-delete-item" onclick="removeItem(this)">
                                <i class="ph ph-trash text-lg"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn-add" id="add-product-btn">
            <i class="ph ph-plus"></i> Tambah Produk
        </button>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-3 mt-4">
        <button type="submit" class="btn-save shadow-sm shadow-red-100">Buat Pesanan</button>
        <a href="orders.php" class="btn-cancel">Cancel</a>
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
        var wrap = document.createElement('div');
        wrap.className = 'select-wrapper';
        var sel = document.createElement('select');
        sel.name = 'products[]';
        sel.className = 'input select product-select';
        sel.required = true;
        sel.innerHTML = buildProductOptions();
        wrap.appendChild(sel);
        td1.appendChild(wrap);

        var td2 = document.createElement('td');
        var qtyInput = document.createElement('input');
        qtyInput.type = 'number';
        qtyInput.name = 'qty[]';
        qtyInput.className = 'input text-center qty-input';
        qtyInput.value = '1';
        qtyInput.required = true;
        qtyInput.min = '1';
        td2.appendChild(qtyInput);

        var td3 = document.createElement('td');
        var ig3 = document.createElement('div');
        ig3.className = 'input-group';
        var pre3 = document.createElement('span');
        pre3.className = 'input-prefix';
        pre3.textContent = 'Rp';
        var inp3 = document.createElement('input');
        inp3.type = 'text';
        inp3.className = 'input input-with-prefix text-right price-display';
        inp3.value = '0';
        inp3.disabled = true;
        ig3.appendChild(pre3);
        ig3.appendChild(inp3);
        td3.appendChild(ig3);

        var td4 = document.createElement('td');
        var ig4 = document.createElement('div');
        ig4.className = 'input-group';
        var pre4 = document.createElement('span');
        pre4.className = 'input-prefix';
        pre4.textContent = 'Rp';
        var inp4 = document.createElement('input');
        inp4.type = 'text';
        inp4.className = 'input input-with-prefix text-right font-semibold subtotal-display';
        inp4.value = '0';
        inp4.disabled = true;
        ig4.appendChild(pre4);
        ig4.appendChild(inp4);
        td4.appendChild(ig4);

        var td5 = document.createElement('td');
        td5.className = 'text-right';
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-delete-item';
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
