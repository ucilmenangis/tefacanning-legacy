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

<!-- Table Section -->
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
    <div class="flex items-center justify-end gap-2.5 px-5 py-3 border-b border-gray-50">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" placeholder="Search" class="border border-gray-200 rounded-lg py-1.5 pl-8 pr-3 text-[12px] outline-none bg-gray-50 w-[180px] transition-colors focus:border-primary focus:bg-white" id="log-search-input">
        </div>
        <button class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
            <i class="ph ph-funnel text-base"></i>
        </button>
        <button class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
            <i class="ph ph-squares-four text-base"></i>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-[13px] border-collapse">
            <thead>
                <tr>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100">Waktu <i class="ph ph-caret-down text-[10px] ml-1"></i></th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100">Aktor <i class="ph ph-caret-down text-[10px] ml-1"></i></th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100">Aksi</th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100">Target</th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <div class="font-semibold text-slate-700"><?php echo $log['waktu']; ?></div>
                        <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $log['ago']; ?></div>
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
                <button class="w-7 h-7 inline-flex items-center justify-center rounded-md text-[12px] text-primary font-semibold border border-red-200 bg-red-50 hover:bg-red-100 transition-colors">1</button>
                <button class="w-7 h-7 inline-flex items-center justify-center rounded-md text-[12px] text-slate-500 border border-gray-200 hover:bg-gray-50 transition-colors">2</button>
                <button class="w-7 h-7 inline-flex items-center justify-center rounded-md text-[12px] text-slate-500 border border-gray-200 hover:bg-gray-50 transition-colors">
                    <i class="ph ph-caret-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../includes/footer-admin.php"; ?>
