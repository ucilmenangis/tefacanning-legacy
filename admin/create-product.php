<?php
/**
 * Admin Create Product Page
 */

$pageTitle   = 'New Produk';
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

// ── POST Handler ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash('error', 'Token CSRF tidak valid.');
        header('Location: create-product.php');
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name) || empty($sku)) {
        setFlash('error', 'Nama dan SKU produk wajib diisi.');
        header('Location: create-product.php');
        exit;
    }

    // teknisi cannot set price — force to 0
    if (!$adminService->canEditPrice()) {
        $price = 0;
    }

    $newId = $productService->create([
        'name' => $name,
        'sku' => $sku,
        'price' => $price,
        'stock' => $stock,
        'is_active' => $isActive,
    ]);

    $activityLogService->log('created', 'App\Models\Product', $newId, 'created', [
        'name' => $name, 'sku' => $sku, 'price' => $price,
    ]);

    setFlash('success', 'Produk berhasil ditambahkan.');
    header('Location: products.php');
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
            <span class="text-slate-600 font-medium">Create</span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">New Produk</h1>
    </div>
</div>

<form action="create-product.php" method="POST">
    <?php echo csrfField(); ?>

    <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
        <div class="text-[14px] font-bold text-navy mb-1 flex items-center gap-2">
            <i class="ph ph-package text-lg text-slate-400"></i>
            Informasi Produk
        </div>
        <span class="text-[11px] text-gray-400 font-medium block mb-5">Data produk baru</span>

        <div class="grid grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Produk<span class="text-primary ml-0.5">*</span></label>
                <input type="text" name="name" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" placeholder="Contoh: Sarden SIP Asin" required>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">SKU<span class="text-primary ml-0.5">*</span></label>
                <input type="text" name="sku" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" placeholder="Contoh: TEFA-ASN-001" required>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Harga (Rp)</label>
                <?php if ($adminService->canEditPrice()): ?>
                <input type="number" name="price" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" value="0" min="0" step="1000">
                <?php else: ?>
                <input type="number" name="price" class="w-full border border-gray-100 rounded-lg py-2.5 px-3.5 text-[13px] bg-gray-100 text-slate-500 cursor-not-allowed outline-none" value="0" disabled>
                <p class="text-[10px] text-slate-400 mt-1">Hanya Super Admin yang dapat mengatur harga.</p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Stok</label>
                <input type="number" name="stock" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" value="0" min="0">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Aktif</label>
                <label class="flex items-center gap-2 mt-1">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 accent-[#E02424]">
                    <span class="text-[12px] text-slate-600">Produk aktif dan terlihat di katalog</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-3 mt-4">
        <button type="submit" class="bg-primary text-white text-[13px] font-bold px-6 py-2.5 rounded-lg transition-colors hover:bg-dark shadow-sm shadow-red-100 border-none cursor-pointer">Create Produk</button>
        <a href="products.php" class="inline-flex items-center justify-center bg-white border border-gray-200 text-slate-500 text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors hover:bg-gray-50 hover:text-navy">Cancel</a>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
