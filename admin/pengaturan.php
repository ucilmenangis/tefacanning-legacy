<?php
$pageTitle   = 'Pengguna';
$currentPage = 'users';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
requireSuperAdmin();
include __DIR__ . '/../includes/header-admin.php';

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$adminService = new AdminService();
$users_raw = $adminService->getAll();

$users = [];
foreach ($users_raw as $u) {
    $roleLabel = $u['role'] === 'super_admin' ? 'Super Admin' : ucfirst($u['role'] ?? 'User');
    $users[] = [
        'id' => $u['id'],
        'name' => $u['name'],
        'email' => $u['email'],
        'role' => $roleLabel,
        'created_at' => FormatHelper::tanggal($u['created_at'] ?? 'now'),
    ];
}

function getRoleClass($role) {
    if ($role === 'Super Admin') {
        return 'bg-rose-50 text-rose-500 border-rose-100';
    }
    return 'bg-sky-50 text-sky-500 border-sky-100';
}
?>

<style>
    .breadcrumb-item { font-size: 11px; color: #94a3b8; }
    .breadcrumb-item.active { color: #1e293b; font-weight: 500; }
    
    .btn-new { display: inline-flex; align-items: center; gap: 8px; background: #E02424; color: #fff; font-size: 12px; font-weight: 700; padding: 8px 16px; border-radius: 8px; transition: background 0.2s; }
    .btn-new:hover { background: #9B1C1C; }

    .table-container { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; overflow: hidden; }
    .table-toolbar { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 12px 20px; border-bottom: 1px solid #f8fafc; }
    
    .search-input { border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px 6px 32px; font-size: 12px; color: #374151; background: #f9fafb; outline: none; width: 180px; }
    .search-input:focus { border-color: #E02424; background: #fff; }

    .data-table { width: 100%; text-align: left; font-size: 13px; border-collapse: collapse; }
    .data-table th { font-size: 11px; font-weight: 700; color: #1e293b; padding: 12px 20px; border-bottom: 1px solid #f1f5f9; background: #fafafa; }
    .data-table td { padding: 14px 20px; border-bottom: 1px solid #f8fafc; color: #334155; vertical-align: middle; }
    .data-table tr:hover td { background: #fafafa; }

    .checkbox-custom { width: 16px; height: 16px; accent-color: #E02424; cursor: pointer; }
    
    .role-badge { display: inline-flex; align-items: center; padding: 2px 12px; border-radius: 6px; font-size: 10px; font-weight: 600; border: 1px solid; }
    
    .table-footer { padding: 12px 20px; border-top: 1px solid #f8fafc; display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: #64748b; }
</style>

<!-- Header & Breadcrumb -->
<div class="mb-6 flex items-end justify-between">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="breadcrumb-item">Pengguna</span>
            <i class="ph ph-caret-right text-[10px] text-slate-400"></i>
            <span class="breadcrumb-item active">List</span>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-800">Pengguna</h1>
    </div>
    <a href="create-user.php" class="btn-new">
        New Pengguna
    </a>
</div>

<!-- Table Section -->
<div class="table-container shadow-sm">
    <div class="table-toolbar">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" placeholder="Search" class="search-input" id="user-search-input">
        </div>
        <button class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors relative">
            <i class="ph ph-funnel text-base"></i>
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 text-white text-[8px] font-bold flex items-center justify-center rounded-full border border-white">0</span>
        </button>
        <button class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
            <i class="ph ph-squares-four text-base"></i>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-10">
                        <input type="checkbox" class="checkbox-custom">
                    </th>
                    <th>Nama <i class="ph ph-caret-down text-[10px] ml-1"></i></th>
                    <th>Email <i class="ph ph-caret-down text-[10px] ml-1"></i></th>
                    <th>WhatsApp</th>
                    <th>Role</th>
                    <th>Terdaftar <i class="ph ph-caret-down text-[10px] ml-1"></i></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <input type="checkbox" class="checkbox-custom">
                    </td>
                    <td class="font-bold text-slate-700"><?php echo $user['name']; ?></td>
                    <td>
                        <div class="flex items-center gap-2 text-slate-500">
                            <i class="ph ph-envelope text-slate-300 text-base"></i>
                            <span><?php echo $user['email']; ?></span>
                        </div>
                    </td>
                    <td class="text-slate-400">—</td>
                    <td>
                        <span class="role-badge <?php echo getRoleClass($user['role']); ?>">
                            <?php echo $user['role']; ?>
                        </span>
                    </td>
                    <td class="text-slate-500"><?php echo $user['created_at']; ?></td>
                    <td class="text-right">
                        <button class="text-slate-300 hover:text-slate-600 transition-colors">
                            <i class="ph ph-dots-three-vertical text-xl"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="table-footer">
        <div>Showing 1 to <?php echo count($users); ?> of <?php echo count($users); ?> results</div>
        <div class="flex items-center gap-2">
            <span>Per page</span>
            <select class="border border-slate-200 rounded px-2 py-1 outline-none bg-white">
                <option>10</option>
                <option>25</option>
                <option>50</option>
            </select>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
