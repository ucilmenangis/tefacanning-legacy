<?php
$pageTitle   = 'Pelanggan';
$currentPage = 'customers';
// TODO: Uncomment when auth ready
// require_once __DIR__ . '/../includes/auth.php';
// requireAdmin();
include __DIR__ . '/../includes/header-admin.php';

// ── Mock Data ──
$customers = [
    [
        'id'           => 1,
        'name'         => 'Customer',
        'organization' => 'customer_organization',
        'phone'        => '08123456789',
        'email'        => 'customer@customer.com',
        'orders'       => 2,
    ],
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

.table-wrap    { background:#fff; border:1px solid #f1f5f9; border-radius:12px; overflow:hidden; }
.table-toolbar { display:flex; align-items:center; justify-content:flex-end; gap:8px; padding:12px 16px; border-bottom:1px solid #f8fafc; }
.mini-search { border:1px solid #e5e7eb; border-radius:7px; padding:6px 10px 6px 30px; font-size:12px; outline:none; background:#f9fafb; width:200px; transition:border-color .15s; }
.mini-search:focus { border-color:#E02424; background:#fff; }
.icon-btn-sm { width:30px; height:30px; border-radius:6px; border:1px solid #e5e7eb; background:#fff;
    display:inline-flex; align-items:center; justify-content:center; color:#9ca3af; cursor:pointer; transition:background .15s; }
.icon-btn-sm:hover { background:#f8fafc; color:#374151; }

.data-table { width:100%; text-align:left; font-size:12.5px; border-collapse:collapse; }
.data-table th { font-size:11.5px; font-weight:600; color:#9ca3af; padding:10px 14px; border-bottom:1px solid #f1f5f9; white-space:nowrap; background:#fafafa; }
.data-table td { padding:14px 14px; border-bottom:1px solid #f8fafc; color:#374151; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tbody tr:hover td { background:#fafafa; }

.cb-cell { width:36px; }
.cb { width:15px; height:15px; accent-color:#E02424; cursor:pointer; }

.order-badge { display:inline-flex; align-items:center; justify-content:center;
    min-width:22px; height:22px; border-radius:999px; background:#fef2f2;
    color:#E02424; font-size:11px; font-weight:700; padding:0 6px; }

.table-footer { padding:12px 16px; border-top:1px solid #f8fafc; display:flex; align-items:center;
    justify-content:space-between; font-size:12px; color:#9ca3af; gap:12px; flex-wrap:wrap; }
.per-page-select { border:1px solid #e5e7eb; border-radius:6px; padding:4px 24px 4px 8px; font-size:12px;
    outline:none; background:#fff; appearance:none; cursor:pointer; }
</style>

<!-- Page Header -->
<div class="breadcrumb">Pelanggan &rsaquo; <span>List</span></div>
<div class="page-header">
    <h1 class="text-[22px] font-extrabold text-navy">Pelanggan</h1>
    <a href="create-customer.php" class="btn-primary" id="btn-new-customer">
        <i class="ph-bold ph-plus text-sm"></i> New Pelanggan
    </a>
</div>

<!-- Table -->
<div class="table-wrap">
    <!-- Toolbar -->
    <div class="table-toolbar">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="mini-search" id="customer-search">
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
                    <th>Nama <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>WhatsApp</th>
                    <th>Email</th>
                    <th>Pesanan <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $row): ?>
                <tr>
                    <td class="cb-cell"><input type="checkbox" class="cb cb-row"></td>
                    <td>
                        <div class="font-bold text-[13px]" style="color:#E02424"><?php echo htmlspecialchars($row['name']); ?></div>
                        <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($row['organization']); ?></div>
                    </td>
                    <td>
                        <span class="flex items-center gap-1.5 text-gray-500">
                            <i class="ph ph-phone text-gray-300 text-sm"></i>
                            <?php echo htmlspecialchars($row['phone']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="flex items-center gap-1.5 text-gray-400">
                            <i class="ph ph-envelope text-gray-300 text-sm"></i>
                            <?php echo htmlspecialchars($row['email']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="order-badge"><?php echo $row['orders']; ?></span>
                    </td>
                    <td class="text-right">
                        <button class="icon-btn-sm" title="Opsi lainnya">
                            <i class="ph ph-dots-three-vertical text-sm"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="table-footer">
        <span>Showing <?php echo count($customers); ?> result<?php echo count($customers) > 1 ? 's' : ''; ?></span>
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
    document.getElementById('customer-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.data-table tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
