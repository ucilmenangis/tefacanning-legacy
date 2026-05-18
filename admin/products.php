<?php
$pageTitle = 'Produk';
$currentPage = 'products';
require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();

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
    if ($deleteId && CsrfService::verify()) {
        if ($adminService->isCoreProduct($deleteId)) {
            FlashMessage::set('error', 'Produk inti tidak dapat dihapus. Alasan: Produk ini merupakan produk utama bawaan sistem TEFA.');
        } else {
            $productService->softDelete($deleteId);
            $activityLogService->log('deleted', 'App\Models\Product', $deleteId, 'deleted');
            FlashMessage::set('success', 'Produk berhasil dihapus.');
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
    <a href="create-product.php"
        class="inline-flex items-center gap-1.5 bg-primary text-white text-[13px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark"
        id="btn-new-product">
        <i class="ph-bold ph-plus text-sm"></i> New Produk
    </a>
</div>

<div class="bg-white border border-gray-100 rounded-xl overflow-visible">
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 px-4 py-3 border-b border-gray-50">
        <!-- Search -->
        <div class="relative group flex-1 w-full sm:max-w-[240px]">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[14px]"></i>
            <input type="text" placeholder="Search"
                class="border border-gray-200 rounded-lg py-2 pl-[34px] pr-3 text-[13px] outline-none bg-white w-full transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                id="product-search">
        </div>

        <div class="flex items-center justify-end gap-2">

        <!-- Filter Dropdown -->
        <div class="relative">
            <button
                class="w-[36px] h-[36px] rounded-lg border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-all hover:bg-gray-50 hover:text-navy active:scale-95"
                title="Filter" id="btn-filter" onclick="toggleFilterMenu(event)">
                <i class="ph ph-funnel text-[18px]"></i>
            </button>
            <div id="filter-menu"
                class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-xl z-[100] py-1.5">
                <div class="px-3 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Filter</div>
                <button onclick="setStatusFilter('all')"
                    class="w-full text-left px-4 py-2 text-[12.5px] text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    All Products
                </button>
                <button onclick="setStatusFilter('active')"
                    class="w-full text-left px-4 py-2 text-[12.5px] text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-400"></span> Active Only
                </button>
                <button onclick="setStatusFilter('inactive')"
                    class="w-full text-left px-4 py-2 text-[12.5px] text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-gray-300"></span> Inactive Only
                </button>
            </div>
        </div>

        <!-- Layout Toggle -->
        <button
            class="w-[36px] h-[36px] rounded-lg border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-all hover:bg-gray-50 hover:text-navy active:scale-95"
            title="Toggle Layout" onclick="toggleLayout()">
            <i class="ph ph-squares-four text-[18px]" id="layout-icon"></i>
        </button>
        </div>
    </div>

    <!-- Data Table View -->
    <div id="table-view" class="overflow-x-auto">
        <table class="w-full text-left text-[12.5px] border-collapse">
            <thead>
                <tr>
                    <th
                        class="w-9 text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">
                        <input type="checkbox" class="w-[15px] h-[15px] accent-primary cursor-pointer" id="cb-all"
                            onchange="toggleAll(this)">
                    </th>
                    <th class="w-12 border-b border-gray-100 bg-gray-50/50"></th>
                    <th
                        class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">
                        Produk</th>
                    <th
                        class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">
                        Harga</th>
                    <th
                        class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">
                        Stok</th>
                    <th
                        class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">
                        Status</th>
                    <th
                        class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50 text-right">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                    <tr class="product-row transition-colors hover:bg-gray-50/50"
                        data-active="<?php echo $prod['is_active'] ? 'yes' : 'no'; ?>"
                        data-name="<?php echo strtolower($prod['name']); ?>"
                        data-sku="<?php echo strtolower($prod['sku']); ?>">
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle"><input type="checkbox"
                                class="w-[15px] h-[15px] accent-primary cursor-pointer cb-row"></td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle">
                            <img src="../assets/images/product.jpeg" alt="product"
                                class="w-8 h-8 rounded object-cover border border-gray-100">
                        </td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle">
                            <div class="font-bold text-[12.5px] text-primary"><?php echo htmlspecialchars($prod['name']); ?>
                            </div>
                            <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($prod['sku']); ?></div>
                        </td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle font-semibold text-primary">
                            <?php echo FormatHelper::rupiah($prod['price']); ?></td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle">
                            <span
                                class="inline-flex items-center justify-center min-w-[38px] px-2 py-0.5 rounded-full text-[11.5px] font-bold <?php echo $prod['stock'] > 10 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500'; ?>"><?php echo $prod['stock']; ?></span>
                        </td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle">
                            <?php if ($prod['is_active']): ?>
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full border-2 border-teal-400 text-teal-400"><i
                                        class="ph ph-check text-[10px]"></i></span>
                            <?php else: ?>
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full border-2 border-gray-300 text-gray-300"><i
                                        class="ph ph-x text-[10px]"></i></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle text-right">
                            <button type="button"
                                class="p-1.5 text-gray-400 hover:text-navy transition-colors dropdown-trigger"
                                onclick="toggleDropdown(event, this, '<?php echo $prod['id']; ?>')"><i
                                    class="ph ph-dots-three-vertical text-lg"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Cards View -->
    <div id="cards-view" class="hidden p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $prod): ?>
                <div class="product-card bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all hover:-translate-y-1"
                    data-active="<?php echo $prod['is_active'] ? 'yes' : 'no'; ?>"
                    data-name="<?php echo strtolower($prod['name']); ?>" data-sku="<?php echo strtolower($prod['sku']); ?>">
                    <div class="relative mb-4">
                        <img src="../assets/images/product.jpeg" alt="product"
                            class="w-full h-40 object-cover rounded-xl border border-gray-50">
                        <?php if (!$prod['is_active']): ?>
                            <div
                                class="absolute inset-0 bg-white/60 backdrop-blur-[2px] rounded-xl flex items-center justify-center">
                                <span class="bg-gray-800 text-white text-[10px] font-bold px-2 py-1 rounded">INACTIVE</span>
                            </div>
                        <?php endif; ?>
                        
                    </div>

                    <div class="mb-4">
    <div class="flex items-center justify-between gap-2 mb-0.5">
        <div class="text-[14px] font-bold text-navy line-clamp-1">
            <?php echo htmlspecialchars($prod['name']); ?>
        </div>

        <span class="shrink-0 backdrop-blur shadow-sm px-2 py-1 rounded-lg text-[11px] font-bold <?php echo $prod['stock'] > 10 ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'; ?>">
            Stok: <?php echo $prod['stock']; ?>
        </span>
    </div>

    <div class="text-[11px] text-gray-400">
        <?php echo htmlspecialchars($prod['sku']); ?>
    </div>
</div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="text-[15px] font-extrabold text-primary">
                            <?php echo FormatHelper::rupiah($prod['price']); ?></div>
                        <div class="text-[11px] text-gray-400 font-medium">/ kaleng</div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="edit-product.php?id=<?php echo $prod['id']; ?>"
                            class="flex-1 h-9 rounded-lg bg-gray-200 text-slate-600 text-[12px] font-bold flex items-center justify-center hover:bg-primary hover:text-white dark:hover:!bg-red-300 dark:!bg-red-500 dark:!text-white transition-all">Edit</a>
                        <button onclick="confirmDelete(<?php echo $prod['id']; ?>)"
                            class="w-9 h-9 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors"><i
                                class="ph ph-trash"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden py-20 text-center">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-200">
            <i class="ph ph-package text-4xl"></i>
        </div>
        <h3 class="text-[16px] font-bold text-navy mb-1">No products found</h3>
        <p class="text-[13px] text-gray-400">Try adjusting your search or filters</p>
    </div>

    <!-- Footer -->
    <div id="pagination-footer"
        class="px-4 py-3 border-t border-gray-50 flex items-center justify-between text-[12px] text-gray-400 gap-3 flex-wrap">
        <span id="results-count">Showing <?php echo count($products); ?> results</span>
        <div class="flex items-center gap-2">
            <span>Per page</span>
            <div class="relative">
                <select onchange="changePerPage(this.value)"
                    class="border border-gray-200 rounded-md px-2 py-1 text-[12px] outline-none bg-white appearance-none cursor-pointer">
                    <option value="10" <?php echo (int)($_GET['per_page'] ?? 10) === 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo (int)($_GET['per_page'] ?? 10) === 25 ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo (int)($_GET['per_page'] ?? 10) === 50 ? 'selected' : ''; ?>>50</option>
                </select>
                <i
                    class="ph ph-caret-down absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
            </div>
        </div>
    </div>
</div>

<!-- Dropdown Menu Global -->
<div id="dropdown-menu-global"
    class="hidden fixed w-32 bg-white border border-gray-100 rounded-lg shadow-lg z-[9999] text-left">
    <div class="py-1">
        <a id="dropdown-edit-link" href="#"
            class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-500 hover:bg-red-50 transition-colors font-medium"><i
                class="ph ph-note-pencil text-base text-red-400"></i> Edit</a>
        <button id="dropdown-delete-btn" type="button"
            class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-600 hover:bg-red-50 transition-colors w-full text-left font-medium"><i
                class="ph ph-trash text-base text-red-500"></i> Delete</button>
    </div>
</div>

<div class="hidden"><?php echo CsrfService::field(); ?></div>

<script>
    let currentView = 'table';
    let currentFilter = 'all';
    let searchQuery = '';
    let perPage = parseInt(new URLSearchParams(window.location.search).get('per_page') || '10', 10);

    function toggleAll(master) {
        document.querySelectorAll('.cb-row').forEach(cb => cb.checked = master.checked);
    }

    function toggleFilterMenu(e) {
        e.stopPropagation();
        document.getElementById('filter-menu').classList.toggle('hidden');
    }

    function setStatusFilter(filter) {
        currentFilter = filter;
        document.getElementById('filter-menu').classList.add('hidden');

        const btn = document.getElementById('btn-filter');
        if (filter !== 'all') {
            btn.classList.add('bg-primary/5', 'text-primary', 'border-primary/20');
        } else {
            btn.classList.remove('bg-primary/5', 'text-primary', 'border-primary/20');
        }

        applyFilters();
    }

    function toggleLayout() {
        currentView = (currentView === 'table') ? 'grid' : 'table';
        const icon = document.getElementById('layout-icon');
        const tableView = document.getElementById('table-view');
        const cardsView = document.getElementById('cards-view');

        if (currentView === 'grid') {
            icon.classList.remove('ph-squares-four');
            icon.classList.add('ph-list');
            tableView.classList.add('hidden');
            cardsView.classList.remove('hidden');
        } else {
            icon.classList.remove('ph-list');
            icon.classList.add('ph-squares-four');
            tableView.classList.remove('hidden');
            cardsView.classList.add('hidden');
        }

        applyFilters();
    }

    function applyFilters() {
        const query = searchQuery.toLowerCase();
        let matchCount = 0;
        let visibleCount = 0;
        const items = (currentView === 'table') ? document.querySelectorAll('.product-row') : document.querySelectorAll('.product-card');

        document.querySelectorAll('.product-row, .product-card').forEach(el => el.classList.add('hidden'));

        items.forEach(el => {
            let filterMatch = true;
            if (currentFilter === 'active') filterMatch = (el.dataset.active === 'yes');
            if (currentFilter === 'inactive') filterMatch = (el.dataset.active === 'no');

            const searchMatch = (
                el.dataset.name.includes(query) ||
                el.dataset.sku.includes(query)
            );

            if (filterMatch && searchMatch) {
                matchCount++;
                if (visibleCount >= perPage) return;
                el.classList.remove('hidden');
                visibleCount++;
            }
        });

        const emptyState = document.getElementById('empty-state');
        const pagination = document.getElementById('pagination-footer');
        if (matchCount === 0) {
            emptyState.classList.remove('hidden');
            pagination.classList.add('hidden');
            if (currentView === 'table') document.getElementById('table-view').classList.add('hidden');
            else document.getElementById('cards-view').classList.add('hidden');
        } else {
            emptyState.classList.add('hidden');
            pagination.classList.remove('hidden');
            if (currentView === 'table') document.getElementById('table-view').classList.remove('hidden');
            else document.getElementById('cards-view').classList.remove('hidden');
        }

        document.getElementById('results-count').textContent = `Showing ${visibleCount} of ${matchCount} results`;
    }

    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    document.getElementById('product-search').addEventListener('input', function () {
        searchQuery = this.value;
        applyFilters();
    });
    applyFilters();

    window.onclick = function (event) {
        const filterMenu = document.getElementById('filter-menu');
        const dropdownGlobal = document.getElementById('dropdown-menu-global');

        if (!event.target.closest('#btn-filter') && !event.target.closest('#filter-menu')) {
            filterMenu.classList.add('hidden');
        }

        if (!event.target.closest('#dropdown-menu-global') && !event.target.closest('.dropdown-trigger')) {
            dropdownGlobal.classList.add('hidden');
        }
    }

    function toggleDropdown(event, btn, id) {
        event.stopPropagation();
        const menu = document.getElementById('dropdown-menu-global');
        const editLink = document.getElementById('dropdown-edit-link');
        const deleteBtn = document.getElementById('dropdown-delete-btn');

        if (!menu.classList.contains('hidden') && menu.dataset.activeId === id) {
            menu.classList.add('hidden');
            return;
        }

        editLink.href = 'edit-product.php?id=' + id;
        deleteBtn.onclick = function () { confirmDelete(id); };

        menu.classList.remove('hidden');
        const rect = btn.getBoundingClientRect();
        menu.style.top = (rect.bottom + 8) + 'px';
        menu.style.left = (rect.right - menu.offsetWidth) + 'px';
        menu.dataset.activeId = id;
    }

    function confirmDelete(id) {
        showConfirm('Apakah Anda yakin ingin menghapus produk ini?').then(function(confirmed) {
            if (!confirmed) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'products.php?action=delete&id=' + id;
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = document.querySelector('input[name="csrf_token"]')?.value || '';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        });
    }
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
