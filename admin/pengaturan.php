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



<!-- Header & Breadcrumb -->
<div class="mb-6 flex items-end justify-between">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="text-[11px] text-slate-400">Pengguna</span>
            <i class="ph ph-caret-right text-[10px] text-slate-400"></i>
            <span class="text-[11px] text-slate-700 font-medium">List</span>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-800">Pengguna</h1>
    </div>
    <a href="create-user.php" class="inline-flex items-center gap-2 bg-primary text-white text-[12px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark">
        New Pengguna
    </a>
</div>

<!-- Table Section -->
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
    <div class="flex items-center justify-end gap-2.5 px-5 py-3 border-b border-gray-50">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" placeholder="Search" class="border border-gray-200 rounded-lg py-1.5 pl-8 pr-3 text-[12px] outline-none bg-gray-50 w-[180px] transition-colors focus:border-primary focus:bg-white" id="user-search-input">
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
        <table class="w-full text-left text-[13px] border-collapse">
            <thead>
                <tr>
                    <th class="w-10 text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                        <input type="checkbox" class="w-4 h-4 accent-primary cursor-pointer">
                    </th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100 bg-gray-50/50">Nama <i class="ph ph-caret-down text-[10px] ml-1"></i></th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100 bg-gray-50/50">Email <i class="ph ph-caret-down text-[10px] ml-1"></i></th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100 bg-gray-50/50">WhatsApp</th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100 bg-gray-50/50">Role</th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100 bg-gray-50/50">Terdaftar <i class="ph ph-caret-down text-[10px] ml-1"></i></th>
                    <th class="text-[11px] font-bold text-navy px-5 py-3 border-b border-gray-100 bg-gray-50/50"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <input type="checkbox" class="w-4 h-4 accent-primary cursor-pointer">
                    </td>
                    <td class="font-bold text-slate-700 px-5 py-3.5 border-b border-gray-50 align-middle"><?php echo $user['name']; ?></td>
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <div class="flex items-center gap-2 text-slate-500">
                            <i class="ph ph-envelope text-slate-300 text-base"></i>
                            <span><?php echo $user['email']; ?></span>
                        </div>
                    </td>
                    <td class="text-slate-400 px-5 py-3.5 border-b border-gray-50 align-middle">—</td>
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <span class="inline-flex items-center px-3 py-0.5 rounded-md text-[10px] font-semibold border <?php echo getRoleClass($user['role']); ?>">
                            <?php echo $user['role']; ?>
                        </span>
                    </td>
                    <td class="text-slate-500 px-5 py-3.5 border-b border-gray-50 align-middle"><?php echo $user['created_at']; ?></td>
                    <td class="text-right px-5 py-3.5 border-b border-gray-50 align-middle">
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
    <div class="px-5 py-3 border-t border-gray-50 flex items-center justify-between text-[12px] text-slate-500">
        <div>Showing 1 to <?php echo count($users); ?> of <?php echo count($users); ?> results</div>
        <div class="flex items-center gap-2">
            <span>Per page</span>
            <select class="border border-slate-200 rounded px-2 py-1 outline-none bg-white text-[12px]">
                <option>10</option>
                <option>25</option>
                <option>50</option>
            </select>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
