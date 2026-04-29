<?php
/**
 * Admin Edit Customer Page
 */

$pageTitle   = 'Edit Customer';
$currentPage = 'customers';

require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();

require_once __DIR__ . '/../classes/CustomerAdminService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$customerAdminService = new CustomerAdminService();
$activityLogService = new ActivityLogService();

// Validate ID parameter
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    FlashMessage::set('error', 'ID pelanggan tidak valid.');
    header('Location: customers.php');
    exit;
}

$customer = $customerAdminService->getById($id);
if (!$customer) {
    FlashMessage::set('error', 'Pelanggan tidak ditemukan.');
    header('Location: customers.php');
    exit;
}

$stats = $customerAdminService->getStats($id);

// ── POST Handlers ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfService::verify()) {
        FlashMessage::set('error', 'Token CSRF tidak valid.');
        header('Location: edit-customer.php?id=' . $id);
        exit;
    }

    $action = $_GET['action'] ?? '';

    if ($action === 'delete') {
        $customerAdminService->softDelete($id);
        $activityLogService->log('deleted', 'App\Models\Customer', $id, 'deleted', ['old' => $customer]);
        FlashMessage::set('success', 'Pelanggan berhasil dihapus.');
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
        FlashMessage::set('error', 'Nama pelanggan wajib diisi.');
        header('Location: edit-customer.php?id=' . $id);
        exit;
    }

    $customerAdminService->updateById($id, $data);
    $activityLogService->log('updated', 'App\Models\Customer', $id, 'updated', ['new' => $data, 'old' => $customer]);

    FlashMessage::set('success', 'Data pelanggan berhasil diperbarui.');
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



<!-- Breadcrumb & Header -->
<div class="flex items-center justify-between mb-2">
    <div>
        <div class="flex items-center gap-2 text-[12px] text-gray-400 mb-3">
            <a href="customers.php" class="hover:text-primary transition-colors">Pelanggan</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <a href="#" class="hover:text-primary transition-colors"><?php echo $customer['name']; ?></a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="text-slate-600 font-medium">Edit</span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">Edit Customer</h1>
    </div>
    <button type="button" class="inline-flex items-center gap-1 bg-primary text-white text-[12px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark" onclick="confirmDelete(<?php echo $id; ?>)">
        Delete
    </button>
</div>

<form action="edit-customer.php?id=<?php echo $id; ?>" method="POST" id="edit-customer-form">
    <?php echo CsrfService::field(); ?>
    <input type="hidden" name="customer_id" value="<?php echo $id; ?>">

    <div class="grid grid-cols-[1fr_300px] gap-6 items-start">
        <!-- Main Column (Left) -->
        <div class="main-content">
            <!-- Informasi Pelanggan -->
            <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
                <div class="text-[14px] font-bold text-navy mb-1 flex items-center gap-2">
                    <i class="ph ph-user text-lg text-slate-400"></i>
                    Informasi Pelanggan
                </div>
                <span class="text-[11px] text-gray-400 font-medium block mb-5">Data identitas pelanggan</span>

                <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Lengkap<span class="text-primary ml-0.5">*</span></label>
                        <input type="text" name="name" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" value="<?php echo $customer['name']; ?>" required>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Organisasi / Instansi</label>
                        <input type="text" name="organization" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" value="<?php echo $customer['organization']; ?>">
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
                <div class="text-[14px] font-bold text-navy mb-1 flex items-center gap-2">
                    <i class="ph ph-phone text-lg text-slate-400"></i>
                    Kontak
                </div>
                <span class="text-[11px] text-gray-400 font-medium block mb-5">Informasi kontak untuk notifikasi</span>

                <div class="grid grid-cols-2 gap-x-6 gap-y-5 mb-5">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">No. WhatsApp</label>
                        <input type="text" name="phone" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" value="<?php echo $customer['phone']; ?>">
                        <p class="text-[10px] text-slate-400 mt-1.5">Format: 628xxxxxxxxxx (tanpa + atau spasi)</p>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Email</label>
                        <input type="email" name="email" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" value="<?php echo $customer['email']; ?>">
                    </div>
                </div>
                
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Alamat</label>
                    <textarea name="address" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none min-h-[100px] resize-none transition-all focus:border-primary"><?php echo $customer['address']; ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center gap-3 mt-4">
                <button type="submit" class="bg-primary text-white text-[13px] font-bold px-6 py-2.5 rounded-lg transition-colors hover:bg-dark shadow-sm shadow-red-100 border-none cursor-pointer">Save changes</button>
                <a href="customers.php" class="inline-flex items-center justify-center bg-white border border-gray-200 text-slate-500 text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors hover:bg-gray-50 hover:text-navy">Cancel</a>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="sidebar-content">
            <!-- Statistik -->
            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                <div class="font-bold text-[13px] text-slate-800 mb-4">Statistik</div>
                
                <div class="mb-4">
                    <div class="text-[11px] font-semibold text-slate-600 mb-1">Total Pesanan</div>
                    <div class="text-[13px] font-medium text-navy"><?php echo $customer['stats']['total_orders']; ?></div>
                </div>
                
                <div class="mb-4">
                    <div class="text-[11px] font-semibold text-slate-600 mb-1">Total Transaksi</div>
                    <div class="text-[13px] font-medium text-navy"><?php echo $customer['stats']['total_transactions']; ?></div>
                </div>
                
                <div>
                    <div class="text-[11px] font-semibold text-slate-600 mb-1">Terdaftar sejak</div>
                    <div class="text-[13px] font-medium text-navy"><?php echo $customer['stats']['joined_at']; ?></div>
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
