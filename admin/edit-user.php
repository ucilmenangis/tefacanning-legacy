<?php
/**
 * Admin Edit User Page
 */

$pageTitle   = 'Edit Pengguna';
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

// Validate ID parameter
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    FlashMessage::set('error', 'ID pengguna tidak valid.');
    header('Location: pengaturan.php');
    exit;
}

$user = $adminService->getById($id);
if (!$user) {
    FlashMessage::set('error', 'Pengguna tidak ditemukan.');
    header('Location: pengaturan.php');
    exit;
}

// Get user role
$userRole = $adminService->getRole($id);
$user['role'] = $userRole;

$roles = $adminService->getRoles();

// ── POST Handlers ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfService::verify()) {
        FlashMessage::set('error', 'Token CSRF tidak valid.');
        header('Location: edit-user.php?id=' . $id);
        exit;
    }

    $action = $_GET['action'] ?? '';

    if ($action === 'delete') {
        if ($adminService->deleteUser($id)) {
            $activityLogService->log('deleted', 'App\Models\User', $id, 'deleted admin user: ' . $user['name']);
            FlashMessage::set('success', 'Pengguna berhasil dihapus.');
            header('Location: pengaturan.php');
        } else {
            FlashMessage::set('error', 'Gagal menghapus pengguna (Anda tidak bisa menghapus diri sendiri).');
            header('Location: edit-user.php?id=' . $id);
        }
        exit;
    }

    // Update user
    $data = [
        'name'     => trim($_POST['name'] ?? ''),
        'email'    => trim($_POST['email'] ?? ''),
        'phone'    => trim($_POST['phone'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'role'     => $_POST['role'] ?? ''
    ];

    if (empty($data['name']) || empty($data['email'])) {
        FlashMessage::set('error', 'Nama dan Email wajib diisi.');
        header('Location: edit-user.php?id=' . $id);
        exit;
    }

    $adminService->updateUser($id, $data);
    $activityLogService->log('updated', 'App\Models\User', $id, 'updated admin user: ' . $data['name']);

    FlashMessage::set('success', 'Data pengguna berhasil diperbarui.');
    header('Location: edit-user.php?id=' . $id);
    exit;
}

include __DIR__ . '/../includes/header-admin.php';
?>

<!-- Breadcrumb & Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="flex items-center gap-2 text-[12px] text-gray-400 mb-3">
            <a href="pengaturan.php" class="hover:text-primary transition-colors text-[11px]">Pengguna</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="text-slate-400 text-[11px]"><?php echo htmlspecialchars($user['name']); ?></span>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="text-slate-700 font-medium text-[11px]">Edit</span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">Edit <?php echo $user['role'] === 'super_admin' ? 'Super Admin' : 'Pengguna'; ?></h1>
    </div>
    <button type="button" class="inline-flex items-center gap-2 bg-primary text-white text-[12px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark shadow-lg shadow-primary/20" onclick="confirmDelete(<?php echo $id; ?>)">
        <i class="ph ph-trash text-sm"></i> Delete
    </button>
</div>

<form action="edit-user.php?id=<?php echo $id; ?>" method="POST" id="edit-user-form">
    <?php echo CsrfService::field(); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <!-- Main Content Card -->
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden mb-6">
        <!-- Card Header -->
        <div class="px-8 py-5 border-b border-gray-50 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-slate-400 border border-gray-100">
                <i class="ph ph-user text-xl"></i>
            </div>
            <div>
                <h3 class="text-[15px] font-bold text-slate-800">Informasi Pengguna</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Data akun pengguna admin panel</p>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- Nama Lengkap -->
                <div class="space-y-2">
                    <label class="text-[13px] font-bold text-slate-800">Nama Lengkap<span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50/30 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/5 outline-none transition-all text-[14px]"
                        value="<?php echo htmlspecialchars($user['name']); ?>"
                        placeholder="Super Admin">
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="text-[13px] font-bold text-slate-800">Email<span class="text-red-500">*</span></label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50/30 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/5 outline-none transition-all text-[14px]"
                        value="<?php echo htmlspecialchars($user['email']); ?>"
                        placeholder="superadmin@tefa.polije.ac.id">
                </div>

                <!-- No WhatsApp -->
                <div class="space-y-2">
                    <label class="text-[13px] font-bold text-slate-800">No. WhatsApp<span class="text-red-500">*</span></label>
                    <input type="text" name="phone" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50/30 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/5 outline-none transition-all text-[14px]"
                        value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                        placeholder="628xxxxxxxxxx">
                    <p class="text-[12px] text-slate-400">Format: 628xxxxxxxxxx (untuk notifikasi Fonnte)</p>
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="text-[13px] font-bold text-slate-800">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="user-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50/30 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/5 outline-none transition-all text-[14px]"
                            placeholder="Kosongkan jika tidak ingin mengubah">
                        <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="ph ph-eye text-xl" id="password-icon"></i>
                        </button>
                    </div>
                    <p class="text-[12px] text-slate-400">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <!-- Role -->
                <div class="space-y-2">
                    <label class="text-[13px] font-bold text-slate-800">Role<span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="role" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50/30 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/5 outline-none transition-all text-[14px] appearance-none cursor-pointer">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['name']; ?>" <?php echo $user['role'] === $role['name'] ? 'selected' : ''; ?>>
                                    <?php echo $role['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    </div>
                    <p class="text-[12px] text-slate-400">Pilih role untuk pengguna ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-3">
        <button type="submit" class="px-8 py-3 bg-primary text-white text-[14px] font-bold rounded-xl hover:bg-dark transition-all shadow-lg shadow-primary/20 active:scale-95">
            Save changes
        </button>
        <a href="pengaturan.php" class="px-8 py-3 bg-white border border-gray-200 text-slate-600 text-[14px] font-bold rounded-xl hover:bg-gray-50 transition-all active:scale-95 flex items-center justify-center">
            Cancel
        </a>
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

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'edit-user.php?action=delete&id=' + id;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = '<?php echo CsrfService::generate(); ?>';
            
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
