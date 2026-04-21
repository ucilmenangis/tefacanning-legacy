<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/CustomerService.php';
requireCustomer();

$customerId = getCustomerId();
$service = new CustomerService();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'profile') {
    if (!verifyCsrf()) {
        setFlash('error', 'Token keamanan tidak valid.');
        header('Location: profile.php');
        exit;
    }

    $name         = trim($_POST['name'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $organization = trim($_POST['organization'] ?? '');
    $address      = trim($_POST['address'] ?? '');

    if (empty($name)) {
        setFlash('error', 'Nama wajib diisi.');
    } elseif ($service->hasActiveOrders($customerId)) {
        setFlash('error', 'Profil tidak dapat diubah karena ada pesanan yang sedang diproses.');
    } else {
        $service->updateProfile($customerId, [
            'name'         => $name,
            'phone'        => $phone,
            'organization' => $organization,
            'address'      => $address,
        ]);
        setFlash('success', 'Profil berhasil diperbarui.');
    }
    header('Location: profile.php');
    exit;
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'password') {
    if (!verifyCsrf()) {
        setFlash('error', 'Token keamanan tidak valid.');
        header('Location: profile.php');
        exit;
    }

    $current        = $_POST['current_password'] ?? '';
    $new            = $_POST['new_password'] ?? '';
    $newConfirm     = $_POST['new_password_confirm'] ?? '';

    if (empty($current) || empty($new)) {
        setFlash('error', 'Semua field password wajib diisi.');
    } elseif (strlen($new) < 8) {
        setFlash('error', 'Password baru minimal 8 karakter.');
    } elseif ($new !== $newConfirm) {
        setFlash('error', 'Konfirmasi password tidak cocok.');
    } elseif (!$service->changePassword($customerId, $current, $new)) {
        setFlash('error', 'Password saat ini salah.');
    } else {
        setFlash('success', 'Password berhasil diubah.');
    }
    header('Location: profile.php');
    exit;
}

$customer = $service->getById($customerId);
$hasActive = $service->hasActiveOrders($customerId);

$pageTitle = 'Edit Profil';
$currentPage = 'profile';
include __DIR__ . '/../includes/header-customer.php';
?>

<style>
    .form-card {
        background: white; border-radius: 12px; border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,.04); margin-bottom: 24px;
    }
    .input-group { margin-bottom: 16px; }
    .label-text {
        display: block; font-size: 13px; font-weight: 600;
        color: #1a202c; margin-bottom: 6px;
    }
    .input-box {
        width: 100%; background: white; border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 10px 12px 10px 42px; font-size: 13px; color: #374151;
        transition: border-color 0.15s, box-shadow 0.15s; outline: none;
        font-family: inherit;
    }
    .input-box:focus {
        border-color: #E02424; box-shadow: 0 0 0 3px rgba(224,36,36,.08);
    }
    .input-box:disabled {
        background: #f8fafc; color: #94a3b8; cursor: not-allowed;
    }
    .input-box.no-icon { padding-left: 12px; }
    .alert-box {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 16px 20px; display: flex; gap: 16px; margin-bottom: 24px;
    }
    .btn-submit {
        background: white; border: 1px solid #e2e8f0; color: #1a202c;
        padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.15s; box-shadow: 0 1px 2px rgba(0,0,0,.05);
        cursor: pointer; font-family: inherit;
    }
    .btn-submit:hover { background: #f9fafb; border-color: #cbd5e1; }
</style>

<?php echo renderFlash(); ?>

<h1 class="text-[22px] font-bold text-navy mb-6">Edit Profil</h1>

<?php if ($hasActive): ?>
<!-- Active order lock warning -->
<div class="alert-box">
    <div class="text-navy">
        <i class="ph-bold ph-warning-circle text-xl"></i>
    </div>
    <div>
        <p class="text-[14px] font-bold text-navy mb-1">Profil Tidak Dapat Diubah</p>
        <p class="text-[12px] text-gray-500 mb-2">
            Anda memiliki pesanan yang sedang diproses. Data profil tidak dapat diubah
            untuk menjaga konsistensi data pesanan. Hubungi admin jika perlu mengubah data.
        </p>
    </div>
</div>
<?php endif; ?>

<!-- Informasi Pribadi -->
<div class="form-card overflow-hidden">
    <form method="POST" action="">
        <?php echo csrfField(); ?>
        <input type="hidden" name="action" value="profile">

        <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2.5 mb-2">
            <i class="ph ph-user text-gray-400 text-lg"></i>
            <div>
                <p class="text-[13px] font-semibold text-navy leading-none">Informasi Pribadi</p>
                <?php if ($hasActive): ?>
                <p class="text-[11px] text-[#d97706] font-medium mt-1 inline-flex items-center gap-1">
                    <i class="ph-fill ph-warning-circle"></i>
                    Anda memiliki pesanan yang sedang diproses. Hubungi admin untuk mengubah data profil.
                </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="p-5 space-y-4">
            <div class="input-group">
                <label class="label-text">Nama Lengkap</label>
                <div class="relative">
                    <i class="ph ph-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="text" name="name" class="input-box"
                           value="<?php echo htmlspecialchars($customer['name']); ?>"
                           <?php echo $hasActive ? 'disabled' : ''; ?> required>
                </div>
            </div>

            <div class="input-group">
                <label class="label-text">Email</label>
                <div class="relative">
                    <i class="ph ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="email" class="input-box"
                           value="<?php echo htmlspecialchars($customer['email']); ?>" disabled>
                </div>
            </div>

            <div class="input-group">
                <label class="label-text">No. Telepon</label>
                <div class="relative">
                    <i class="ph ph-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="text" name="phone" class="input-box"
                           value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>"
                           <?php echo $hasActive ? 'disabled' : ''; ?>>
                </div>
            </div>

            <div class="input-group">
                <label class="label-text">Organisasi / Instansi</label>
                <div class="relative">
                    <i class="ph ph-buildings absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="text" name="organization" class="input-box"
                           value="<?php echo htmlspecialchars($customer['organization'] ?? ''); ?>"
                           <?php echo $hasActive ? 'disabled' : ''; ?>>
                </div>
            </div>

            <div class="input-group">
                <label class="label-text">Alamat</label>
                <textarea name="address" rows="3"
                          class="w-full border border-e2e8f0 rounded-lg p-3 text-[13px] outline-none focus:border-[#E02424] focus:shadow-[0_0_0_3px_rgba(224,36,36,.08)]"
                          <?php echo $hasActive ? 'disabled' : ''; ?>
                          style="font-family:inherit; resize:vertical;"><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea>
            </div>
        </div>

        <?php if (!$hasActive): ?>
        <div class="px-5 pb-5 flex justify-end">
            <button type="submit" class="btn-submit">
                <i class="ph ph-floppy-disk text-base"></i>
                Simpan Perubahan
            </button>
        </div>
        <?php endif; ?>
    </form>
</div>

<!-- Ubah Password -->
<div class="form-card">
    <form method="POST" action="">
        <?php echo csrfField(); ?>
        <input type="hidden" name="action" value="password">

        <div class="px-5 py-4 border-b border-gray-50 mb-2 flex items-center gap-2.5">
            <i class="ph ph-lock-key text-gray-400 text-lg"></i>
            <div>
                <p class="text-[13px] font-semibold text-navy leading-none">Ubah Password</p>
                <p class="text-[11px] text-gray-400 mt-1">Pastikan password baru minimal 8 karakter.</p>
            </div>
        </div>

        <div class="p-5 space-y-4">
            <div class="input-group">
                <label class="label-text">Password Saat Ini <span class="text-[#E02424]">*</span></label>
                <div class="relative">
                    <i class="ph ph-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="password" name="current_password" class="input-box" placeholder="••••••••" required>
                </div>
            </div>

            <div class="input-group">
                <label class="label-text">Password Baru <span class="text-[#E02424]">*</span></label>
                <div class="relative">
                    <i class="ph ph-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="password" name="new_password" class="input-box" placeholder="••••••••" required minlength="8">
                </div>
            </div>

            <div class="input-group">
                <label class="label-text">Konfirmasi Password Baru <span class="text-[#E02424]">*</span></label>
                <div class="relative">
                    <i class="ph ph-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="password" name="new_password_confirm" class="input-box" placeholder="••••••••" required minlength="8">
                </div>
            </div>
        </div>

        <div class="px-5 pb-5 flex justify-end">
            <button type="submit" class="btn-submit">
                <i class="ph ph-lock-key text-base"></i>
                Ubah Password
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer-customer.php'; ?>
