<?php
/**
 * Admin Edit Order Page
 */

$pageTitle   = 'Edit Order';
$currentPage = 'orders';

require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$adminService = new AdminService();
$activityLogService = new ActivityLogService();

// Validate ID parameter
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    FlashMessage::set('error', 'ID pesanan tidak valid.');
    header('Location: orders.php');
    exit;
}

// Fetch order with customer + batch info
$orderRaw = Database::getInstance()->fetch(
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
    FlashMessage::set('error', 'Pesanan tidak ditemukan.');
    header('Location: orders.php');
    exit;
}

// Fetch order items
$items = Database::getInstance()->fetchAll(
    "SELECT op.id, op.product_id, op.quantity, op.unit_price, op.subtotal, p.name AS product_name
     FROM order_product op
     JOIN products p ON p.id = op.product_id
     WHERE op.order_id = ?",
    [$id]
);

// Fetch all active products for dropdown
$allProducts = Database::getInstance()->fetchAll(
    "SELECT id, name, price FROM products WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC"
);

// ── POST Handlers ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfService::verify()) {
        FlashMessage::set('error', 'Token CSRF tidak valid.');
        header('Location: edit-order.php?id=' . $id);
        exit;
    }

    $action = $_GET['action'] ?? '';

    if ($action === 'delete') {
        Database::getInstance()->update("UPDATE orders SET deleted_at = NOW() WHERE id = ?", [$id]);
        $activityLogService->log('deleted', 'App\Models\Order', $id, 'deleted');
        FlashMessage::set('success', 'Pesanan berhasil dihapus.');
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
    Database::getInstance()->delete("DELETE FROM order_product WHERE order_id = ?", [$id]);

    $totalAmount = 0;
    foreach ($productIds as $i => $productId) {
        $productId = intval($productId);
        $qty = max(1, intval($quantities[$i] ?? 1));

        // Get price from DB (security: never trust form prices)
        $price = $adminService->verifyProductPrice($productId);
        $subtotal = $price * $qty;
        $totalAmount += $subtotal;

        Database::getInstance()->insert(
            "INSERT INTO order_product (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
            [$id, $productId, $qty, $price, $subtotal]
        );
    }

    Database::getInstance()->update(
        "UPDATE orders SET status = ?, total_amount = ?, picked_up_at = ?, updated_at = NOW() WHERE id = ?",
        [$status, $totalAmount, $pickedUpAt, $id]
    );

    $activityLogService->log('updated', 'App\Models\Order', $id, 'updated', [
        'status' => $status,
        'total' => $totalAmount,
    ]);

    FlashMessage::set('success', 'Pesanan berhasil diperbarui.');
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



<!-- Breadcrumb & Header -->
<div class="flex items-center justify-between mb-2">
    <div>
        <div class="flex items-center gap-2 text-[12px] text-gray-400 mb-3">
            <a href="orders.php" class="hover:text-primary transition-colors">Pesanan</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <a href="view-order.php?id=<?php echo $id; ?>" class="hover:text-primary transition-colors"><?php echo htmlspecialchars($order['number']); ?></a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="text-slate-600 font-medium">Edit</span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">Edit <?php echo $order['number']; ?></h1>
    </div>
    <div class="flex items-center gap-3">
        <a href="../api/download-pdf.php?id=<?php echo $id; ?>" class="inline-flex items-center gap-1 bg-white border border-emerald-500 text-emerald-600 text-[12px] font-semibold px-4 py-2 rounded-lg transition-colors hover:bg-emerald-50">
            <i class="ph ph-file-pdf"></i> Download PDF
        </a>
        <button type="button" class="inline-flex items-center gap-1 bg-primary text-white text-[12px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark" onclick="confirmDelete(<?php echo $id; ?>)">
            Delete
        </button>
    </div>
</div>

<form action="edit-order.php?id=<?php echo $id; ?>" method="POST" id="edit-order-form">
    <?php echo CsrfService::field(); ?>
    <input type="hidden" name="order_id" value="<?php echo $id; ?>">

    <div class="grid grid-cols-[1fr_300px] gap-6 items-start">
        <!-- Main Column (Left) -->
        <div class="main-content">
            <!-- Informasi Pesanan -->
            <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
                <div class="text-[14px] font-bold text-navy mb-1 flex items-center gap-2">
                    <i class="ph ph-article-ny-times text-lg text-slate-400"></i>
                    Informasi Pesanan
                </div>
                <span class="text-[11px] text-gray-400 font-medium block mb-5">Detail pesanan pelanggan</span>

                <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nomor Pesanan</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-[12px] text-gray-400 font-semibold pointer-events-none">#</span>
                            <input type="text" class="w-full border border-gray-200 rounded-lg py-2.5 pl-9 pr-3 text-[13px] text-navy bg-gray-100 text-slate-500 cursor-not-allowed outline-none" value="<?php echo $order['number']; ?>" disabled>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Kode Pickup</label>
                        <div class="relative flex items-center">
                            <i class="ph ph-key text-slate-400 absolute left-3.5 text-base pointer-events-none"></i>
                            <input type="text" class="w-full border border-gray-200 rounded-lg py-2.5 pl-9 pr-3 text-[13px] text-navy bg-gray-100 text-slate-500 cursor-not-allowed outline-none" value="<?php echo $order['pickup_code']; ?>" disabled>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Pelanggan<span class="text-primary ml-0.5">*</span></label>
                        <div class="relative">
                            <select name="customer_id" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-gray-100 text-slate-500 cursor-not-allowed outline-none appearance-none" required disabled>
                                <option value="1" selected><?php echo $order['customer']; ?></option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Batch<span class="text-primary ml-0.5">*</span></label>
                        <div class="relative">
                            <select name="batch_id" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-gray-100 text-slate-500 cursor-not-allowed outline-none appearance-none" required disabled>
                                <option value="1" selected><?php echo $order['batch']; ?></option>
                            </select>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block italic">Hanya batch dengan status Open yang dapat dipilih</span>
                    </div>
                </div>
            </div>

            <!-- Item Pesanan -->
            <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
                <div class="text-[14px] font-bold text-navy mb-1 flex items-center gap-2">
                    <i class="ph ph-shopping-cart text-lg text-slate-400"></i>
                    Item Pesanan
                </div>
                <span class="text-[11px] text-gray-400 font-medium block mb-5">Daftar produk yang dipesan</span>

                <div id="product-list-container">
                    <table class="w-full text-[12.5px] border-separate" style="border-spacing: 0 10px; margin-top: -10px;">
                        <thead>
                            <tr>
                                <th class="text-[11px] font-bold text-gray-400 uppercase text-left px-3.5" style="width:45%">Produk<span class="text-primary">*</span></th>
                                <th class="text-[11px] font-bold text-gray-400 uppercase text-left px-3.5" style="width:10%">Qty<span class="text-primary">*</span></th>
                                <th class="text-[11px] font-bold text-gray-400 uppercase text-left px-3.5" style="width:20%">Harga</th>
                                <th class="text-[11px] font-bold text-gray-400 uppercase text-left px-3.5" style="width:20%">Subtotal</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <tbody id="product-tbody">
                            <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td class="bg-white border border-gray-100 rounded-l-xl px-3.5 py-4">
                                    <select name="products[]" class="w-full border border-gray-200 rounded-lg py-2 px-3 text-[13px] text-navy bg-gray-50 outline-none appearance-none cursor-pointer focus:border-primary" required>
                                            <?php foreach ($allProducts as $ap): ?>
                                            <option value="<?php echo $ap['id']; ?>" <?php echo $ap['id'] == $item['product_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($ap['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                </td>
                                <td class="bg-white border-t border-b border-gray-100 px-3.5 py-4">
                                    <input type="number" name="qty[]" class="w-full border border-gray-200 rounded-lg py-2 px-3 text-[13px] text-navy bg-gray-50 outline-none text-center focus:border-primary" value="<?php echo $item['qty']; ?>" required min="1">
                                </td>
                                <td class="bg-white border-t border-b border-gray-100 px-3.5 py-4">
                                    <div class="relative flex items-center">
                                        <span class="absolute left-3.5 text-[12px] text-gray-400 font-semibold pointer-events-none">Rp</span>
                                        <input type="text" class="w-full border border-gray-100 rounded-lg py-2 pl-9 pr-3 text-[13px] bg-gray-100 text-slate-500 cursor-not-allowed outline-none text-right" value="<?php echo number_format($item['price'], 0, ',', '.'); ?>" disabled>
                                    </div>
                                </td>
                                <td class="bg-white border-t border-b border-gray-100 px-3.5 py-4">
                                    <div class="relative flex items-center">
                                        <span class="absolute left-3.5 text-[12px] text-gray-400 font-semibold pointer-events-none">Rp</span>
                                        <input type="text" class="w-full border border-gray-100 rounded-lg py-2 pl-9 pr-3 text-[13px] bg-gray-100 text-slate-500 cursor-not-allowed outline-none text-right font-semibold" value="<?php echo number_format($item['subtotal'], 0, ',', '.'); ?>" disabled>
                                    </div>
                                </td>
                                <td class="bg-white border border-gray-100 rounded-r-xl px-3.5 py-4 text-right">
                                    <button type="button" class="text-red-300 hover:text-red-500 transition-colors bg-none border-none cursor-pointer" onclick="removeItem(this)">
                                        <i class="ph ph-trash text-lg"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="inline-flex items-center gap-1.5 bg-white border border-gray-200 text-slate-600 text-[12px] font-semibold px-3.5 py-2 rounded-lg cursor-pointer transition-colors hover:bg-gray-50 mt-1" id="add-product-btn">
                    <i class="ph ph-plus"></i> Tambah Produk
                </button>
            </div>

            <!-- Catatan -->
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden mb-6 shadow-sm">
                <div class="px-6 py-4 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-colors" onclick="toggleCatatan()">
                    <div class="font-bold text-[13px] text-slate-800 flex items-center gap-2">
                        Catatan
                    </div>
                    <i class="ph ph-caret-down text-[10px] text-slate-400 transition-transform" id="catatan-caret"></i>
                </div>
                <div class="hidden px-6 pb-6" id="catatan-content">
                    <textarea name="notes" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none min-h-[100px] focus:border-primary" placeholder="Masukkan catatan pesanan..."><?php echo $order['notes']; ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center gap-3 mt-4">
                <button type="submit" class="bg-primary text-white text-[13px] font-bold px-6 py-2.5 rounded-lg transition-colors hover:bg-dark shadow-sm shadow-red-100 border-none cursor-pointer">Save changes</button>
                <a href="orders.php" class="inline-flex items-center justify-center bg-white border border-gray-200 text-slate-500 text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors hover:bg-gray-50 hover:text-navy">Cancel</a>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="sidebar-content">
            <!-- Status Pesanan -->
            <div class="bg-white border border-gray-100 rounded-xl p-5 mb-5 shadow-sm">
                <div class="font-bold text-[13px] text-slate-800 mb-4">Status Pesanan</div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Status<span class="text-primary ml-0.5">*</span></label>
                <div class="relative">
                    <select name="status" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-gray-50 outline-none appearance-none cursor-pointer focus:border-primary" required>
                        <?php foreach ($statuses as $val => $label): ?>
                        <option value="<?php echo $val; ?>" <?php echo $order['status'] == $val ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="bg-white border border-gray-100 rounded-xl p-5 mb-5 shadow-sm">
                <div class="font-bold text-[13px] text-slate-800 mb-4">Ringkasan</div>
                <div class="mb-4">
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Total Pesanan</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-[12px] text-gray-400 font-normal pointer-events-none">Rp</span>
                        <input type="text" class="w-full border border-gray-100 rounded-lg py-2.5 pl-9 pr-3 text-right font-mono text-[12px] bg-gray-100 text-slate-500 cursor-not-allowed outline-none" value="<?php echo $order['total']; ?>" disabled>
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Profit</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-[12px] text-gray-400 font-normal pointer-events-none">Rp</span>
                        <input type="text" class="w-full border border-gray-100 rounded-lg py-2.5 pl-9 pr-3 text-right font-mono text-[12px] bg-gray-100 text-slate-500 cursor-not-allowed outline-none" value="<?php echo $order['profit']; ?>" disabled>
                    </div>
                </div>
            </div>

            <!-- Informasi Pickup -->
            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                <div class="font-bold text-[13px] text-slate-800 mb-4">Informasi Pickup</div>
                <div class="mb-4">
                    <div class="block text-[12px] font-semibold text-slate-600 mb-1">Diambil pada</div>
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
