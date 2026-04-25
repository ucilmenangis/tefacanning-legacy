<?php
$pageTitle = "Log Aktivitas";
$currentPage = "activity-log";
require_once __DIR__ . "/../includes/auth.php";
requireAdmin();
requireSuperAdmin();
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

<style>
    .breadcrumb-item { font-size: 11px; color: #94a3b8; }
    .breadcrumb-item.active { color: #1e293b; font-weight: 500; }

    .table-container { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; overflow: hidden; }
    .table-toolbar { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 12px 20px; border-bottom: 1px solid #f8fafc; }

    .search-input { border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px 6px 32px; font-size: 12px; color: #374151; background: #f9fafb; outline: none; width: 180px; }
    .search-input:focus { border-color: #E02424; background: #fff; }

    .data-table { width: 100%; text-align: left; font-size: 13px; border-collapse: collapse; }
    .data-table th { font-size: 11px; font-weight: 700; color: #1e293b; padding: 12px 20px; border-bottom: 1px solid #f1f5f9; text-transform: none; }
    .data-table td { padding: 14px 20px; border-bottom: 1px solid #f8fafc; color: #334155; vertical-align: middle; }
    .data-table tr:hover td { background: #fafafa; }

    .badge-action { display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 6px; font-size: 10px; font-weight: 600; border: 1px solid; }
    .badge-target { display: flex; flex-direction: column; line-height: 1.2; }
    .badge-target .type { font-size: 9px; color: #94a3b8; font-weight: 500; }
    .badge-target .id { font-size: 11px; color: #334155; font-weight: 600; }

    .pagination-btn { width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 12px; color: #64748b; border: 1px solid #e2e8f0; transition: all 0.2s; }
    .pagination-btn.active { background: #fef2f2; border-color: #fecaca; color: #E02424; font-weight: 600; }
    .pagination-btn:hover:not(.active) { background: #f8fafc; }
</style>

<!-- Header & Breadcrumb -->
<div class="mb-6">
    <div class="flex items-center gap-2 mb-1">
        <span class="breadcrumb-item">Log Aktivitas</span>
        <i class="ph ph-caret-right text-[10px] text-slate-400"></i>
        <span class="breadcrumb-item active">List</span>
    </div>
    <h1 class="text-2xl font-extrabold text-slate-800">Log Aktivitas</h1>
</div>

<!-- Table Section -->
<div class="table-container shadow-sm">
    <div class="table-toolbar">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" placeholder="Search" class="search-input" id="log-search-input">
        </div>
        <button class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
            <i class="ph ph-funnel text-base"></i>
        </button>
        <button class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
            <i class="ph ph-squares-four text-base"></i>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Waktu <i class="ph ph-caret-down text-[10px] ml-1"></i></th>
                    <th>Aktor <i class="ph ph-caret-down text-[10px] ml-1"></i></th>
                    <th>Aksi</th>
                    <th>Target</th>
                    <th>Deskripsi</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td>
                        <div class="font-semibold text-slate-700"><?php echo $log[
                          "waktu"
                        ]; ?></div>
                        <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $log[
                          "ago"
                        ]; ?></div>
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <i class="ph ph-user text-slate-300 text-lg"></i>
                            <span class="font-medium"><?php echo $log[
                              "aktor"
                            ]; ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="badge-action <?php echo getActionClass(
                          $log["aksi"],
                        ); ?>">
                            <?php echo $log["aksi"]; ?>
                        </span>
                    </td>
                    <td>
                        <div class="badge-target border border-slate-100 bg-slate-50 px-2 py-1 rounded-md w-fit min-w-[70px]">
                            <span class="type uppercase tracking-wider"><?php echo $log[
                              "target_type"
                            ]; ?></span>
                            <span class="id">ID: <?php echo $log[
                              "target_id"
                            ]; ?></span>
                        </div>
                    </td>
                    <td class="text-slate-500 italic"><?php echo $log[
                      "deskripsi"
                    ]; ?></td>
                    <td class="text-right">
                        <button class="text-[12px] font-bold text-slate-600 hover:text-navy flex items-center gap-1.5 ml-auto group">
                            <i class="ph ph-eye text-base text-slate-400 group-hover:text-navy"></i>
                            View
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="px-6 py-4 border-t border-slate-50 flex items-center justify-between">
        <div class="text-[12px] text-slate-500">
            Showing <?php echo $offset + 1; ?> to <?php echo min(
   $offset + $perPage,
   $totalLogs,
 ); ?> of <?php echo $totalLogs; ?> results
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 mr-6">
                <span class="text-[12px] text-slate-400">Per page</span>
                <select class="text-[12px] border border-slate-200 rounded px-2 py-1 outline-none bg-white">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>
            <div class="flex items-center gap-1">
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">
                    <i class="ph ph-caret-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../includes/footer-admin.php"; ?>
