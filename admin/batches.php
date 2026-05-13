<?php
$pageTitle   = 'Batches';
$currentPage = 'batches';
require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();

require_once __DIR__ . '/../classes/BatchService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$batchService = new BatchService();
$activityLogService = new ActivityLogService();

// ── POST: Delete ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'delete') {
    $deleteId = intval($_GET['id'] ?? 0);
    if ($deleteId && CsrfService::verify()) {
        $batchService->softDelete($deleteId);
        $activityLogService->log('deleted', 'App\Models\Batch', $deleteId, 'deleted');
        FlashMessage::set('success', 'Batch berhasil dihapus.');
    }
    header('Location: batches.php');
    exit;
}

include __DIR__ . '/../includes/header-admin.php';

$batches = $batchService->getAll();

$statusMap = [
    'open'       => ['label' => 'Open',       'bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'],
    'processing' => ['label' => 'Processing', 'bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#dbeafe'],
    'ready'      => ['label' => 'Ready',      'bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'],
    'closed'     => ['label' => 'Closed',     'bg' => '#f9fafb', 'color' => '#6b7280', 'border' => '#e5e7eb'],
];
?>



<!-- Page Header -->
<div class="text-[12px] text-gray-400 mb-1">Batches &rsaquo; <span class="text-gray-700">List</span></div>
<div class="flex items-center justify-between mb-5">
    <h1 class="text-[22px] font-extrabold text-navy">Batches</h1>
    <a href="create-batch.php" class="inline-flex items-center gap-1.5 bg-primary text-white text-[13px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark" id="btn-new-batch">
        <i class="ph-bold ph-plus text-sm"></i> New batch
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
                id="batch-search">
        </div>
        <div class="flex items-center justify-end gap-2">

        <!-- Filter Dropdown -->
        <div class="relative">
            <button
                class="w-[36px] h-[36px] rounded-lg border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-all hover:bg-gray-50 hover:text-navy active:scale-95"
                title="Filter" id="btn-filter" onclick="toggleFilterMenu(event)">
                <i class="ph ph-funnel text-[18px]"></i>
            </button>
            <div id="filter-menu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-xl z-[100] py-1.5">
                <div class="px-3 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Filter Status</div>
                <button onclick="setStatusFilter('all')" class="w-full text-left px-4 py-2 text-[12.5px] text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    All Batches
                </button>
                <?php foreach($statusMap as $val => $cfg): ?>
                <button onclick="setStatusFilter('<?php echo $val; ?>')" class="w-full text-left px-4 py-2 text-[12.5px] text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" style="background: <?php echo $cfg['color']; ?>"></span>
                    <?php echo $cfg['label']; ?>
                </button>
                <?php endforeach; ?>
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
                    <th class="w-9 text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">
                        <input type="checkbox" class="w-[15px] h-[15px] accent-primary cursor-pointer" id="cb-all" onchange="toggleAll(this)">
                    </th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">Nama Batch</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">Event</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">Tanggal</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">Status</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50">Pesanan</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 bg-gray-50/50 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($batches as $batch): 
                    $st = $statusMap[$batch['status']] ?? ['label'=>$batch['status'],'bg'=>'#f9fafb','color'=>'#6b7280','border'=>'#e5e7eb'];
                ?>
                    <tr class="batch-row transition-colors hover:bg-gray-50/50" data-status="<?php echo $batch['status']; ?>" data-name="<?php echo strtolower($batch['name']); ?>" data-event="<?php echo strtolower($batch['event_name']); ?>">
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle"><input type="checkbox" class="w-[15px] h-[15px] accent-primary cursor-pointer cb-row"></td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle font-bold text-navy"><?php echo htmlspecialchars($batch['name']); ?></td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle">
                            <div class="flex items-center gap-1.5 text-gray-500">
                                <i class="ph ph-calendar-blank text-gray-300"></i> <?php echo htmlspecialchars($batch['event_name']); ?>
                            </div>
                        </td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle font-semibold text-primary"><?php echo FormatHelper::tanggal($batch['event_date']); ?></td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border" style="background:<?php echo $st['bg'];?>;color:<?php echo $st['color'];?>;border-color:<?php echo $st['border'];?>">
                                <?php echo $st['label']; ?>
                            </span>
                        </td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle">
                            <span class="text-[11px] bg-blue-50 text-blue-600 border border-blue-100 px-2 py-0.5 rounded font-semibold"><?php echo $batch['order_count']; ?></span>
                        </td>
                        <td class="px-3.5 py-3 border-b border-gray-50/50 align-middle text-right">
                            <button type="button" class="p-1.5 text-gray-400 hover:text-navy transition-colors dropdown-trigger" onclick="toggleDropdown(event, this, '<?php echo $batch['id']; ?>')"><i class="ph ph-dots-three-vertical text-lg"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Cards View -->
    <div id="cards-view" class="hidden p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($batches as $batch): 
                $st = $statusMap[$batch['status']] ?? ['label'=>$batch['status'],'bg'=>'#f9fafb','color'=>'#6b7280','border'=>'#e5e7eb'];
            ?>
                <div class="batch-card bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all hover:-translate-y-1" data-status="<?php echo $batch['status']; ?>" data-name="<?php echo strtolower($batch['name']); ?>" data-event="<?php echo strtolower($batch['event_name']); ?>">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider" style="background:<?php echo $st['bg'];?>;color:<?php echo $st['color'];?>;border-color:<?php echo $st['border'];?>">
                            <?php echo $st['label']; ?>
                        </span>
                        <div class="text-[12px] font-bold text-primary"><?php echo FormatHelper::tanggal($batch['event_date']); ?></div>
                    </div>

                    <div class="mb-5">
                        <div class="text-[16px] font-bold text-navy mb-1"><?php echo htmlspecialchars($batch['name']); ?></div>
                        <div class="flex items-center gap-2 text-[12px] text-gray-400">
                            <i class="ph ph-calendar-blank"></i> <?php echo htmlspecialchars($batch['event_name']); ?>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-5">
                        <div class="text-center flex-1 border-r border-gray-200">
                            <div class="text-[10px] text-gray-400 font-bold uppercase mb-0.5">Total Orders</div>
                            <div class="text-[16px] font-extrabold text-navy"><?php echo $batch['order_count']; ?></div>
                        </div>
                        <div class="text-center flex-1">
                            <div class="text-[10px] text-gray-400 font-bold uppercase mb-0.5">Status</div>
                            <div class="text-[12px] font-bold text-slate-600"><?php echo $st['label']; ?></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-4 border-t border-gray-50">
                        <a href="edit-batch.php?id=<?php echo $batch['id']; ?>" class="flex-1 h-9 rounded-lg bg-gray-50 text-slate-600 text-[12px] font-bold flex items-center justify-center hover:bg-primary hover:text-white transition-all"><i class="ph ph-note-pencil mr-1.5"></i> Edit</a>
                        <button onclick="confirmDelete(<?php echo $batch['id']; ?>)" class="w-9 h-9 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors"><i class="ph ph-trash"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden py-20 text-center">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-200">
            <i class="ph ph-factory text-4xl"></i>
        </div>
        <h3 class="text-[16px] font-bold text-navy mb-1">No batches found</h3>
        <p class="text-[13px] text-gray-400">Try adjusting your search or filters</p>
    </div>

    <!-- Footer -->
    <div id="pagination-footer" class="px-4 py-3 border-t border-gray-50 flex items-center justify-between text-[12px] text-gray-400 gap-3 flex-wrap">
        <span id="results-count">Showing <?php echo count($batches); ?> results</span>
        <div class="flex items-center gap-2">
            <span>Per page</span>
            <div class="relative">
                <select onchange="changePerPage(this.value)" class="border border-gray-200 rounded-md px-2 py-1 text-[12px] outline-none bg-white appearance-none cursor-pointer">
                    <option value="10" <?php echo (int)($_GET['per_page'] ?? 10) === 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo (int)($_GET['per_page'] ?? 10) === 25 ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo (int)($_GET['per_page'] ?? 10) === 50 ? 'selected' : ''; ?>>50</option>
                </select>
                <i class="ph ph-caret-down absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
            </div>
        </div>
    </div>
</div>

<!-- Dropdown Menu Global -->
<div id="dropdown-menu-global" class="hidden fixed w-32 bg-white border border-gray-100 rounded-lg shadow-lg z-[9999] text-left">
    <div class="py-1">
        <a id="dropdown-edit-link" href="#" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-500 hover:bg-red-50 transition-colors font-medium"><i class="ph ph-note-pencil text-base text-red-400"></i> Edit</a>
        <button id="dropdown-delete-btn" type="button" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-600 hover:bg-red-50 transition-colors w-full text-left font-medium"><i class="ph ph-trash text-base text-red-500"></i> Delete</button>
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
        const items = (currentView === 'table') ? document.querySelectorAll('.batch-row') : document.querySelectorAll('.batch-card');
        
        document.querySelectorAll('.batch-row, .batch-card').forEach(el => el.classList.add('hidden'));

        items.forEach(el => {
            let filterMatch = true;
            if (currentFilter !== 'all') filterMatch = (el.dataset.status === currentFilter);

            const searchMatch = (
                el.dataset.name.includes(query) || 
                el.dataset.event.includes(query)
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

    document.getElementById('batch-search').addEventListener('input', function() {
        searchQuery = this.value;
        applyFilters();
    });
    applyFilters();

    window.onclick = function(event) {
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

        editLink.href = 'edit-batch.php?id=' + id;
        deleteBtn.onclick = function() { confirmDelete(id); };

        menu.classList.remove('hidden');
        const rect = btn.getBoundingClientRect();
        menu.style.top = (rect.bottom + 8) + 'px';
        menu.style.left = (rect.right - menu.offsetWidth) + 'px';
        menu.dataset.activeId = id;
    }

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus batch ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'batches.php?action=delete&id=' + id;
            const csrf = document.createElement('input');
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
