<?php
/**
 * Admin Edit Product Page
 */

$pageTitle   = 'Edit Produk';
$currentPage = 'products';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

require_once __DIR__ . '/../classes/ProductService.php';
require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';

$adminService = new AdminService();
$productService = new ProductService();
$activityLogService = new ActivityLogService();

// Validate ID
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    setFlash('error', 'ID produk tidak valid.');
    header('Location: products.php');
    exit;
}

$product = $productService->getById($id);
if (!$product) {
    setFlash('error', 'Produk tidak ditemukan.');
    header('Location: products.php');
    exit;
}

// ── POST Handler ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash('error', 'Token CSRF tidak valid.');
        header('Location: edit-product.php?id=' . $id);
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name) || empty($sku)) {
        setFlash('error', 'Nama dan SKU produk wajib diisi.');
        header('Location: edit-product.php?id=' . $id);
        exit;
    }

    // teknisi cannot set price — keep existing
    if (!$adminService->canEditPrice()) {
        $price = (float) $product['price'];
    }

    $productService->update($id, [
        'name' => $name,
        'sku' => $sku,
        'price' => $price,
        'stock' => $stock,
        'is_active' => $isActive,
    ]);

    $activityLogService->log('updated', 'App\Models\Product', $id, 'updated', [
        'name' => $name, 'sku' => $sku, 'price' => $price,
    ]);

    setFlash('success', 'Produk berhasil diperbarui.');
    header('Location: edit-product.php?id=' . $id);
    exit;
}

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
    .input:disabled { background: #f1f5f9; color: #64748b; cursor: not-allowed; }

    .btn-save { background: #E02424; color: #fff; font-size: 13px; font-weight: 700; padding: 10px 24px; border-radius: 8px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-save:hover { background: #9B1C1C; transform: translateY(-1px); }

    .btn-cancel { background: #fff; border: 1px solid #e2e8f0; color: #64748b; font-size: 13px; font-weight: 600; padding: 10px 20px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .btn-cancel:hover { background: #f8fafc; color: #1e293b; }

    .btn-delete-top { background: #E02424; color: #fff; font-size: 12px; font-weight: 700; padding: 8px 16px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; border: none; cursor: pointer; }
    .btn-delete-top:hover { background: #9B1C1C; }

    .breadcrumb { font-size: 12px; color: #94a3b8; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .breadcrumb a { color: #94a3b8; text-decoration: none; }
    .breadcrumb a:hover { color: #E02424; }
    .breadcrumb .active { color: #475569; font-weight: 500; }

    .input-prefix { position: absolute; left: 14px; font-size: 12px; color: #94a3b8; font-weight: 600; pointer-events: none; }
    .input-with-prefix { padding-left: 36px !important; }
</style>

<!-- Breadcrumb & Header -->
<div class="flex items-center justify-between mb-2">
    <div>
        <div class="breadcrumb">
            <a href="products.php">Produk</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="active"><?php echo htmlspecialchars($product['name']); ?></span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">Edit Produk</h1>
    </div>
    <button type="button" class="btn-delete-top" onclick="confirmDelete(<?php echo $id; ?>)">
        Delete
    </button>
</div>

<form action="edit-product.php?id=<?php echo $id; ?>" method="POST">
    <?php echo csrfField(); ?>

    <div class="card shadow-sm">
        <div class="card-title">
            <i class="ph ph-package text-lg text-slate-400"></i>
            Informasi Produk
        </div>
        <span class="card-subtitle">Data produk</span>

        <div class="grid grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <label class="label">Nama Produk<span class="required">*</span></label>
                <input type="text" name="name" class="input" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div>
                <label class="label">SKU<span class="required">*</span></label>
                <input type="text" name="sku" class="input" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
            </div>
            <div>
                <label class="label">Harga (Rp)</label>
                <?php if ($adminService->canEditPrice()): ?>
                <input type="number" name="price" class="input" value="<?php echo $product['price']; ?>" min="0" step="1000">
                <?php else: ?>
                <input type="number" name="price" class="input" value="<?php echo $product['price']; ?>" disabled>
                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                <p class="text-[10px] text-slate-400 mt-1">Hanya Super Admin yang dapat mengatur harga.</p>
                <?php endif; ?>
            </div>
            <div>
                <label class="label">Stok</label>
                <input type="number" name="stock" class="input" value="<?php echo $product['stock']; ?>" min="0">
            </div>
            <div>
                <label class="label">Aktif</label>
                <label class="flex items-center gap-2 mt-1">
                    <input type="checkbox" name="is_active" value="1" <?php echo $product['is_active'] ? 'checked' : ''; ?> class="w-4 h-4 accent-[#E02424]">
                    <span class="text-[12px] text-slate-600">Produk aktif dan terlihat di katalog</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-3 mt-4">
        <button type="submit" class="btn-save shadow-sm shadow-red-100">Save changes</button>
        <a href="products.php" class="btn-cancel">Cancel</a>
    </div>
</form>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'products.php?action=delete&id=' + id;
        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = 'csrf_token';
        csrf.value = document.querySelector('input[name="csrf_token"]')?.value || '';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
