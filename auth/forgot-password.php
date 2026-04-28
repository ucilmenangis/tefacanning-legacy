<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $customer = db_fetch(
            "SELECT id, name, email FROM customers WHERE email = ? AND deleted_at IS NULL LIMIT 1",
            [$email]
        );

        // Always show success to prevent email enumeration
        $success = 'Jika email terdaftar, kami telah mengirimkan instruksi reset password ke ' . htmlspecialchars($email) . '.';

        // TODO: integrate with Fonnte / email service to send reset link
        // if ($customer) { ... send reset email ... }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — TEFA Canning SIP</title>
    <meta name="description" content="Reset password akun pelanggan TEFA Canning SIP Politeknik Negeri Jember.">

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
            <h1 class="text-[22px] font-bold text-navy text-center mb-2">Forgot password?</h1>
            <div class="text-center mb-6">
                <a href="login-customer.php" class="inline-flex items-center gap-2 text-gray-400 hover:text-primary transition-colors text-[13px]">
                    <i class="ph ph-arrow-left text-[13px]"></i>
                    back to login
                </a>
            </div>

            <!-- Alerts -->
            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-[12px] rounded-lg px-4 py-3 mb-5 flex items-center gap-2">
                <i class="ph-fill ph-warning-circle text-red-500 text-[16px] shrink-0"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 text-[12px] rounded-lg px-4 py-3 mb-5 flex items-center gap-2">
                <i class="ph-fill ph-check-circle text-green-600 text-[16px] shrink-0"></i>
                <?php echo $success; ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <?php if (!$success): ?>
            <form id="forgot-form" method="POST" action="">

                <!-- Email -->
                <div class="mb-5">
                    <label for="email" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">
                        Email address<span class="text-primary ml-0.5">*</span>
                    </label>
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

                <!-- Submit -->
                <button type="submit" id="send-btn" class="w-full bg-primary text-white font-bold py-3 rounded-lg transition-all hover:bg-dark active:scale-[0.98]">
                    Send email
                </button>
            </form>
            <?php endif; ?>

        </div>
    </div>

    <script>
        const form = document.getElementById('forgot-form');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = document.getElementById('send-btn');
                btn.textContent = 'Mengirim...';
                btn.disabled = true;
                btn.style.opacity = '0.75';
            });
        }
    </script>

</body>
</html>
