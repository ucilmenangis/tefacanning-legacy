<?php
/**
 * Admin Edit Customer Page
 * UI Prototype for TEFA Canning Admin Panel
 */

$pageTitle   = 'Edit Customer';
$currentPage = 'customers';

require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

include __DIR__ . '/../includes/header-admin.php';

// Mock Data
$customer = [
    'id' => 1,
    'name' => 'Customer',
    'organization' => 'customer_organization',
    'phone' => '08123456789',
    'email' => 'customer@customer.com',
    'address' => 'alamat testing',
    'stats' => [
        'total_orders' => '2 pesanan',
        'total_transactions' => 'Rp 10.000.000',
        'joined_at' => '15 Feb 2026'
    ]
];
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
    <button type="button" class="btn-delete-top" onclick="confirmDelete(1)">
        Delete
    </button>
</div>

<form action="update-customer.php" method="POST" id="edit-customer-form">
    <?php if (function_exists('csrfField')) echo csrfField(); ?>
    <input type="hidden" name="customer_id" value="1">

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
            window.location.href = 'delete-customer.php?id=' + id;
        }
    }
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
