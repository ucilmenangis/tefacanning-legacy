<?php
$pageTitle   = 'Pesanan';
$currentPage = 'orders';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

require_once __DIR__ . '/../classes/FormatHelper.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';

$activityLogService = new ActivityLogService();

// ── POST: Delete ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'delete') {
    $deleteId = intval($_GET['id'] ?? 0);
    if ($deleteId && verifyCsrf()) {
        db_update("UPDATE orders SET deleted_at = NOW() WHERE id = ?", [$deleteId]);
        $activityLogService->log('deleted', 'App\Models\Order', $deleteId, 'deleted');
        setFlash('success', 'Pesanan berhasil dihapus.');
    }
    header('Location: orders.php');
    exit;
}

include __DIR__ . '/../includes/header-admin.php';

$orders = db_fetch_all(
    "SELECT o.id, o.order_number, o.status, o.pickup_code, o.total_amount, o.picked_up_at, o.created_at,
            c.name AS customer_name, c.phone AS customer_phone,
            b.name AS batch_name
     FROM orders o
     JOIN customers c ON c.id = o.customer_id
     JOIN batches b ON b.id = o.batch_id
     WHERE o.deleted_at IS NULL
     ORDER BY o.created_at DESC"
);

$statusMap = [
    'processing' => ['label' => 'Processing', 'icon' => 'ph-arrows-clockwise', 'bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#dbeafe'],
    'pending'    => ['label' => 'Pending',    'icon' => 'ph-clock',            'bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'],
    'ready'      => ['label' => 'Ready',      'icon' => 'ph-check-circle',     'bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'],
    'picked_up'  => ['label' => 'Picked Up',  'icon' => 'ph-bag-simple',       'bg' => '#f9fafb', 'color' => '#6b7280', 'border' => '#e5e7eb'],
];
?>



<!-- Page Header -->
<div class="text-[12px] text-gray-400 mb-1">Pesanan &rsaquo; <span class="text-gray-700">List</span></div>
<div class="flex items-center justify-between mb-5">
    <h1 class="text-[22px] font-extrabold text-navy">Pesanan</h1>
    <a href="create-order.php" class="inline-flex items-center gap-1.5 bg-primary text-white text-[13px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark" id="btn-new-order">
        <i class="ph-bold ph-plus text-sm"></i> New Pesanan
    </a>
</div>

<div class="bg-white border border-gray-100 rounded-xl overflow-visible">
    <!-- Toolbar -->
    <div class="flex items-center justify-end gap-2 px-4 py-3 border-b border-gray-50">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="border border-gray-200 rounded-lg py-1.5 pl-[30px] pr-3 text-[12px] outline-none bg-gray-50 w-[200px] transition-colors focus:border-primary focus:bg-white" id="order-search">
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
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">No. Pesanan <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Pelanggan <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Batch</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Status</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Kode Pickup</th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Total <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Diambil <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50">Tanggal Order <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th class="text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $row):
                    $st = $statusMap[$row['status']] ?? ['label'=>$row['status'],'icon'=>'ph-circle','bg'=>'#f9fafb','color'=>'#6b7280','border'=>'#e5e7eb'];
                ?>
                <tr>
                    <td class="w-9 px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle"><input type="checkbox" class="w-[15px] h-[15px] accent-primary cursor-pointer cb-row"></td>
                    <td class="font-bold text-navy px-3.5 py-3 border-b border-gray-50/50 align-middle"><?php echo htmlspecialchars($row['order_number']); ?></td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <div class="font-semibold text-[12.5px] text-primary"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                        <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($row['customer_phone'] ?? '-'); ?></div>
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <span class="text-[11px] bg-blue-50 text-blue-600 border border-blue-100 px-2 py-0.5 rounded font-semibold">
                            <?php echo htmlspecialchars($row['batch_name']); ?>
                        </span>
                    </td>
                    <td class="px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border"
                              style="background:<?php echo $st['bg'];?>;color:<?php echo $st['color'];?>;border-color:<?php echo $st['border'];?>">
                            <i class="ph <?php echo $st['icon']; ?> text-xs"></i>
                            <?php echo $st['label']; ?>
                        </span>
                    </td>
                    <td class="font-mono text-[12px] text-gray-500 px-3.5 py-3 border-b border-gray-50/50 align-middle"><?php echo htmlspecialchars($row['pickup_code']); ?></td>
                    <td class="font-semibold text-primary px-3.5 py-3 border-b border-gray-50/50 align-middle"><?php echo FormatHelper::rupiah($row['total_amount']); ?></td>
                    <td class="text-gray-400 text-[12px] px-3.5 py-3 border-b border-gray-50/50 align-middle"><?php echo $row['picked_up_at'] ? FormatHelper::tanggal($row['picked_up_at']) : '—'; ?></td>
                    <td class="text-primary font-semibold text-[12px] px-3.5 py-3 border-b border-gray-50/50 align-middle"><?php echo FormatHelper::tanggal($row['created_at']); ?></td>
                    <td class="text-right px-3.5 py-3 border-b border-gray-50/50 align-middle">
                        <div class="flex items-center gap-1 justify-end">
                            <a href="../api/download-pdf.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11.5px] font-semibold cursor-pointer transition-colors border-0 bg-transparent text-gray-400 hover:bg-red-50 hover:text-primary" title="Download PDF">
                                <i class="ph ph-file-pdf text-base"></i> PDF
                            </a>
                            <div class="relative inline-block text-left dropdown-container">
                                <button type="button" class="w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-colors hover:bg-gray-50 hover:text-gray-700 dropdown-trigger" title="Opsi lainnya" onclick="toggleDropdown(event, this)">
                                    <i class="ph ph-dots-three-vertical text-sm pointer-events-none"></i>
                                </button>
                                <div class="hidden absolute right-0 mt-2 w-32 bg-white border border-gray-100 rounded-lg shadow-lg z-50 dropdown-menu">
                                    <div class="py-1">
                                        <a href="view-order.php?id=<?php echo $row['id']; ?>" class="flex items-center gap-2 px-4 py-2 text-[12px] text-gray-600 hover:bg-gray-50 transition-colors">
                                            <i class="ph ph-eye text-base text-gray-400"></i> View
                                        </a>
                                        <a href="edit-order.php?id=<?php echo $row['id']; ?>" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-500 hover:bg-red-50 transition-colors font-medium">
                                            <i class="ph ph-note-pencil text-base text-red-400"></i> Edit
                                        </a>
                                        <button type="button" onclick="confirmDelete(<?php echo $row['id']; ?>)" class="flex items-center gap-2 px-4 py-2 text-[12px] text-red-600 hover:bg-red-50 transition-colors w-full text-left font-medium">
                                            <i class="ph ph-trash text-base text-red-500"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer / Pagination -->
    <div class="px-4 py-3 border-t border-gray-50 flex items-center justify-between text-[12px] text-gray-400 gap-3 flex-wrap">
        <span>Showing 1 to <?php echo count($orders); ?> of <?php echo count($orders); ?> results</span>
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
<div class="hidden"><?php echo csrfField(); ?></div>

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
        if (confirm('Apakah Anda yakin ingin menghapus pesanan ini?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'orders.php?action=delete&id=' + id;
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = document.querySelector('input[name="csrf_token"]')?.value || '';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Live search
    document.getElementById('order-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
