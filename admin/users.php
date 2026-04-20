<?php
$pageTitle   = 'Pengguna';
$currentPage = 'users';
// TODO: Uncomment when auth ready (super_admin only)
// require_once __DIR__ . '/../includes/auth.php';
// requireAdmin();
include __DIR__ . '/../includes/header-admin.php';

// ── Mock Data ──
$users = [
    ['id'=>1,'name'=>'Super Admin','email'=>'superadmin@tefa.polije.ac.id','role'=>'super_admin','phone'=>'08111000001'],
    ['id'=>2,'name'=>'Teknisi',    'email'=>'teknisi@tefa.polije.ac.id',   'role'=>'teknisi',    'phone'=>'08111000002'],
];
$roleMap = [
    'super_admin' => ['label'=>'Super Admin','bg'=>'#fef2f2','color'=>'#E02424','border'=>'#fecaca'],
    'teknisi'     => ['label'=>'Teknisi',    'bg'=>'#eff6ff','color'=>'#2563eb','border'=>'#dbeafe'],
];
?>

<style>
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.breadcrumb  { font-size:12px; color:#9ca3af; margin-bottom:4px; }
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
.data-table { width:100%; text-align:left; font-size:12.5px; border-collapse:collapse; }
.data-table th { font-size:11.5px; font-weight:600; color:#9ca3af; padding:10px 14px; border-bottom:1px solid #f1f5f9; white-space:nowrap; background:#fafafa; }
.data-table td { padding:13px 14px; border-bottom:1px solid #f8fafc; color:#374151; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tbody tr:hover td { background:#fafafa; }
.cb-cell { width:36px; }
.cb { width:15px; height:15px; accent-color:#E02424; cursor:pointer; }
.role-pill { display:inline-flex; align-items:center; padding:2px 9px; border-radius:999px;
    font-size:11px; font-weight:600; border-width:1px; border-style:solid; }
.table-footer { padding:12px 16px; border-top:1px solid #f8fafc; display:flex; align-items:center;
    justify-content:space-between; font-size:12px; color:#9ca3af; }
</style>

<div class="breadcrumb">Pengaturan &rsaquo; <span>Pengguna</span></div>
<div class="page-header">
    <h1 class="text-[22px] font-extrabold text-navy">Pengguna</h1>
    <a href="create-user.php" class="btn-primary" id="btn-new-user">
        <i class="ph-bold ph-plus text-sm"></i> New Pengguna
    </a>
</div>

<div class="table-wrap">
    <div class="table-toolbar">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="mini-search" id="user-search">
        </div>
        <button class="icon-btn-sm"><i class="ph ph-funnel text-sm"></i></button>
        <button class="icon-btn-sm" style="color:#E02424;"><i class="ph-bold ph-squares-four text-sm"></i></button>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="cb-cell"><input type="checkbox" class="cb" id="cb-all" onchange="toggleAll(this)"></th>
                    <th>Nama <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Role</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user):
                    $role = $roleMap[$user['role']] ?? ['label'=>$user['role'],'bg'=>'#f9fafb','color'=>'#6b7280','border'=>'#e5e7eb'];
                ?>
                <tr>
                    <td class="cb-cell"><input type="checkbox" class="cb cb-row"></td>
                    <td class="font-bold text-navy"><?php echo htmlspecialchars($user['name']); ?></td>
                    <td class="text-gray-500"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="text-gray-400 font-mono text-[12px]"><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td>
                        <span class="role-pill"
                              style="background:<?php echo $role['bg'];?>;color:<?php echo $role['color'];?>;border-color:<?php echo $role['border'];?>">
                            <?php echo $role['label']; ?>
                        </span>
                    </td>
                    <td class="text-right">
                        <button class="icon-btn-sm"><i class="ph ph-dots-three-vertical text-sm"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        <span>Showing <?php echo count($users); ?> results</span>
    </div>
</div>

<script>
    function toggleAll(master) {
        document.querySelectorAll('.cb-row').forEach(cb => cb.checked = master.checked);
    }
    document.getElementById('user-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.data-table tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
