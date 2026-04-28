<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    header('Location: ../admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $user = db_fetch(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [$email]
        );

        if ($user && password_verify($password, $user['password'])) {
            loginAdmin($user['id']);
            header('Location: ../admin/dashboard.php');
            exit;
        } else {
            $error = 'Email atau password tidak valid.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — TEFA Canning SIP</title>
    <meta name="description" content="Halaman login admin panel TEFA Canning SIP Politeknik Negeri Jember.">

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
            <div class="flex flex-col items-center mb-7">
                <div class="flex items-center gap-3 mb-1">
                    <img src="../assets/images/politeknik_logo_red.png" alt="Logo TEFA" class="h-10 w-auto">
                    <div class="flex flex-col justify-center">
                        <span class="text-[17px] font-bold text-navy leading-tight">TEFA Canning SIP</span>
                        <span class="text-[12px] text-slate-400 leading-tight">Politeknik Negeri Jember</span>
                    </div>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-[22px] font-bold text-navy text-center mb-6">Sign in</h1>

            <!-- Error Alert -->
            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-[12px] rounded-lg px-4 py-3 mb-5 flex items-center gap-2">
                <i class="ph-fill ph-warning-circle text-red-500 text-[16px] shrink-0"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form id="admin-login-form" method="POST" action="">
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">
                        Email address<span class="text-primary ml-0.5">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400"
                            placeholder=""
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">
                        Password<span class="text-primary ml-0.5">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400 pr-[42px]"
                            placeholder=""
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors bg-transparent border-none cursor-pointer" onclick="togglePassword('password', this)" id="toggle-pwd-btn" aria-label="Tampilkan/Sembunyikan password">
                            <i class="ph ph-eye" id="eye-icon-password"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 accent-primary cursor-pointer">
                    <label for="remember" class="text-[13px] text-gray-600 cursor-pointer select-none">Remember me</label>
                </div>

                <!-- Submit -->
                <button type="submit" id="login-btn" class="w-full bg-primary text-white font-bold py-3 rounded-lg transition-all hover:bg-dark active:scale-[0.98]">
                    Sign in
                </button>
            </form>

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

        // Loading state on submit
        document.getElementById('admin-login-form').addEventListener('submit', function() {
            const btn = document.getElementById('login-btn');
            btn.textContent = 'Signing in...';
            btn.disabled = true;
            btn.style.opacity = '0.75';
        });
    </script>

</body>
</html>
