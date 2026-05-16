<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../classes/PasswordResetService.php';

$resetService = new PasswordResetService();

$error   = '';
$success = '';

// Get email from query param or POST
$email = trim($_GET['email'] ?? $_POST['email'] ?? '');

if (empty($email)) {
    header('Location: forgot-password.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token       = trim($_POST['token'] ?? '');
    $password    = $_POST['password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if (empty($token) || empty($password) || empty($confirmPass)) {
        $error = 'Semua field wajib diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirmPass) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        if ($resetService->resetPassword($email, $token, $password)) {
            $success = 'Password berhasil diubah! Silakan login dengan password baru Anda.';
        } else {
            $error = 'Kode OTP tidak valid atau sudah kadaluarsa. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — TEFA Canning SIP</title>
    <meta name="description" content="Halaman reset password akun pelanggan TEFA Canning SIP.">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: '#E02424',
                        accent:  '#F05252',
                        dark:    '#9B1C1C',
                        navy:    '#111827',
                    }
                }
            }
        }
    </script>

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4 bg-[#f8f9fb]">

    <div class="w-full max-w-[420px]">
        <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.06),0_4px_24px_rgba(0,0,0,0.06)] border border-black/[0.04] px-10 py-10">

            <!-- Logo -->
            <div class="flex flex-col items-center mb-6">
                <div class="flex items-center gap-3">
                    <img src="../assets/images/politeknik_logo_red.png" alt="Logo TEFA" class="h-10 w-auto">
                    <div class="flex flex-col justify-center">
                        <span class="text-[17px] font-bold text-navy leading-tight">TEFA Canning SIP</span>
                        <span class="text-[12px] text-slate-400 leading-tight">Politeknik Negeri Jember</span>
                    </div>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-[22px] font-bold text-navy text-center mb-2">Reset Password</h1>
            <p class="text-center text-[13px] text-gray-400 mb-1">
                Masukkan kode OTP yang dikirim via WhatsApp
            </p>
            <p class="text-center text-[12px] text-gray-500 mb-6">
                untuk <span class="font-semibold text-navy"><?php echo htmlspecialchars($email); ?></span>
            </p>

            <!-- Success Alert -->
            <?php if ($success): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 text-[12px] rounded-lg px-4 py-3 mb-5 flex items-start gap-2">
                <i class="ph-fill ph-check-circle text-green-600 text-[16px] shrink-0 mt-0.5"></i>
                <div>
                    <?php echo $success; ?>
                    <div class="mt-3">
                        <a href="login-customer.php" class="text-primary font-semibold hover:text-dark transition-colors">
                            <i class="ph ph-arrow-left text-[12px]"></i> Kembali ke Login
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Error Alert -->
            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-[12px] rounded-lg px-4 py-3 mb-5 flex items-center gap-2">
                <i class="ph-fill ph-warning-circle text-red-500 text-[16px] shrink-0"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- Form (only show when not success) -->
            <?php if (!$success): ?>
            <form id="reset-form" method="POST" action="">

                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                <!-- OTP Code -->
                <div class="mb-4">
                    <label for="token" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">
                        Kode OTP<span class="text-primary ml-0.5">*</span>
                    </label>
                    <input
                        type="text"
                        id="token"
                        name="token"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400 text-center tracking-[0.3em] font-mono text-lg font-bold"
                        placeholder="000000"
                        maxlength="6"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        required
                        autocomplete="one-time-code"
                    >
                </div>

                <!-- New Password -->
                <div class="mb-4">
                    <label for="password" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">
                        Password Baru<span class="text-primary ml-0.5">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400 pr-[42px]"
                            placeholder="Min. 6 karakter"
                            minlength="6"
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors bg-transparent border-none cursor-pointer" onclick="togglePassword('password', this)" aria-label="Tampilkan/Sembunyikan password">
                            <i class="ph ph-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="confirm_password" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">
                        Konfirmasi Password<span class="text-primary ml-0.5">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400 pr-[42px]"
                            placeholder="Ulangi password baru"
                            minlength="6"
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors bg-transparent border-none cursor-pointer" onclick="togglePassword('confirm_password', this)" aria-label="Tampilkan/Sembunyikan password">
                            <i class="ph ph-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" id="reset-btn" class="w-full bg-primary text-white font-bold py-3 rounded-lg transition-all hover:bg-dark active:scale-[0.98]">
                    Reset Password
                </button>

                <!-- Back link -->
                <div class="mt-4 text-center">
                    <a href="forgot-password.php" class="text-[12px] text-gray-400 hover:text-primary transition-colors">
                        <i class="ph ph-arrow-left text-[12px]"></i> Kirim ulang kode OTP
                    </a>
                </div>

                <!-- Help: contact admin if OTP not received -->
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <p class="text-[11px] text-gray-400 text-center mb-2">Tidak menerima kode OTP?</p>
                    <p class="text-[11px] text-gray-400 text-center mb-3">Nomor WhatsApp mungkin salah. Hubungi admin untuk reset password.</p>
                    <?php
                    $ownerPhone = preg_replace('/[^0-9]/', '', $_ENV['FONNTE_OWNER_PHONE'] ?? '');
                    if (!empty($ownerPhone)):
                    ?>
                    <a href="https://wa.me/<?php echo $ownerPhone; ?>?text=<?php echo urlencode('Halo admin, saya lupa password dan tidak bisa menerima OTP karena nomor WhatsApp salah/belum terdaftar. Mohon bantuan reset password. Email saya: '); ?>" target="_blank" class="flex items-center justify-center gap-2 w-full border border-green-300 text-green-700 bg-green-50 font-semibold py-2.5 rounded-lg transition-all hover:bg-green-100 text-[12px]">
                        <i class="ph ph-whatsapp-logo text-[16px]"></i>
                        Hubungi Admin via WhatsApp
                    </a>
                    <?php else: ?>
                    <p class="text-[11px] text-gray-500 text-center">Hubungi admin sistem untuk bantuan reset password.</p>
                    <?php endif; ?>
                </div>
            </form>
            <?php endif; ?>

        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'ph ph-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'ph ph-eye';
            }
        }

        // Auto-focus OTP input
        const tokenInput = document.getElementById('token');
        if (tokenInput) {
            tokenInput.focus();
        }

        // Loading state on submit
        const form = document.getElementById('reset-form');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = document.getElementById('reset-btn');
                btn.textContent = 'Memproses...';
                btn.disabled = true;
                btn.style.opacity = '0.75';
            });
        }
    </script>

</body>
</html>
