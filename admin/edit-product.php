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



<!-- Breadcrumb & Header -->
<div class="flex items-center justify-between mb-2">
    <div>
        <div class="flex items-center gap-2 text-[12px] text-gray-400 mb-3">
            <a href="products.php" class="hover:text-primary transition-colors">Produk</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="text-slate-600 font-medium"><?php echo htmlspecialchars($product['name']); ?></span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">Edit Produk</h1>
    </div>
    <button type="button" class="inline-flex items-center gap-1 bg-primary text-white text-[12px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark border-none cursor-pointer" onclick="confirmDelete(<?php echo $id; ?>)">
        Delete
    </button>
</div>

<form action="edit-product.php?id=<?php echo $id; ?>" method="POST">
    <?php echo csrfField(); ?>

    <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
        <div class="text-[14px] font-bold text-navy mb-1 flex items-center gap-2">
            <i class="ph ph-package text-lg text-slate-400"></i>
            Informasi Produk
        </div>
        <span class="text-[11px] text-gray-400 font-medium block mb-5">Data produk</span>

        <div class="grid grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Produk<span class="text-primary ml-0.5">*</span></label>
                <input type="text" name="name" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">SKU<span class="text-primary ml-0.5">*</span></label>
                <input type="text" name="sku" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Harga (Rp)</label>
                <?php if ($adminService->canEditPrice()): ?>
                <input type="number" name="price" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" value="<?php echo $product['price']; ?>" min="0" step="1000">
                <?php else: ?>
                <input type="number" name="price" class="w-full border border-gray-100 rounded-lg py-2.5 px-3.5 text-[13px] bg-gray-100 text-slate-500 cursor-not-allowed outline-none" value="<?php echo $product['price']; ?>" disabled>
                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                <p class="text-[10px] text-slate-400 mt-1">Hanya Super Admin yang dapat mengatur harga.</p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Stok</label>
                <input type="number" name="stock" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" value="<?php echo $product['stock']; ?>" min="0">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Aktif</label>
                <label class="flex items-center gap-2 mt-1">
                    <input type="checkbox" name="is_active" value="1" <?php echo $product['is_active'] ? 'checked' : ''; ?> class="w-4 h-4 accent-[#E02424]">
                    <span class="text-[12px] text-slate-600">Produk aktif dan terlihat di katalog</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-3 mt-4">
        <button type="submit" class="bg-primary text-white text-[13px] font-bold px-6 py-2.5 rounded-lg transition-colors hover:bg-dark shadow-sm shadow-red-100 border-none cursor-pointer">Save changes</button>
        <a href="products.php" class="inline-flex items-center justify-center bg-white border border-gray-200 text-slate-500 text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors hover:bg-gray-50 hover:text-navy">Cancel</a>
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
