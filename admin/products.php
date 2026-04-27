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

<style>
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.breadcrumb  { font-size:12px; color:#9ca3af; margin-bottom:4px; }
.breadcrumb span { color:#374151; }

.btn-primary { display:inline-flex; align-items:center; gap:6px; background:#E02424; color:#fff;
    font-size:13px; font-weight:700; padding:8px 18px; border-radius:8px;
    border:none; cursor:pointer; transition:background .15s; text-decoration:none; }
.btn-primary:hover { background:#9B1C1C; }

.table-wrap    { background:#fff; border:1px solid #f1f5f9; border-radius:12px; overflow:visible; }
.table-toolbar { display:flex; align-items:center; justify-content:flex-end; gap:8px; padding:12px 16px; border-bottom:1px solid #f8fafc; }
.mini-search { border:1px solid #e5e7eb; border-radius:7px; padding:6px 10px 6px 30px; font-size:12px; outline:none; background:#f9fafb; width:200px; transition:border-color .15s; }
.mini-search:focus { border-color:#E02424; background:#fff; }
.icon-btn-sm { width:30px; height:30px; border-radius:6px; border:1px solid #e5e7eb; background:#fff;
    display:inline-flex; align-items:center; justify-content:center; color:#9ca3af; cursor:pointer; transition:background .15s; }
.icon-btn-sm:hover { background:#f8fafc; color:#374151; }

.data-table { width:100%; text-align:left; font-size:12.5px; border-collapse:collapse; }
.data-table th { font-size:11.5px; font-weight:600; color:#9ca3af; padding:10px 14px; border-bottom:1px solid #f1f5f9; white-space:nowrap; background:#fafafa; }
.data-table td { padding:12px 14px; border-bottom:1px solid #f8fafc; color:#374151; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tbody tr:hover td { background:#fafafa; }

.cb-cell { width:36px; }
.cb { width:15px; height:15px; accent-color:#E02424; cursor:pointer; }

.stock-badge { display:inline-flex; align-items:center; justify-content:center;
    min-width:38px; padding:2px 8px; border-radius:999px; font-size:11.5px; font-weight:700;
    background:#ecfdf5; color:#059669; }

.table-footer { padding:12px 16px; border-top:1px solid #f8fafc; display:flex; align-items:center;
    justify-content:space-between; font-size:12px; color:#9ca3af; gap:12px; flex-wrap:wrap; }
.per-page-select { border:1px solid #e5e7eb; border-radius:6px; padding:4px 24px 4px 8px; font-size:12px;
    outline:none; background:#fff; appearance:none; cursor:pointer; }
</style>

<!-- Page Header -->
<div class="breadcrumb">Produk &rsaquo; <span>List</span></div>
<div class="page-header">
    <h1 class="text-[22px] font-extrabold text-navy">Produk</h1>
    <a href="create-product.php" class="btn-primary" id="btn-new-product">
        <i class="ph-bold ph-plus text-sm"></i> New Produk
    </a>
</div>

<!-- Table -->
<div class="table-wrap">
    <!-- Toolbar -->
    <div class="table-toolbar">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="mini-search" id="product-search">
        </div>
        <button class="icon-btn-sm" title="Filter">
            <i class="ph ph-funnel text-sm"></i>
        </button>
        <button class="icon-btn-sm" title="Kolom" style="color:#E02424;">
            <i class="ph-bold ph-squares-four text-sm"></i>
        </button>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="cb-cell"><input type="checkbox" class="cb" id="cb-all" onchange="toggleAll(this)"></th>
                    <th></th><!-- product image col -->
                    <th>Produk <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Harga <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Stok <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Satuan</th>
                    <th>Aktif</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                <tr>
                    <td class="cb-cell"><input type="checkbox" class="cb cb-row"></td>
                    <td style="width:44px">
                        <img src="../assets/images/product.jpeg" alt="product" class="w-8 h-8 rounded object-cover border border-gray-100">
                    </td>
                    <td>
                        <div class="font-bold text-[12.5px]" style="color:#E02424"><?php echo htmlspecialchars($prod['name']); ?></div>
                        <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($prod['sku']); ?></div>
                    </td>
                    <td class="font-semibold" style="color:#E02424"><?php echo FormatHelper::rupiah($prod['price']); ?></td>
                    <td><span class="stock-badge"><?php echo $prod['stock']; ?></span></td>
                    <td class="text-gray-500">kaleng</td>
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
                    <td class="text-right">
                        <div class="relative inline-block text-left dropdown-container">
                            <button type="button" class="icon-btn-sm dropdown-trigger" title="Opsi lainnya" onclick="toggleDropdown(event, this)">
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
    <div class="table-footer">
        <span>Showing 1 to <?php echo count($products); ?> of <?php echo count($products); ?> results</span>
        <div class="flex items-center gap-2">
            <span>Per page</span>
            <div class="relative">
                <select class="per-page-select">
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
