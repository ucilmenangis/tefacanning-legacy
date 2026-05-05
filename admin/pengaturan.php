<?php
$pageTitle   = 'Pengguna';
$currentPage = 'users';
require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();
Auth::admin()->requireSuperAdmin();

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';
require_once __DIR__ . '/../classes/CsrfService.php';

$adminService = new AdminService();
$users_raw = $adminService->getAll();

function getRoleClass($role) {
    if ($role === 'super_admin') {
        return 'bg-rose-50 text-rose-500 border-rose-100';
    }
    return 'bg-sky-50 text-sky-500 border-sky-100';
}

include __DIR__ . '/../includes/header-admin.php';
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
    <a href="create-user.php" class="inline-flex items-center gap-2 bg-primary text-white text-[12px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark shadow-lg shadow-primary/20">
        <i class="ph-bold ph-plus text-sm"></i> New Pengguna
    </a>
</div>

<!-- Table Section -->
<div class="bg-white border border-gray-100 rounded-xl overflow-visible shadow-sm">
    <div class="flex items-center justify-end gap-2.5 px-5 py-3 border-b border-gray-50">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" placeholder="Search" class="border border-gray-200 rounded-lg py-1.5 pl-8 pr-3 text-[12px] outline-none bg-gray-50 w-[180px] transition-colors focus:border-primary focus:bg-white" id="user-search-input">
        </div>
        <button class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors relative">
            <i class="ph ph-funnel text-base"></i>
        </button>
        <button class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
            <i class="ph ph-squares-four text-base"></i>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-[13px] border-collapse" id="user-table">
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
                <?php foreach ($users_raw as $u): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <input type="checkbox" class="w-4 h-4 accent-primary cursor-pointer">
                    </td>
                    <td class="font-bold text-slate-700 px-5 py-3.5 border-b border-gray-50 align-middle"><?php echo htmlspecialchars($u['name']); ?></td>
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <div class="flex items-center gap-2 text-slate-500">
                            <i class="ph ph-envelope text-slate-300 text-base"></i>
                            <span><?php echo htmlspecialchars($u['email']); ?></span>
                        </div>
                    </td>
                    <td class="text-slate-500 px-5 py-3.5 border-b border-gray-50 align-middle">
                        <?php echo $u['phone'] ? htmlspecialchars($u['phone']) : '<span class="text-slate-300">—</span>'; ?>
                    </td>
                    <td class="px-5 py-3.5 border-b border-gray-50 align-middle">
                        <span class="inline-flex items-center px-3 py-0.5 rounded-md text-[10px] font-semibold border <?php echo getRoleClass($u['role']); ?>">
                            <?php echo $u['role'] === 'super_admin' ? 'Super Admin' : ucfirst($u['role'] ?? 'User'); ?>
                        </span>
                    </td>
                    <td class="text-slate-500 px-5 py-3.5 border-b border-gray-50 align-middle"><?php echo FormatHelper::tanggal($u['created_at']); ?></td>
                    <td class="text-right px-5 py-3.5 border-b border-gray-50 align-middle">
                        <button type="button" 
                                onclick="toggleDropdown(event, this, '<?php echo $u['id']; ?>')"
                                class="text-red-500 hover:text-red-700 transition-colors dropdown-trigger">
                            <i class="ph ph-dots-three-vertical text-xl pointer-events-none font-bold"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="px-5 py-3 border-t border-gray-50 flex items-center justify-between text-[12px] text-slate-500">
        <div>Showing 1 to <?php echo count($users_raw); ?> of <?php echo count($users_raw); ?> results</div>
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

<!-- Dropdown Menu Global -->
<div id="dropdown-menu-global" class="hidden fixed w-32 bg-white border border-gray-100 rounded-lg shadow-xl z-[9999] text-left animate-in fade-in zoom-in duration-100">
    <div class="py-1">
        <a id="dropdown-edit-link" href="#" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-500 hover:bg-red-50 transition-colors font-bold">
            <i class="ph ph-note-pencil text-lg"></i> Edit
        </a>
    </div>
</div>

<script>
    function toggleDropdown(event, btn, id) {
        event.stopPropagation();
        const menu = document.getElementById('dropdown-menu-global');
        const editLink = document.getElementById('dropdown-edit-link');

        if (!menu.classList.contains('hidden') && menu.dataset.activeId == id) {
            menu.classList.add('hidden');
            return;
        }

        editLink.href = 'edit-user.php?id=' + id;

        menu.classList.remove('hidden');
        const rect = btn.getBoundingClientRect();
        
        // Positioning
        let top = rect.bottom + 8;
        let left = rect.right - menu.offsetWidth;
        
        menu.style.top = top + 'px';
        menu.style.left = left + 'px';
        menu.dataset.activeId = id;
    }

    window.onclick = function(event) {
        const menu = document.getElementById('dropdown-menu-global');
        if (!event.target.closest('#dropdown-menu-global') && !event.target.closest('.dropdown-trigger')) {
            menu.classList.add('hidden');
        }
    }

    document.getElementById('user-search-input').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        const rows = document.querySelectorAll('#user-table tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
