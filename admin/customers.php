<?php
$pageTitle   = 'Pelanggan';
$currentPage = 'customers';
require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();

require_once __DIR__ . '/../classes/CustomerAdminService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';

$customerAdminService = new CustomerAdminService();
$activityLogService = new ActivityLogService();

// ── POST: Delete ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'delete') {
    $deleteId = intval($_GET['id'] ?? 0);
    if ($deleteId && CsrfService::verify()) {
        $customerAdminService->softDelete($deleteId);
        $activityLogService->log('deleted', 'App\Models\Customer', $deleteId, 'deleted');
        FlashMessage::set('success', 'Pelanggan berhasil dihapus.');
    }
    header('Location: customers.php');
    exit;
}

include __DIR__ . '/../includes/header-admin.php';

$customers = $customerAdminService->getAll();
?>



<!-- Page Header -->
<div class="text-[12px] text-gray-400 mb-1">Pelanggan &rsaquo; <span class="text-gray-700">List</span></div>
<div class="flex items-center justify-between mb-5">
    <h1 class="text-[22px] font-extrabold text-navy">Pelanggan</h1>
    <a href="create-customer.php" class="inline-flex items-center gap-1.5 bg-primary text-white text-[13px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark" id="btn-new-customer">
        <i class="ph-bold ph-plus text-sm"></i> New Pelanggan
    </a>
</div>

<!-- Table -->
<div class="bg-white border border-gray-100 rounded-xl overflow-visible">
    <!-- Toolbar -->
    <div class="flex items-center justify-end gap-2 px-4 py-3 border-b border-gray-50">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="border border-gray-200 rounded-lg py-1.5 pl-[30px] pr-3 text-[12px] outline-none bg-gray-50 w-[200px] transition-colors focus:border-primary focus:bg-white" id="customer-search">
        </div>
        <button class="w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-colors hover:bg-gray-50 hover:text-gray-700" title="Filter">
            <i class="ph ph-funnel text-sm"></i>
        </button>
        <button class="w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-primary cursor-pointer transition-colors hover:bg-gray-50" title="Kolom">
            <i class="ph-bold ph-squares-four text-sm"></i>
        </button>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[12.5px] border-collapse">
            <thead>
                <tr>
                    <th class="w-9 text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50"><input type="checkbox" class="w-[15px] h-[15px] accent-primary cursor-pointer" id="cb-all" onchange="toggleAll(this)"></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Nama <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">WhatsApp</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Email</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Pesanan <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $row): ?>
                <tr>
                    <td class="w-9 px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle"><input type="checkbox" class="w-[15px] h-[15px] accent-primary cursor-pointer cb-row"></td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <div class="font-bold text-[13px] text-primary"><?php echo htmlspecialchars($row['name']); ?></div>
                        <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($row['organization']); ?></div>
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <span class="flex items-center gap-1.5 text-gray-500">
                            <i class="ph ph-phone text-gray-300 text-sm"></i>
                            <?php echo htmlspecialchars($row['phone']); ?>
                        </span>
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <span class="flex items-center gap-1.5 text-gray-400">
                            <i class="ph ph-envelope text-gray-300 text-sm"></i>
                            <?php echo htmlspecialchars($row['email']); ?>
                        </span>
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600"><?php echo $row['order_count']; ?></span>
                    </td>
                    <td class="text-right px-3.5 py-3 border-b border-gray-50/50 align-middle">
                        <div class="relative inline-block text-left dropdown-container">
                            <button type="button" class="w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-colors hover:bg-gray-50 hover:text-gray-700 dropdown-trigger" title="Opsi lainnya" onclick="toggleDropdown(event, this)">
                                <i class="ph ph-dots-three-vertical text-sm pointer-events-none"></i>
                            </button>
                            <div class="hidden absolute right-0 mt-2 w-32 bg-white border border-gray-100 rounded-lg shadow-lg z-50 dropdown-menu text-left">
                                <div class="py-1">
                                    <a href="view-customer.php?id=<?php echo $row['id']; ?>" class="flex items-center gap-2 px-4 py-2 text-[12px] text-gray-600 hover:bg-gray-50 transition-colors">
                                        <i class="ph ph-eye text-base text-gray-400"></i> View
                                    </a>
                                    <a href="edit-customer.php?id=<?php echo $row['id']; ?>" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-500 hover:bg-red-50 transition-colors font-medium">
                                        <i class="ph ph-note-pencil text-base text-red-400"></i> Edit
                                    </a>
                                    <button type="button" onclick="confirmDelete(<?php echo $row['id']; ?>)" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-600 hover:bg-red-50 transition-colors w-full text-left font-medium">
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
    <div class="px-4 py-3 border-t border-gray-50 flex items-center justify-between text-[12px] text-gray-400 gap-3 flex-wrap">
        <span>Showing <?php echo count($customers); ?> result<?php echo count($customers) > 1 ? 's' : ''; ?></span>
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

<!-- Hidden CSRF for JS actions -->
<div class="hidden"><?php echo CsrfService::field(); ?></div>

<script>
    function toggleAll(master) {
        document.querySelectorAll('.cb-row').forEach(cb => cb.checked = master.checked);
    }

    // Dropdown toggle
    function toggleDropdown(event, btn) {
        event.stopPropagation();
        // Close other dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== btn.nextElementSibling) {
                menu.classList.add('hidden');
            }
        });
        // Toggle current
        const menu = btn.nextElementSibling;
        menu.classList.toggle('hidden');
    }

    // Close dropdowns on outside click
    window.onclick = function(event) {
        if (!event.target.closest('.dropdown-container')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    }

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'customers.php?action=delete&id=' + id;
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="csrf_token"]')?.value || '';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.getElementById('customer-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.data-table tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
