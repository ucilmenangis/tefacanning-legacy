<?php
/**
 * Admin Create User Page (using create-customer.php file)
 */

$pageTitle   = 'Create Pengguna';
$currentPage = 'users';

require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();
Auth::admin()->requireSuperAdmin();

require_once __DIR__ . '/../classes/AdminService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';
require_once __DIR__ . '/../classes/CsrfService.php';
require_once __DIR__ . '/../classes/FlashMessage.php';

$adminService = new AdminService();
$activityLogService = new ActivityLogService();

$roles = $adminService->getRoles();

// ── POST Handlers ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfService::verify()) {
        FlashMessage::set('error', 'Token CSRF tidak valid.');
        header('Location: create-customer.php');
        exit;
    }

    $data = [
        'name'     => trim($_POST['name'] ?? ''),
        'email'    => trim($_POST['email'] ?? ''),
        'phone'    => trim($_POST['phone'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'role'     => $_POST['role'] ?? ''
    ];

    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        FlashMessage::set('error', 'Semua field wajib diisi.');
        header('Location: create-customer.php');
        exit;
    }

    $newId = $adminService->createUser($data);
    $activityLogService->log('created', 'App\Models\User', $newId, 'created new admin user via create-customer: ' . $data['name']);

    FlashMessage::set('success', 'Pengguna berhasil dibuat.');

    // Check which button was clicked
    if (isset($_POST['create_another'])) {
        header('Location: create-customer.php');
    } else {
        header('Location: pengaturan.php');
    }
    exit;
}

include __DIR__ . '/../includes/header-admin.php';
?>

<!-- Breadcrumb & Header -->
<div class="mb-5">
    <div class="flex items-center gap-2 text-[12px] text-gray-400 mb-2">
        <span class="text-gray-400">Pengguna</span>
        <i class="ph ph-caret-right text-[10px]"></i>
        <span class="text-slate-600 font-medium">Create</span>
    </div>
    <h1 class="text-[24px] font-extrabold text-navy">Create Pengguna</h1>
</div>

<form action="create-customer.php" method="POST" id="create-user-form">
    <?php echo CsrfService::field(); ?>

    <!-- Main Content Card -->
    <div class="bg-white border border-gray-100 rounded-xl p-8 mb-6 shadow-sm">
        <div class="text-[14px] font-bold text-navy mb-1 flex items-center gap-2">
            <i class="ph ph-user text-xl text-slate-300"></i>
            Informasi Pengguna
        </div>
        <span class="text-[11px] text-gray-400 font-medium block mb-8">Data akun pengguna admin panel</span>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <!-- Nama Lengkap -->
            <div class="space-y-1.5">
                <label class="block text-[12px] font-semibold text-slate-700">Nama Lengkap<span class="text-primary ml-0.5">*</span></label>
                <input type="text" name="name" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" placeholder="Masukkan nama lengkap" required>
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label class="block text-[12px] font-semibold text-slate-700">Email<span class="text-primary ml-0.5">*</span></label>
                <input type="email" name="email" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" placeholder="contoh@polije.ac.id" required>
            </div>

            <!-- No WhatsApp -->
            <div class="space-y-1.5">
                <label class="block text-[12px] font-semibold text-slate-700">No. WhatsApp</label>
                <input type="text" name="phone" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" placeholder="628xxxxxxxxxx">
                <p class="text-[10px] text-slate-400 mt-1">Format: 628xxxxxxxxxx (untuk notifikasi Fonnte)</p>
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label class="block text-[12px] font-semibold text-slate-700">Password<span class="text-primary ml-0.5">*</span></label>
                <div class="relative">
                    <input type="password" name="password" id="user-password" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" placeholder="Masukkan password" required>
                    <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="ph ph-eye text-lg" id="password-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Role -->
            <div class="space-y-1.5 md:col-span-1">
                <label class="block text-[12px] font-semibold text-slate-700">Role<span class="text-primary ml-0.5">*</span></label>
                <div class="relative">
                    <select name="role" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none appearance-none cursor-pointer transition-all focus:border-primary" required>
                        <option value="" disabled selected>Select an option</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['name']; ?>"><?php echo $role['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Pilih role untuk pengguna ini</p>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-2.5">
        <button type="submit" name="create" class="bg-primary text-white text-[13px] font-bold px-5 py-2.5 rounded-lg transition-colors hover:bg-dark border-none cursor-pointer shadow-sm">Create</button>
        <button type="submit" name="create_another" class="bg-white border border-gray-200 text-slate-700 text-[13px] font-bold px-5 py-2.5 rounded-lg transition-colors hover:bg-gray-50 cursor-pointer shadow-sm">Create & create another</button>
        <a href="pengaturan.php" class="inline-flex items-center justify-center bg-white border border-gray-200 text-slate-500 text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors hover:bg-gray-50 hover:text-navy shadow-sm">Cancel</a>
    </div>
</form>

<script>
    function togglePassword() {
        const input = document.getElementById('user-password');
        const icon = document.getElementById('password-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('ph-eye');
            icon.classList.add('ph-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('ph-eye-slash');
            icon.classList.add('ph-eye');
        }
    }
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
