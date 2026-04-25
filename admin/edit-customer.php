<?php
/**
 * Admin Edit Customer Page
 */

$pageTitle   = 'Edit Customer';
$currentPage = 'customers';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

require_once __DIR__ . '/../classes/CustomerAdminService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$customerAdminService = new CustomerAdminService();
$activityLogService = new ActivityLogService();

// Validate ID parameter
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    setFlash('error', 'ID pelanggan tidak valid.');
    header('Location: customers.php');
    exit;
}

$customer = $customerAdminService->getById($id);
if (!$customer) {
    setFlash('error', 'Pelanggan tidak ditemukan.');
    header('Location: customers.php');
    exit;
}

$stats = $customerAdminService->getStats($id);

// ── POST Handlers ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash('error', 'Token CSRF tidak valid.');
        header('Location: edit-customer.php?id=' . $id);
        exit;
    }

    $action = $_GET['action'] ?? '';

    if ($action === 'delete') {
        $customerAdminService->softDelete($id);
        $activityLogService->log('deleted', 'App\Models\Customer', $id, 'deleted', ['old' => $customer]);
        setFlash('success', 'Pelanggan berhasil dihapus.');
        header('Location: customers.php');
        exit;
    }

    // Update customer
    $data = [
        'name'         => trim($_POST['name'] ?? ''),
        'organization' => trim($_POST['organization'] ?? '') ?: null,
        'phone'        => trim($_POST['phone'] ?? '') ?: null,
        'email'        => trim($_POST['email'] ?? '') ?: null,
        'address'      => trim($_POST['address'] ?? '') ?: null,
    ];

    if (empty($data['name'])) {
        setFlash('error', 'Nama pelanggan wajib diisi.');
        header('Location: edit-customer.php?id=' . $id);
        exit;
    }

    $customerAdminService->update($id, $data);
    $activityLogService->log('updated', 'App\Models\Customer', $id, 'updated', ['new' => $data, 'old' => $customer]);

    setFlash('success', 'Data pelanggan berhasil diperbarui.');
    header('Location: edit-customer.php?id=' . $id);
    exit;
}

// Build template-ready data
$customer = [
    'id'           => $customer['id'],
    'name'         => $customer['name'],
    'organization' => $customer['organization'] ?? '',
    'phone'        => $customer['phone'] ?? '',
    'email'        => $customer['email'] ?? '',
    'address'      => $customer['address'] ?? '',
    'stats'        => [
        'total_orders'      => $stats['total_orders'] . ' pesanan',
        'total_transactions' => FormatHelper::rupiah($stats['total_spent']),
        'joined_at'         => FormatHelper::tanggal($stats['joined_at'] ?? 'now'),
    ]
];

include __DIR__ . '/../includes/header-admin.php';
?>

<style>
    /* Reuse styles from edit-order.php */
    .card { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
    .card-title { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .card-subtitle { font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: -12px; margin-bottom: 20px; display: block; }
    
    .label { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .label .required { color: #E02424; margin-left: 2px; }
    
    .input { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #1e293b; background: #fff; transition: all 0.2s; outline: none; }
    .input:focus { border-color: #E02424; box-shadow: 0 0 0 3px rgba(224, 36, 36, 0.05); }
    .input:disabled { background: #f1f5f9; color: #64748b; cursor: not-allowed; }

    .btn-save { background: #E02424; color: #fff; font-size: 13px; font-weight: 700; padding: 10px 24px; border-radius: 8px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-save:hover { background: #9B1C1C; transform: translateY(-1px); }

    .btn-cancel { background: #fff; border: 1px solid #e2e8f0; color: #64748b; font-size: 13px; font-weight: 600; padding: 10px 20px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .btn-cancel:hover { background: #f8fafc; color: #1e293b; }

    .btn-delete-top { background: #E02424; color: #fff; font-size: 12px; font-weight: 700; padding: 8px 16px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-delete-top:hover { background: #9B1C1C; }

    .breadcrumb { font-size: 12px; color: #94a3b8; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .breadcrumb a { color: #94a3b8; text-decoration: none; }
    .breadcrumb a:hover { color: #E02424; }
    .breadcrumb .active { color: #475569; font-weight: 500; }

    .edit-grid { display: grid; grid-template-columns: 1fr 300px; gap: 24px; align-items: start; }
    
    .stat-item { margin-bottom: 16px; }
    .stat-label { font-size: 11px; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .stat-value { font-size: 13px; font-weight: 500; color: #1e293b; }
</style>

<!-- Breadcrumb & Header -->
<div class="flex items-center justify-between mb-2">
    <div>
        <div class="breadcrumb">
            <a href="customers.php">Pelanggan</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <a href="#"><?php echo $customer['name']; ?></a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="active">Edit</span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">Edit Customer</h1>
    </div>
    <button type="button" class="btn-delete-top" onclick="confirmDelete(<?php echo $id; ?>)">
        Delete
    </button>
</div>

<form action="edit-customer.php?id=<?php echo $id; ?>" method="POST" id="edit-customer-form">
    <?php echo csrfField(); ?>
    <input type="hidden" name="customer_id" value="<?php echo $id; ?>">

    <div class="edit-grid">
        <!-- Main Column (Left) -->
        <div class="main-content">
            <!-- Informasi Pelanggan -->
            <div class="card shadow-sm">
                <div class="card-title">
                    <i class="ph ph-user text-lg text-slate-400"></i>
                    Informasi Pelanggan
                </div>
                <span class="card-subtitle">Data identitas pelanggan</span>

                <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                    <div>
                        <label class="label">Nama Lengkap<span class="required">*</span></label>
                        <input type="text" name="name" class="input" value="<?php echo $customer['name']; ?>" required>
                    </div>
                    <div>
                        <label class="label">Organisasi / Instansi</label>
                        <input type="text" name="organization" class="input" value="<?php echo $customer['organization']; ?>">
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="card shadow-sm">
                <div class="card-title">
                    <i class="ph ph-phone text-lg text-slate-400"></i>
                    Kontak
                </div>
                <span class="card-subtitle">Informasi kontak untuk notifikasi</span>

                <div class="grid grid-cols-2 gap-x-6 gap-y-5 mb-5">
                    <div>
                        <label class="label">No. WhatsApp</label>
                        <input type="text" name="phone" class="input" value="<?php echo $customer['phone']; ?>">
                        <p class="text-[10px] text-slate-400 mt-1.5">Format: 628xxxxxxxxxx (tanpa + atau spasi)</p>
                    </div>
                    <div>
                        <label class="label">Email</label>
                        <input type="email" name="email" class="input" value="<?php echo $customer['email']; ?>">
                    </div>
                </div>
                
                <div>
                    <label class="label">Alamat</label>
                    <textarea name="address" class="input min-h-[100px] resize-none"><?php echo $customer['address']; ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center gap-3 mt-4">
                <button type="submit" class="btn-save shadow-sm shadow-red-100">Save changes</button>
                <a href="customers.php" class="btn-cancel">Cancel</a>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="sidebar-content">
            <!-- Statistik -->
            <div class="card shadow-sm p-5">
                <div class="font-bold text-[13px] text-slate-800 mb-4">Statistik</div>
                
                <div class="stat-item">
                    <div class="stat-label">Total Pesanan</div>
                    <div class="stat-value"><?php echo $customer['stats']['total_orders']; ?></div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Total Transaksi</div>
                    <div class="stat-value"><?php echo $customer['stats']['total_transactions']; ?></div>
                </div>
                
                <div class="stat-item mb-0">
                    <div class="stat-label">Terdaftar sejak</div>
                    <div class="stat-value"><?php echo $customer['stats']['joined_at']; ?></div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'edit-customer.php?action=delete&id=' + id;
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = document.querySelector('input[name="csrf_token"]')?.value || '';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
