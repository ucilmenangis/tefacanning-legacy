<?php
/**
 * Admin Edit Order Page
 */

$pageTitle   = 'Edit Order';
$currentPage = 'orders';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$adminService = new AdminService();
$activityLogService = new ActivityLogService();

// Validate ID parameter
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    setFlash('error', 'ID pesanan tidak valid.');
    header('Location: orders.php');
    exit;
}

// Fetch order with customer + batch info
$orderRaw = db_fetch(
    "SELECT o.id, o.order_number, o.pickup_code, o.status, o.total_amount, o.profit, o.picked_up_at, o.created_at,
            o.customer_id, o.batch_id,
            c.name AS customer_name, b.name AS batch_name
     FROM orders o
     JOIN customers c ON c.id = o.customer_id
     JOIN batches b ON b.id = o.batch_id
     WHERE o.id = ? AND o.deleted_at IS NULL",
    [$id]
);

if (!$orderRaw) {
    setFlash('error', 'Pesanan tidak ditemukan.');
    header('Location: orders.php');
    exit;
}

// Fetch order items
$items = db_fetch_all(
    "SELECT op.id, op.product_id, op.quantity, op.unit_price, op.subtotal, p.name AS product_name
     FROM order_product op
     JOIN products p ON p.id = op.product_id
     WHERE op.order_id = ?",
    [$id]
);

// Fetch all active products for dropdown
$allProducts = db_fetch_all(
    "SELECT id, name, price FROM products WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC"
);

// ── POST Handlers ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash('error', 'Token CSRF tidak valid.');
        header('Location: edit-order.php?id=' . $id);
        exit;
    }

    $action = $_GET['action'] ?? '';

    if ($action === 'delete') {
        db_update("UPDATE orders SET deleted_at = NOW() WHERE id = ?", [$id]);
        $activityLogService->log('deleted', 'App\Models\Order', $id, 'deleted');
        setFlash('success', 'Pesanan berhasil dihapus.');
        header('Location: orders.php');
        exit;
    }

    // Update order
    $status = $_POST['status'] ?? $orderRaw['status'];
    $productIds = $_POST['products'] ?? [];
    $quantities = $_POST['qty'] ?? [];

    // Validate status transition
    $validStatuses = ['pending', 'processing', 'ready', 'picked_up'];
    if (!in_array($status, $validStatuses)) {
        $status = $orderRaw['status'];
    }

    // If picked_up, set timestamp
    $pickedUpAt = $orderRaw['picked_up_at'];
    if ($status === 'picked_up' && !$pickedUpAt) {
        $pickedUpAt = date('Y-m-d H:i:s');
    }

    // Delete existing order_product rows and recalculate
    db_delete("DELETE FROM order_product WHERE order_id = ?", [$id]);

    $totalAmount = 0;
    foreach ($productIds as $i => $productId) {
        $productId = intval($productId);
        $qty = max(1, intval($quantities[$i] ?? 1));

        // Get price from DB (security: never trust form prices)
        $price = $adminService->verifyProductPrice($productId);
        $subtotal = $price * $qty;
        $totalAmount += $subtotal;

        db_insert(
            "INSERT INTO order_product (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
            [$id, $productId, $qty, $price, $subtotal]
        );
    }

    db_update(
        "UPDATE orders SET status = ?, total_amount = ?, picked_up_at = ?, updated_at = NOW() WHERE id = ?",
        [$status, $totalAmount, $pickedUpAt, $id]
    );

    $activityLogService->log('updated', 'App\Models\Order', $id, 'updated', [
        'status' => $status,
        'total' => $totalAmount,
    ]);

    setFlash('success', 'Pesanan berhasil diperbarui.');
    header('Location: edit-order.php?id=' . $id);
    exit;
}

// Build template-ready order data
$order = [
    'number'     => $orderRaw['order_number'],
    'pickup_code' => $orderRaw['pickup_code'],
    'customer'   => $orderRaw['customer_name'],
    'batch'      => $orderRaw['batch_name'],
    'status'     => $orderRaw['status'],
    'total'      => $orderRaw['total_amount'],
    'profit'     => $orderRaw['profit'] ?? '0.00',
    'pickup_at'  => $orderRaw['picked_up_at'] ? FormatHelper::tanggal($orderRaw['picked_up_at']) : 'Belum diambil',
    'created_at' => FormatHelper::tanggal($orderRaw['created_at']),
    'items'      => array_map(function($item) {
        return [
            'id'       => $item['id'],
            'product_id' => $item['product_id'],
            'product'  => $item['product_name'],
            'qty'      => $item['quantity'],
            'price'    => $item['unit_price'],
            'subtotal' => $item['subtotal'],
        ];
    }, $items),
    'notes' => ''
];

$statuses = [
    'pending' => 'Pending',
    'processing' => 'Processing',
    'ready' => 'Ready',
    'picked_up' => 'Picked Up'
];

include __DIR__ . '/../includes/header-admin.php';
?>

<style>
    /* Custom Styles for Form */
    .card { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
    .card-title { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .card-subtitle { font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: -12px; margin-bottom: 20px; display: block; }
    
    .label { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .label .required { color: #E02424; margin-left: 2px; }
    
    .input { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #1e293b; background: #f9fafb; transition: all 0.2s; outline: none; }
    .input:focus { border-color: #E02424; background: #fff; box-shadow: 0 0 0 3px rgba(224, 36, 36, 0.05); }
    .input:disabled { background: #f1f5f9; color: #64748b; cursor: not-allowed; }

    .select-wrapper { position: relative; }
    .select-wrapper::after { content: "\e8d3"; font-family: "Phosphor"; position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #94a3b8; pointer-events: none; }
    .select { appearance: none; cursor: pointer; padding-right: 36px !important; }

    .table-items { width: 100%; font-size: 12.5px; border-collapse: separate; border-spacing: 0 12px; margin-top: -12px; }
    .table-items th { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; text-align: left; padding: 0 14px; }
    .table-items td { background: #fff; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; padding: 16px 14px; }
    .table-items td:first-child { border-left: 1px solid #f1f5f9; border-radius: 10px 0 0 10px; }
    .table-items td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 10px 10px 0; }

    .btn-add { display: inline-flex; align-items: center; gap: 6px; background: #fff; border: 1px solid #e2e8f0; color: #475569; font-size: 12px; font-weight: 600; padding: 8px 14px; border-radius: 8px; cursor: pointer; transition: all 0.2s; margin-top: 4px; }
    .btn-add:hover { background: #f8fafc; border-color: #cbd5e1; }

    .btn-save { background: #E02424; color: #fff; font-size: 13px; font-weight: 700; padding: 10px 24px; border-radius: 8px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-save:hover { background: #9B1C1C; transform: translateY(-1px); }

    .btn-cancel { background: #fff; border: 1px solid #e2e8f0; color: #64748b; font-size: 13px; font-weight: 600; padding: 10px 20px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .btn-cancel:hover { background: #f8fafc; color: #1e293b; }

    .btn-delete-top { background: #E02424; color: #fff; font-size: 12px; font-weight: 700; padding: 8px 16px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-delete-top:hover { background: #9B1C1C; }

    .btn-delete-item { color: #f87171; transition: color 0.2s; background: none; border: none; cursor: pointer; }
    .btn-delete-item:hover { color: #ef4444; }

    .breadcrumb { font-size: 12px; color: #94a3b8; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .breadcrumb a { color: #94a3b8; text-decoration: none; }
    .breadcrumb a:hover { color: #E02424; }
    .breadcrumb .active { color: #475569; font-weight: 500; }

    .input-group { position: relative; display: flex; align-items: center; }
    .input-prefix { position: absolute; left: 14px; font-size: 12px; color: #94a3b8; font-weight: 600; pointer-events: none; }
    .input-with-prefix { padding-left: 36px !important; }

    .collapsible-header { cursor: pointer; display: flex; align-items: center; justify-content: space-between; }
    .collapsible-header i.caret { transition: transform 0.2s; }
    .collapsible-header.open i.caret { transform: rotate(180deg); }

    /* Layout override */
    .edit-grid { display: grid; grid-template-columns: 1fr 300px; gap: 24px; align-items: start; }
</style>

<!-- Breadcrumb & Header -->
<div class="flex items-center justify-between mb-2">
    <div>
        <div class="breadcrumb">
            <a href="orders.php">Pesanan</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <a href="view-order.php?id=<?php echo $id; ?>"><?php echo htmlspecialchars($order['number']); ?></a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="active">Edit</span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">Edit <?php echo $order['number']; ?></h1>
    </div>
    <button type="button" class="btn-delete-top" onclick="confirmDelete(<?php echo $id; ?>)">
        Delete
    </button>
</div>

<form action="edit-order.php?id=<?php echo $id; ?>" method="POST" id="edit-order-form">
    <?php echo csrfField(); ?>
    <input type="hidden" name="order_id" value="<?php echo $id; ?>">

    <div class="edit-grid">
        <!-- Main Column (Left) -->
        <div class="main-content">
            <!-- Informasi Pesanan -->
            <div class="card shadow-sm">
                <div class="card-title">
                    <i class="ph ph-article-ny-times text-lg text-slate-400"></i>
                    Informasi Pesanan
                </div>
                <span class="card-subtitle">Detail pesanan pelanggan</span>

                <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                    <div>
                        <label class="label">Nomor Pesanan</label>
                        <div class="input-group">
                            <span class="input-prefix">#</span>
                            <input type="text" class="input input-with-prefix" value="<?php echo $order['number']; ?>" disabled>
                        </div>
                    </div>
                    <div>
                        <label class="label">Kode Pickup</label>
                        <div class="input-group">
                            <i class="ph ph-key text-slate-400 absolute left-3.5 text-base pointer-events-none"></i>
                            <input type="text" class="input pl-10" value="<?php echo $order['pickup_code']; ?>" disabled>
                        </div>
                    </div>
                    <div>
                        <label class="label">Pelanggan<span class="required">*</span></label>
                        <div class="select-wrapper">
                            <select name="customer_id" class="input select" required disabled>
                                <option value="1" selected><?php echo $order['customer']; ?></option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Batch<span class="required">*</span></label>
                        <div class="select-wrapper">
                            <select name="batch_id" class="input select" required disabled>
                                <option value="1" selected><?php echo $order['batch']; ?></option>
                            </select>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block italic">Hanya batch dengan status Open yang dapat dipilih</span>
                    </div>
                </div>
            </div>

            <!-- Item Pesanan -->
            <div class="card shadow-sm">
                <div class="card-title">
                    <i class="ph ph-shopping-cart text-lg text-slate-400"></i>
                    Item Pesanan
                </div>
                <span class="card-subtitle">Daftar produk yang dipesan</span>

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
                            <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="select-wrapper">
                                        <select name="products[]" class="input select" required>
                                            <?php foreach ($allProducts as $ap): ?>
                                            <option value="<?php echo $ap['id']; ?>" <?php echo $ap['id'] == $item['product_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($ap['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" name="qty[]" class="input text-center" value="<?php echo $item['qty']; ?>" required min="1">
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-prefix">Rp</span>
                                        <input type="text" class="input input-with-prefix text-right" value="<?php echo number_format($item['price'], 0, ',', '.'); ?>" disabled>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-prefix">Rp</span>
                                        <input type="text" class="input input-with-prefix text-right font-semibold" value="<?php echo number_format($item['subtotal'], 0, ',', '.'); ?>" disabled>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <button type="button" class="btn-delete-item" onclick="removeItem(this)">
                                        <i class="ph ph-trash text-lg"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn-add" id="add-product-btn">
                    <i class="ph ph-plus"></i> Tambah Produk
                </button>
            </div>

            <!-- Catatan -->
            <div class="card shadow-sm p-0 overflow-hidden">
                <div class="px-6 py-4 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-colors" onclick="toggleCatatan()">
                    <div class="font-bold text-[13px] text-slate-800 flex items-center gap-2">
                        Catatan
                    </div>
                    <i class="ph ph-caret-down text-[10px] text-slate-400 transition-transform" id="catatan-caret"></i>
                </div>
                <div class="hidden px-6 pb-6" id="catatan-content">
                    <textarea name="notes" class="input min-h-[100px] bg-white" placeholder="Masukkan catatan pesanan..."><?php echo $order['notes']; ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center gap-3 mt-4">
                <button type="submit" class="btn-save shadow-sm shadow-red-100">Save changes</button>
                <a href="orders.php" class="btn-cancel">Cancel</a>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="sidebar-content">
            <!-- Status Pesanan -->
            <div class="card shadow-sm p-5 mb-5">
                <div class="font-bold text-[13px] text-slate-800 mb-4">Status Pesanan</div>
                <label class="label">Status<span class="required">*</span></label>
                <div class="select-wrapper">
                    <select name="status" class="input select" required>
                        <?php foreach ($statuses as $val => $label): ?>
                        <option value="<?php echo $val; ?>" <?php echo $order['status'] == $val ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="card shadow-sm p-5 mb-5">
                <div class="font-bold text-[13px] text-slate-800 mb-4">Ringkasan</div>
                <div class="mb-4">
                    <label class="label">Total Pesanan</label>
                    <div class="input-group">
                        <span class="input-prefix text-xs font-normal">Rp</span>
                        <input type="text" class="input input-with-prefix text-right font-mono text-[12px]" value="<?php echo $order['total']; ?>" disabled>
                    </div>
                </div>
                <div>
                    <label class="label">Profit</label>
                    <div class="input-group">
                        <span class="input-prefix text-xs font-normal">Rp</span>
                        <input type="text" class="input input-with-prefix text-right font-mono text-[12px]" value="<?php echo $order['profit']; ?>" disabled>
                    </div>
                </div>
            </div>

            <!-- Informasi Pickup -->
            <div class="card shadow-sm p-5">
                <div class="font-bold text-[13px] text-slate-800 mb-4">Informasi Pickup</div>
                <div class="mb-4">
                    <div class="label">Diambil pada</div>
                    <div class="text-[12px] text-slate-500 font-medium"><?php echo $order['pickup_at']; ?></div>
                </div>
                <div>
                    <div class="label">Dibuat</div>
                    <div class="text-[12px] text-slate-500 font-medium"><?php echo $order['created_at']; ?></div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleCatatan() {
        const content = document.getElementById('catatan-content');
        const caret = document.getElementById('catatan-caret');
        content.classList.toggle('hidden');
        caret.classList.toggle('rotate-180');
    }

    function removeItem(btn) {
        if (document.querySelectorAll('#product-tbody tr').length > 1) {
            btn.closest('tr').remove();
        } else {
            alert('Pesanan harus memiliki minimal satu produk.');
        }
    }

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pesanan ini?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'edit-order.php?action=delete&id=' + id;
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = document.querySelector('input[name="csrf_token"]')?.value || '';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.getElementById('add-product-btn').addEventListener('click', function() {
        const tbody = document.getElementById('product-tbody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <div class="select-wrapper">
                    <select name="products[]" class="input select" required>
                        <option value="" disabled selected>Pilih Produk</option>
                        <?php foreach ($allProducts as $ap): ?>
                        <option value="<?php echo $ap['id']; ?>"><?php echo htmlspecialchars(addslashes($ap['name'])); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </td>
            <td>
                <input type="number" name="qty[]" class="input text-center" value="1" required min="1">
            </td>
            <td>
                <div class="input-group">
                    <span class="input-prefix">Rp</span>
                    <input type="text" class="input input-with-prefix text-right" value="0" disabled>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-prefix">Rp</span>
                    <input type="text" class="input input-with-prefix text-right font-semibold" value="0" disabled>
                </div>
            </td>
            <td class="text-right">
                <button type="button" class="btn-delete-item" onclick="removeItem(this)">
                    <i class="ph ph-trash text-lg"></i>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
