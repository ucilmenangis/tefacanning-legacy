<?php
$pageTitle   = 'Produk';
$currentPage = 'products';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

require_once __DIR__ . '/../classes/ProductService.php';
require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$productService = new ProductService();
$adminService = new AdminService();
$activityLogService = new ActivityLogService();

// ── POST: Delete ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'delete') {
    $deleteId = intval($_GET['id'] ?? 0);
    if ($deleteId && verifyCsrf()) {
        if ($adminService->isCoreProduct($deleteId)) {
            setFlash('error', 'Produk inti tidak dapat dihapus.');
        } else {
            $productService->softDelete($deleteId);
            $activityLogService->log('deleted', 'App\Models\Product', $deleteId, 'deleted');
            setFlash('success', 'Produk berhasil dihapus.');
        }
    }
    header('Location: products.php');
    exit;
}

include __DIR__ . '/../includes/header-admin.php';

$products = $productService->getAll();
?>



<!-- Page Header -->
<div class="text-[12px] text-gray-400 mb-1">Produk &rsaquo; <span class="text-gray-700">List</span></div>
<div class="flex items-center justify-between mb-5">
    <h1 class="text-[22px] font-extrabold text-navy">Produk</h1>
    <a href="create-product.php" class="inline-flex items-center gap-1.5 bg-primary text-white text-[13px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark" id="btn-new-product">
        <i class="ph-bold ph-plus text-sm"></i> New Produk
    </a>
</div>

<!-- Table -->
<div class="bg-white border border-gray-100 rounded-xl overflow-visible">
    <!-- Toolbar -->
    <div class="flex items-center justify-end gap-2 px-4 py-3 border-b border-gray-50">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="border border-gray-200 rounded-lg py-1.5 pl-[30px] pr-3 text-[12px] outline-none bg-gray-50 w-[200px] transition-colors focus:border-primary focus:bg-white" id="product-search">
        </div>
        <button class="w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-colors hover:bg-gray-50 hover:text-gray-700" title="Filter">
            <i class="ph ph-funnel text-sm"></i>
        </button>
        <button class="w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-primary cursor-pointer transition-colors hover:bg-gray-50" title="Kolom">
            <i class="ph-bold ph-squares-four text-sm"></i>
        </button>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[12.5px] border-collapse">
            <thead>
                <tr>
                    <th class="w-9 text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50"><input type="checkbox" class="w-[15px] h-[15px] accent-primary cursor-pointer" id="cb-all" onchange="toggleAll(this)"></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50"></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Produk <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Harga <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Stok <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Satuan</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Aktif</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                <tr>
                    <td class="w-9 px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle"><input type="checkbox" class="w-[15px] h-[15px] accent-primary cursor-pointer cb-row"></td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle" style="width:44px">
                        <img src="../assets/images/product.jpeg" alt="product" class="w-8 h-8 rounded object-cover border border-gray-100">
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <div class="font-bold text-[12.5px] text-primary"><?php echo htmlspecialchars($prod['name']); ?></div>
                        <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($prod['sku']); ?></div>
                    </td>
                    <td class="font-semibold text-primary px-3.5 py-3 border-b border-gray-50/50 align-middle"><?php echo FormatHelper::rupiah($prod['price']); ?></td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle"><span class="inline-flex items-center justify-center min-w-[38px] px-2 py-0.5 rounded-full text-[11.5px] font-bold bg-emerald-50 text-emerald-600"><?php echo $prod['stock']; ?></span></td>
                    <td class="text-gray-500 px-3.5 py-3 border-b border-gray-50/50 align-middle">kaleng</td>
                    <td>
                        <?php if ($prod['is_active']): ?>
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full border-2 border-teal-400 text-teal-400">
                            <i class="ph ph-check text-xs"></i>
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full border-2 border-gray-300 text-gray-300">
                            <i class="ph ph-x text-xs"></i>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right px-3.5 py-3 border-b border-gray-50/50 align-middle">
                        <div class="relative inline-block text-left dropdown-container">
                                <button type="button" class="w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-colors hover:bg-gray-50 hover:text-gray-700 dropdown-trigger" title="Opsi lainnya" onclick="toggleDropdown(event, this)">
                                <i class="ph ph-dots-three-vertical text-sm pointer-events-none"></i>
                            </button>
                            <div class="hidden absolute right-0 mt-2 w-32 bg-white border border-gray-100 rounded-lg shadow-lg z-50 dropdown-menu text-left">
                                <div class="py-1">
                                    <a href="edit-product.php?id=<?php echo $prod['id']; ?>" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-500 hover:bg-red-50 transition-colors font-medium">
                                        <i class="ph ph-note-pencil text-base text-red-400"></i> Edit
                                    </a>
                                    <button type="button" onclick="confirmDelete(<?php echo $prod['id']; ?>)" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-600 hover:bg-red-50 transition-colors w-full text-left font-medium">
                                        <i class="ph ph-trash text-base text-red-500"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="px-4 py-3 border-t border-gray-50 flex items-center justify-between text-[12px] text-gray-400 gap-3 flex-wrap">
        <span>Showing 1 to <?php echo count($products); ?> of <?php echo count($products); ?> results</span>
        <div class="flex items-center gap-2">
            <span>Per page</span>
            <div class="relative">
                <select class="border border-gray-200 rounded-md px-2 py-1 text-[12px] outline-none bg-white appearance-none cursor-pointer">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                <i class="ph ph-caret-down absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
            </div>
        </div>
    </div>
</div>

<!-- Hidden CSRF for JS actions -->
<div class="hidden"><?php echo csrfField(); ?></div>

<script>
    function toggleAll(master) {
        document.querySelectorAll('.cb-row').forEach(cb => cb.checked = master.checked);
    }
    function toggleDropdown(event, btn) {
        event.stopPropagation();
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== btn.nextElementSibling) menu.classList.add('hidden');
        });
        btn.nextElementSibling.classList.toggle('hidden');
    }
    window.onclick = function(event) {
        if (!event.target.closest('.dropdown-container')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        }
    }
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
    document.getElementById('product-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.data-table tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
