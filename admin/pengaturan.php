<?php
$pageTitle   = 'Pengguna';
$currentPage = 'users';
require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();
Auth::admin()->requireSuperAdmin();

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';
require_once __DIR__ . '/../classes/CsrfService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';

$adminService = new AdminService();
$activityLogService = new ActivityLogService();

// ── POST: Delete ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'delete') {
    $deleteId = intval($_GET['id'] ?? 0);
    if ($deleteId && CsrfService::verify()) {
        if ($adminService->deleteUser($deleteId)) {
            $activityLogService->log('deleted', 'App\Models\User', $deleteId, 'deleted admin user');
            FlashMessage::set('success', 'Pengguna berhasil dihapus.');
        } else {
            FlashMessage::set('error', 'Gagal menghapus pengguna (Anda tidak bisa menghapus diri sendiri).');
        }
    }
    header('Location: pengaturan.php');
    exit;
}

$users = $adminService->getAll();

include __DIR__ . '/../includes/header-admin.php';
?>

<!-- Page Header -->
<div class="text-[12px] text-gray-400 mb-1">Pengguna &rsaquo; <span class="text-gray-700">List</span></div>
<div class="flex items-center justify-between mb-5">
    <h1 class="text-[22px] font-extrabold text-navy">Pengguna</h1>
    <a href="create-user.php"
        class="inline-flex items-center gap-1.5 bg-primary text-white text-[13px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark"
        id="btn-new-user">
        <i class="ph-bold ph-plus text-sm"></i> New Pengguna
    </a>
</div>

<!-- Table Section -->
<div class="bg-white border border-gray-100 rounded-xl overflow-visible shadow-sm">
    <!-- Toolbar -->
    <div class="flex items-center justify-end gap-2 px-4 py-3 border-b border-gray-50">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search"
                class="border border-gray-200 rounded-lg py-1.5 pl-[30px] pr-3 text-[12px] outline-none bg-gray-50 w-[200px] transition-colors focus:border-primary focus:bg-white"
                id="user-search">
        </div>
        <button
            class="w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-colors hover:bg-gray-50 hover:text-gray-700"
            title="Filter">
            <i class="ph ph-funnel text-sm"></i>
        </button>
        <button
            class="w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-primary cursor-pointer transition-colors hover:bg-gray-50"
            title="Kolom">
            <i class="ph-bold ph-squares-four text-sm"></i>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-[12.5px] border-collapse" id="user-table">
            <thead>
                <tr>
                    <th class="w-9 text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">
                        <input type="checkbox" class="w-[15px] h-[15px] accent-primary cursor-pointer" id="cb-all" onchange="toggleAll(this)">
                    </th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Nama <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Email</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">WhatsApp</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Role</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Terdaftar <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="w-9 px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <input type="checkbox" class="w-[15px] h-[15px] accent-primary cursor-pointer cb-row">
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <div class="font-bold text-[13px] text-primary"><?php echo htmlspecialchars($u['name']); ?></div>
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <div class="flex items-center gap-1.5 text-gray-400">
                            <i class="ph ph-envelope text-gray-300 text-sm"></i>
                            <span><?php echo htmlspecialchars($u['email']); ?></span>
                        </div>
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <div class="flex items-center gap-1.5 text-gray-500">
                            <i class="ph ph-phone text-gray-300 text-sm"></i>
                            <span><?php echo $u['phone'] ? htmlspecialchars($u['phone']) : '<span class="text-gray-300">—</span>'; ?></span>
                        </div>
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <?php if ($u['role'] === 'super_admin'): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-600">Super Admin</span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-600"><?php echo ucfirst($u['role'] ?? 'User'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-500 align-middle"><?php echo FormatHelper::tanggal($u['created_at']); ?></td>
                    <td class="text-right px-3.5 py-3 border-b border-gray-50/50 align-middle">
                        <div class="relative inline-block text-left">
                            <button type="button"
                                class="w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-colors hover:bg-gray-50 hover:text-gray-700 dropdown-trigger"
                                title="Opsi lainnya" onclick="toggleDropdown(event, this, '<?php echo $u['id']; ?>')">
                                <i class="ph ph-dots-three-vertical text-sm pointer-events-none"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="px-4 py-3 border-t border-gray-50 flex items-center justify-between text-[12px] text-gray-400 gap-3 flex-wrap">
        <div>Showing <?php echo count($users); ?> result<?php echo count($users) > 1 ? 's' : ''; ?></div>
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

<!-- Dropdown Menu Global -->
<div id="dropdown-menu-global" class="hidden fixed w-32 bg-white border border-gray-100 rounded-lg shadow-lg z-[9999] text-left">
    <div class="py-1">
        <a id="dropdown-edit-link" href="#" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-500 hover:bg-red-50 transition-colors font-medium">
            <i class="ph ph-note-pencil text-base text-red-400"></i> Edit
        </a>
        <button id="dropdown-delete-btn" type="button" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-600 hover:bg-red-50 transition-colors w-full text-left font-medium">
            <i class="ph ph-trash text-base text-red-500"></i> Delete
        </button>
    </div>
</div>

<!-- Hidden CSRF for JS actions -->
<div class="hidden"><?php echo CsrfService::field(); ?></div>

<script>
    function toggleAll(master) {
        document.querySelectorAll('.cb-row').forEach(cb => cb.checked = master.checked);
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

        editLink.href = 'edit-user.php?id=' + id;
        deleteBtn.onclick = function() { confirmDelete(id); };

        menu.classList.remove('hidden');
        const rect = btn.getBoundingClientRect();
        const menuWidth = menu.offsetWidth;
        let top = rect.bottom + 8;
        let left = rect.right - menuWidth;

        if (left < 10) left = 10;
        if (top + menu.offsetHeight > window.innerHeight) {
            top = rect.top - menu.offsetHeight - 8;
        }

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

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'pengaturan.php?action=delete&id=' + id;
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = document.querySelector('input[name="csrf_token"]')?.value || '';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.getElementById('user-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#user-table tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
