<?php
$pageTitle = "Log Aktivitas";
$currentPage = "activity-log";
require_once __DIR__ . "/../includes/auth.php";
Auth::admin()->requireAuth();
Auth::admin()->requireSuperAdmin();
include __DIR__ . "/../includes/header-admin.php";
require_once __DIR__ . "/../includes/functions.php";

require_once __DIR__ . "/../classes/ActivityLogService.php";

$logService = new ActivityLogService();

$page = max(1, intval($_GET["page"] ?? 1));
$perPage = max(1, intval($_GET["per_page"] ?? 10));
$offset = ($page - 1) * $perPage;

$filters = [];
if (!empty($_GET["event"])) {
  $filters["event"] = $_GET["event"];
}
if (!empty($_GET["subject_type"])) {
  $filters["subject_type"] = $_GET["subject_type"];
}

$logs_raw = $logService->getAll($perPage, $offset, $filters);
$totalLogs = $logService->countAll($filters);
$totalPages = max(1, ceil($totalLogs / $perPage));

// Map raw DB rows to template format
$logs = [];
foreach ($logs_raw as $row) {
  $subjectShort = basename(
    str_replace("App\\Models\\", "", $row["subject_type"] ?? ""),
  );
  $eventLabel = match ($row["description"] ?? "") {
    "created" => "Dibuat",
    "updated" => "Diubah",
    "deleted" => "Dihapus",
    default => ucfirst($row["description"] ?? "-"),
  };

  $logs[] = [
    "waktu" => date("d M Y, H:i:s", strtotime($row["created_at"])),
    "ago" => "",
    "aktor" => $row["causer_name"] ?? "Sistem",
    "aksi" => $eventLabel,
    "target_type" => $subjectShort,
    "target_id" => $row["subject_id"] ?? "-",
    "deskripsi" => $row["description"] ?? "-",
  ];
}

function getActionClass($action)
{
  switch ($action) {
    case "Dibuat":
      return "bg-emerald-50 text-emerald-600 border-emerald-100";
    case "Diubah":
      return "bg-sky-50 text-sky-600 border-sky-100";
    case "Dihapus":
      return "bg-rose-50 text-rose-600 border-rose-100";
    default:
      return "bg-gray-50 text-gray-600 border-gray-100";
  }
}
?>

<!-- Header & Breadcrumb -->
<div class="mb-6">
    <div class="flex items-center gap-2 mb-1">
        <span class="text-[11px] text-slate-400">Log Aktivitas</span>
        <i class="ph ph-caret-right text-[10px] text-slate-400"></i>
        <span class="text-[11px] text-slate-700 font-medium">List</span>
    </div>
    <h1 class="text-2xl font-extrabold text-slate-800">Log Aktivitas</h1>
</div>

<div class="bg-white border border-gray-100 rounded-xl overflow-visible">
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 px-4 py-3 border-b border-gray-50">
        <!-- Search -->
        <div class="relative group flex-1 w-full sm:max-w-[240px]">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[14px]"></i>
            <input type="text" placeholder="Search logs..."
                class="border border-gray-200 rounded-lg py-2 pl-[34px] pr-3 text-[13px] outline-none bg-white w-full transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                id="log-search">
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
                <div class="px-3 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Filter Aksi</div>
                <button onclick="setStatusFilter('all')" class="w-full text-left px-4 py-2 text-[12.5px] text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    Semua Aksi
                </button>
                <button onclick="setStatusFilter('Dibuat')" class="w-full text-left px-4 py-2 text-[12.5px] text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Dibuat
                </button>
                <button onclick="setStatusFilter('Diubah')" class="w-full text-left px-4 py-2 text-[12.5px] text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span> Diubah
                </button>
                <button onclick="setStatusFilter('Dihapus')" class="w-full text-left px-4 py-2 text-[12.5px] text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-400"></span> Dihapus
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
        <table class="w-full text-left text-[13px] border-collapse">
            <thead>
                <tr>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100">Waktu</th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100">Aktor</th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100">Aksi</th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100">Target</th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr class="log-row transition-colors hover:bg-gray-50/50" data-event="<?php echo $log['aksi']; ?>" data-aktor="<?php echo strtolower($log['aktor']); ?>" data-target="<?php echo strtolower($log['target_type']); ?>" data-desc="<?php echo strtolower($log['deskripsi']); ?>">
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <div class="font-semibold text-slate-700"><?php echo $log['waktu']; ?></div>
                    </td>
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <div class="flex items-center gap-2">
                            <i class="ph ph-user text-slate-300 text-lg"></i>
                            <span class="font-medium"><?php echo $log['aktor']; ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-semibold border <?php echo getActionClass($log['aksi']); ?>">
                            <?php echo $log['aksi']; ?>
                        </span>
                    </td>
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <div class="flex flex-col leading-snug border border-slate-100 bg-slate-50 px-2 py-1 rounded-md w-fit min-w-[70px]">
                            <span class="text-[9px] text-slate-400 font-medium uppercase tracking-wider"><?php echo $log['target_type']; ?></span>
                            <span class="text-[11px] text-slate-700 font-semibold">ID: <?php echo $log['target_id']; ?></span>
                        </div>
                    </td>
                    <td class="text-slate-500 italic px-5 py-3.5 border-b border-gray-50 align-middle"><?php echo $log['deskripsi']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Cards View -->
    <div id="cards-view" class="hidden p-6 bg-gray-50/30">
        <div class="space-y-4 max-w-3xl mx-auto relative before:absolute before:left-[17px] before:top-2 before:bottom-2 before:w-px before:bg-gray-200">
            <?php foreach ($logs as $log): ?>
                <div class="log-card relative pl-10 transition-all" data-event="<?php echo $log['aksi']; ?>" data-aktor="<?php echo strtolower($log['aktor']); ?>" data-target="<?php echo strtolower($log['target_type']); ?>" data-desc="<?php echo strtolower($log['deskripsi']); ?>">
                    <!-- Timeline Dot -->
                    <div class="absolute left-0 top-1.5 w-[35px] h-[35px] rounded-full bg-white border-2 flex items-center justify-center z-10 <?php 
                        echo match($log['aksi']) {
                            'Dibuat' => 'border-emerald-400 text-emerald-500',
                            'Diubah' => 'border-sky-400 text-sky-500',
                            'Dihapus' => 'border-rose-400 text-rose-500',
                            default => 'border-gray-300 text-gray-400'
                        };
                    ?>">
                        <i class="ph-bold <?php 
                            echo match($log['aksi']) {
                                'Dibuat' => 'ph-plus',
                                'Diubah' => 'ph-pencil-simple',
                                'Dihapus' => 'ph-trash',
                                default => 'ph-info'
                            };
                        ?> text-sm"></i>
                    </div>

                    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-navy text-[13px]"><?php echo $log['aktor']; ?></span>
                                <span class="text-gray-400 text-[12px]"><?php echo $log['aksi']; ?></span>
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 text-[9px] font-bold text-gray-500 uppercase tracking-tighter"><?php echo $log['target_type']; ?> #<?php echo $log['target_id']; ?></span>
                            </div>
                            <span class="text-[11px] text-gray-400"><?php echo $log['waktu']; ?></span>
                        </div>
                        <div class="text-[12.5px] text-gray-600 italic">"<?php echo $log['deskripsi']; ?>"</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden py-20 text-center">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-200">
            <i class="ph ph-clock-counter-clockwise text-4xl"></i>
        </div>
        <h3 class="text-[16px] font-bold text-navy mb-1">No logs found</h3>
        <p class="text-[13px] text-gray-400">Try adjusting your search or filters</p>
    </div>

    <!-- Footer -->
    <div id="pagination-footer" class="px-6 py-4 border-t border-slate-50 flex items-center justify-between">
        <div class="text-[12px] text-slate-500" id="results-count">
            Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $perPage, $totalLogs); ?> of <?php echo $totalLogs; ?> results
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 mr-6">
                <span class="text-[12px] text-slate-400">Per page</span>
                <select class="text-[12px] border border-slate-200 rounded px-2 py-1 outline-none bg-white appearance-none cursor-pointer pr-6 relative">
                    <option value="10" <?php echo $perPage == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo $perPage == 25 ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo $perPage == 50 ? 'selected' : ''; ?>>50</option>
                </select>
            </div>
            <!-- Pagination Controls -->
            <div class="flex items-center gap-1">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&per_page=<?php echo $perPage; ?>" class="w-7 h-7 inline-flex items-center justify-center rounded-md text-[12px] text-slate-500 border border-gray-200 hover:bg-gray-50 transition-colors"><i class="ph ph-caret-left"></i></a>
                <?php endif; ?>
                
                <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&per_page=<?php echo $perPage; ?>" class="w-7 h-7 inline-flex items-center justify-center rounded-md text-[12px] <?php echo $i === $page ? 'text-primary font-semibold border border-red-200 bg-red-50' : 'text-slate-500 border border-gray-200 hover:bg-gray-50'; ?> transition-colors"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&per_page=<?php echo $perPage; ?>" class="w-7 h-7 inline-flex items-center justify-center rounded-md text-[12px] text-slate-500 border border-gray-200 hover:bg-gray-50 transition-colors"><i class="ph ph-caret-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    let currentView = 'table';
    let currentFilter = 'all';
    let searchQuery = '';

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
        let visibleCount = 0;
        const items = (currentView === 'table') ? document.querySelectorAll('.log-row') : document.querySelectorAll('.log-card');
        
        document.querySelectorAll('.log-row, .log-card').forEach(el => el.classList.add('hidden'));

        items.forEach(el => {
            let filterMatch = true;
            if (currentFilter !== 'all') filterMatch = (el.dataset.event === currentFilter);

            const searchMatch = (
                el.dataset.aktor.includes(query) || 
                el.dataset.target.includes(query) ||
                el.dataset.desc.includes(query)
            );

            if (filterMatch && searchMatch) {
                el.classList.remove('hidden');
                visibleCount++;
            }
        });

        const emptyState = document.getElementById('empty-state');
        const pagination = document.getElementById('pagination-footer');
        if (visibleCount === 0) {
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
    }

    document.getElementById('log-search').addEventListener('input', function() {
        searchQuery = this.value;
        applyFilters();
    });

    window.onclick = function(event) {
        const filterMenu = document.getElementById('filter-menu');
        if (!event.target.closest('#btn-filter') && !event.target.closest('#filter-menu')) {
            filterMenu.classList.add('hidden');
        }
    }
</script>

<?php include __DIR__ . "/../includes/footer-admin.php"; ?>
