<?php
$pageTitle   = 'Log Aktivitas';
$currentPage = 'activity-log';
// TODO: Uncomment when auth ready
// require_once __DIR__ . '/../includes/auth.php';
// requireAdmin();
include __DIR__ . '/../includes/header-admin.php';

// ── Mock Data ──
$logs = [
    ['id'=>1,'user'=>'Super Admin','event'=>'Pesanan Dibuat','desc'=>'Membuat pesanan baru ORD-NAEU0M9Z untuk Customer','created_at'=>'15 Feb 2026, 18:34'],
    ['id'=>2,'user'=>'Super Admin','event'=>'Pesanan Dibuat','desc'=>'Membuat pesanan baru ORD-29T8TFXY untuk Customer','created_at'=>'15 Feb 2026, 18:34'],
    ['id'=>3,'user'=>'Super Admin','event'=>'Login','desc'=>'Admin login via browser','created_at'=>'15 Feb 2026, 18:20'],
];
$eventColors = [
    'Pesanan Dibuat' => ['bg'=>'#eff6ff','color'=>'#2563eb','border'=>'#dbeafe'],
    'Login'          => ['bg'=>'#ecfdf5','color'=>'#059669','border'=>'#a7f3d0'],
    'Batch Update'   => ['bg'=>'#fffbeb','color'=>'#d97706','border'=>'#fde68a'],
    'Hapus'          => ['bg'=>'#fef2f2','color'=>'#E02424','border'=>'#fecaca'],
];
?>

<style>
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.breadcrumb  { font-size:12px; color:#9ca3af; margin-bottom:4px; }
.table-wrap    { background:#fff; border:1px solid #f1f5f9; border-radius:12px; overflow:hidden; }
.table-toolbar { display:flex; align-items:center; justify-content:flex-end; gap:8px; padding:12px 16px; border-bottom:1px solid #f8fafc; }
.mini-search { border:1px solid #e5e7eb; border-radius:7px; padding:6px 10px 6px 30px; font-size:12px; outline:none; background:#f9fafb; width:200px; transition:border-color .15s; }
.mini-search:focus { border-color:#E02424; background:#fff; }
.data-table { width:100%; text-align:left; font-size:12.5px; border-collapse:collapse; }
.data-table th { font-size:11.5px; font-weight:600; color:#9ca3af; padding:10px 14px; border-bottom:1px solid #f1f5f9; white-space:nowrap; background:#fafafa; }
.data-table td { padding:13px 14px; border-bottom:1px solid #f8fafc; color:#374151; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tbody tr:hover td { background:#fafafa; }
.event-pill { display:inline-flex; align-items:center; padding:2px 9px; border-radius:999px;
    font-size:11px; font-weight:600; border-width:1px; border-style:solid; }
.table-footer { padding:12px 16px; border-top:1px solid #f8fafc; display:flex; align-items:center;
    justify-content:space-between; font-size:12px; color:#9ca3af; }
</style>

<div class="breadcrumb">Audit & Log &rsaquo; <span>Log Aktivitas</span></div>
<div class="page-header">
    <h1 class="text-[22px] font-extrabold text-navy">Log Aktivitas</h1>
</div>

<div class="table-wrap">
    <div class="table-toolbar">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="mini-search" id="log-search">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pengguna</th>
                    <th>Event</th>
                    <th>Deskripsi</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log):
                    $ev = $eventColors[$log['event']] ?? ['bg'=>'#f9fafb','color'=>'#6b7280','border'=>'#e5e7eb'];
                ?>
                <tr>
                    <td class="text-gray-400 text-[11px]"><?php echo $log['id']; ?></td>
                    <td class="font-semibold text-navy"><?php echo htmlspecialchars($log['user']); ?></td>
                    <td>
                        <span class="event-pill"
                              style="background:<?php echo $ev['bg'];?>;color:<?php echo $ev['color'];?>;border-color:<?php echo $ev['border'];?>">
                            <?php echo htmlspecialchars($log['event']); ?>
                        </span>
                    </td>
                    <td class="text-gray-500 text-[12px]"><?php echo htmlspecialchars($log['desc']); ?></td>
                    <td class="text-gray-400 text-[11.5px]"><?php echo $log['created_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        <span>Showing <?php echo count($logs); ?> results</span>
    </div>
</div>

<script>
    document.getElementById('log-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.data-table tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
