<?php
$pageTitle   = 'Pesanan';
$currentPage = 'orders';
// TODO: Uncomment when auth ready
// require_once __DIR__ . '/../includes/auth.php';
// requireAdmin();
include __DIR__ . '/../includes/header-admin.php';

// ── Mock Data ──
$orders = [
    [
        'id'          => 1,
        'no'          => 'ORD-NAEU0M9Z',
        'customer'    => 'Customer',
        'phone'       => '08123456789',
        'batch'       => 'Batch 1',
        'status'      => 'processing',
        'pickup_code' => 'PSTXCY',
        'total'       => 'IDR 7,500,000.00',
        'diambil'     => null,
        'tanggal'     => '15 Feb 2026',
    ],
    [
        'id'          => 2,
        'no'          => 'ORD-29T8TFXY',
        'customer'    => 'Customer',
        'phone'       => '08123456789',
        'batch'       => 'Batch 1',
        'status'      => 'pending',
        'pickup_code' => 'YUHS9K',
        'total'       => 'IDR 2,500,000.00',
        'diambil'     => null,
        'tanggal'     => '15 Feb 2026',
    ],
];

$statusMap = [
    'processing' => ['label' => 'Processing', 'icon' => 'ph-arrows-clockwise', 'bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#dbeafe'],
    'pending'    => ['label' => 'Pending',    'icon' => 'ph-clock',            'bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'],
    'ready'      => ['label' => 'Ready',      'icon' => 'ph-check-circle',     'bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'],
    'picked_up'  => ['label' => 'Picked Up',  'icon' => 'ph-bag-simple',       'bg' => '#f9fafb', 'color' => '#6b7280', 'border' => '#e5e7eb'],
];
?>

<style>
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.breadcrumb  { font-size:12px; color:#9ca3af; margin-bottom:4px; }
.breadcrumb span { color:#374151; }

.btn-primary { display:inline-flex; align-items:center; gap:6px; background:#E02424; color:#fff;
    font-size:13px; font-weight:700; padding:8px 18px; border-radius:8px;
    border:none; cursor:pointer; transition:background .15s; text-decoration:none; }
.btn-primary:hover { background:#9B1C1C; }

/* Table container */
.table-wrap { background:#fff; border:1px solid #f1f5f9; border-radius:12px; overflow:hidden; }
.table-toolbar { display:flex; align-items:center; justify-content:flex-end; gap:8px; padding:12px 16px; border-bottom:1px solid #f8fafc; }

.mini-search { border:1px solid #e5e7eb; border-radius:7px; padding:6px 10px 6px 30px; font-size:12px; outline:none; background:#f9fafb; width:200px; transition:border-color .15s; }
.mini-search:focus { border-color:#E02424; background:#fff; }

.icon-btn-sm { width:30px; height:30px; border-radius:6px; border:1px solid #e5e7eb; background:#fff;
    display:inline-flex; align-items:center; justify-content:center; color:#9ca3af;
    cursor:pointer; transition:background .15s, color .15s; }
.icon-btn-sm:hover { background:#f8fafc; color:#374151; }

.data-table { width:100%; text-align:left; font-size:12.5px; border-collapse:collapse; }
.data-table th { font-size:11.5px; font-weight:600; color:#9ca3af; padding:10px 14px; border-bottom:1px solid #f1f5f9; white-space:nowrap; background:#fafafa; }
.data-table td { padding:13px 14px; border-bottom:1px solid #f8fafc; color:#374151; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tbody tr:hover td { background:#fafafa; }

.cb-cell { width:36px; }
.cb { width:15px; height:15px; accent-color:#E02424; cursor:pointer; }

.status-inline { display:inline-flex; align-items:center; gap:5px;
    padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; border-width:1px; border-style:solid; }

.table-footer { padding:12px 16px; border-top:1px solid #f8fafc; display:flex; align-items:center;
    justify-content:space-between; font-size:12px; color:#9ca3af; gap:12px; flex-wrap:wrap; }
.per-page-select { border:1px solid #e5e7eb; border-radius:6px; padding:4px 24px 4px 8px; font-size:12px;
    outline:none; background:#fff; appearance:none; cursor:pointer; }

.action-pill { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:6px;
    font-size:11.5px; font-weight:600; cursor:pointer; transition:background .15s; border:none; background:transparent; }
</style>

<!-- Page Header -->
<div class="breadcrumb">Pesanan &rsaquo; <span>List</span></div>
<div class="page-header">
    <h1 class="text-[22px] font-extrabold text-navy">Pesanan</h1>
    <a href="create-order.php" class="btn-primary" id="btn-new-order">
        <i class="ph-bold ph-plus text-sm"></i> New Pesanan
    </a>
</div>

<!-- Table -->
<div class="table-wrap">
    <!-- Toolbar -->
    <div class="table-toolbar">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="mini-search" id="order-search">
        </div>
        <button class="icon-btn-sm" title="Filter">
            <i class="ph ph-funnel text-sm"></i>
        </button>
        <button class="icon-btn-sm" title="Kolom" style="color:#E02424;">
            <i class="ph-bold ph-squares-four text-sm"></i>
        </button>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="cb-cell"><input type="checkbox" class="cb" id="cb-all" onchange="toggleAll(this)"></th>
                    <th>No. Pesanan <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Pelanggan <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Batch</th>
                    <th>Status</th>
                    <th>Kode Pickup</th>
                    <th>Total <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Diambil <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Tanggal Order <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $row):
                    $st = $statusMap[$row['status']] ?? ['label'=>$row['status'],'icon'=>'ph-circle','bg'=>'#f9fafb','color'=>'#6b7280','border'=>'#e5e7eb'];
                ?>
                <tr>
                    <td class="cb-cell"><input type="checkbox" class="cb cb-row"></td>
                    <td class="font-bold text-navy"><?php echo $row['no']; ?></td>
                    <td>
                        <div class="font-semibold text-[12.5px]" style="color:#E02424"><?php echo $row['customer']; ?></div>
                        <div class="text-[11px] text-gray-400"><?php echo $row['phone']; ?></div>
                    </td>
                    <td>
                        <span class="text-[11px] bg-blue-50 text-blue-600 border border-blue-100 px-2 py-0.5 rounded font-semibold">
                            <?php echo $row['batch']; ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-inline"
                              style="background:<?php echo $st['bg'];?>;color:<?php echo $st['color'];?>;border-color:<?php echo $st['border'];?>">
                            <i class="ph <?php echo $st['icon']; ?> text-xs"></i>
                            <?php echo $st['label']; ?>
                        </span>
                    </td>
                    <td class="font-mono text-[12px] text-gray-500"><?php echo $row['pickup_code']; ?></td>
                    <td class="font-semibold" style="color:#E02424"><?php echo $row['total']; ?></td>
                    <td class="text-gray-400 text-[12px]"><?php echo $row['diambil'] ?? '—'; ?></td>
                    <td class="text-primary font-semibold text-[12px]"><?php echo $row['tanggal']; ?></td>
                    <td class="text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <button class="action-pill text-gray-400 hover:bg-red-50 hover:text-primary" title="Download PDF">
                                <i class="ph ph-file-pdf text-base"></i> PDF
                            </button>
                            <div class="relative inline-block text-left dropdown-container">
                                <button type="button" class="icon-btn-sm dropdown-trigger" title="Opsi lainnya" onclick="toggleDropdown(event, this)">
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
    <div class="table-footer">
        <span>Showing 1 to <?php echo count($orders); ?> of <?php echo count($orders); ?> results</span>
        <div class="flex items-center gap-2">
            <span>Per page</span>
            <div class="relative">
                <select class="per-page-select">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                <i class="ph ph-caret-down absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
            </div>
        </div>
    </div>
</div>

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
            window.location.href = 'delete-order.php?id=' + id;
        }
    }

    // Live search
    document.getElementById('order-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.data-table tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
