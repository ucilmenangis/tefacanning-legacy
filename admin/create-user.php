<?php
/**
 * Admin Create User Page
 */

$pageTitle = 'Tambah Pengguna';
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
        header('Location: create-user.php');
        exit;
    }

    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'role' => $_POST['role'] ?? ''
    ];

    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        FlashMessage::set('error', 'Semua field wajib diisi.');
        header('Location: create-user.php');
        exit;
    }

    $newId = $adminService->createUser($data);
    $activityLogService->log('created', 'App\Models\User', $newId, 'created new admin user: ' . $data['name']);

    FlashMessage::set('success', 'Pengguna berhasil dibuat.');
    header('Location: pengaturan.php');
    exit;
}

include __DIR__ . '/../includes/header-admin.php';
?>

<!-- Breadcrumb & Header -->
<div class="mb-5">
    <div class="flex items-center gap-2 text-[12px] text-gray-400 mb-2">
        <a href="pengaturan.php" class="hover:text-primary transition-colors">Pengguna</a>
        <i class="ph ph-caret-right text-[10px]"></i>
        <span class="text-slate-600 font-medium">Tambah</span>
    </div>
    <h1 class="text-[24px] font-extrabold text-navy">Tambah Pengguna</h1>
</div>

<form action="create-user.php" method="POST" id="create-user-form">
    <?php echo CsrfService::field(); ?>

    <div class="grid grid-cols-[1fr_300px] gap-6 items-start">
        <!-- Main Column (Left) -->
        <div class="main-content">
            <!-- Informasi Akun -->
            <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
                <div class="text-[14px] font-bold text-navy mb-1 flex items-center gap-2">
                    <i class="ph ph-user-plus text-lg text-slate-400"></i>
                    Informasi Akun
                </div>
                <span class="text-[11px] text-gray-400 font-medium block mb-5">Data akun pengguna admin panel
                    baru</span>

                <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                    <div class="col-span-2">
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Lengkap<span
                                class="text-primary ml-0.5">*</span></label>
                        <input type="text" name="name"
                            class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary"
                            placeholder="Contoh: Super Admin" required>
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Email<span
                                class="text-primary ml-0.5">*</span></label>
                        <input type="email" name="email"
                            class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary"
                            placeholder="email@example.com" required>
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">No. WhatsApp<span
                                class="text-primary ml-0.5">*</span></label>
                        <input type="text" name="phone"
                            class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary"
                            placeholder="628xxxxxxxxxx" required>
                        <p class="text-[10px] text-slate-400 mt-1.5">Format: 628xxxxxxxxxx (untuk notifikasi Fonnte)</p>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Password<span
                                class="text-primary ml-0.5">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="user-password"
                                class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary"
                                placeholder="Password minimal 8 karakter" required>
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="ph ph-eye text-lg" id="password-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center gap-3 mt-4">
                <button type="submit"
                    class="bg-primary text-white text-[13px] font-bold px-6 py-2.5 rounded-lg transition-colors hover:bg-dark shadow-sm shadow-red-100 border-none cursor-pointer">Create
                    User</button>
                <a href="pengaturan.php"
                    class="inline-flex items-center justify-center bg-white border border-gray-200 text-slate-500 text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors hover:bg-gray-50 hover:text-navy">Cancel</a>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="sidebar-content">
            <!-- Role Selection -->
            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                <div class="font-bold text-[13px] text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ph ph-shield-check text-lg text-slate-400"></i>
                    Role & Akses
                </div>

                <div class="space-y-4">
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Pilih
                            Role</label>
                        <div class="relative">
                            <select name="role"
                                class="w-full border border-gray-200 rounded-lg py-2 px-3 text-[12.5px] text-navy bg-white outline-none appearance-none cursor-pointer transition-all focus:border-primary"
                                required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['name']; ?>"><?php echo $role['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <i
                                class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="text-[11px] text-slate-500 leading-relaxed">
                            <i class="ph ph-info text-sm mr-1 inline-block align-middle"></i>
                            Role menentukan hak akses pengguna di panel admin ini.
                        </div>
                    </div>
                </div>
            </div>
        </div>
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