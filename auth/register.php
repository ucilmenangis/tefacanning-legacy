<?php
require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in
if (Auth::customer()->isLoggedIn()) {
    header('Location: ../customer/dashboard.php');
    exit;
}

$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $organization = trim($_POST['organization'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm      = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($password)) {
        $error = 'Semua field yang bertanda * wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        // Check duplicate email
        $existing = Database::getInstance()->fetch(
            "SELECT id FROM customers WHERE email = ? AND deleted_at IS NULL LIMIT 1",
            [$email]
        );

        if ($existing) {
            $error = 'Email sudah terdaftar. Silakan gunakan email lain atau masuk.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            $newId = Database::getInstance()->insert(
                "INSERT INTO customers (name, email, password, phone, organization, address, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())",
                [$name, $email, $hashed, $phone, $organization, $address]
            );

            if ($newId) {
                Auth::customer()->login($newId);
                header('Location: ../customer/dashboard.php');
                exit;
            } else {
                $error = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
            }
        }
    }
}

// Preserve form data on error
$old = [
    'name'         => htmlspecialchars($_POST['name'] ?? ''),
    'email'        => htmlspecialchars($_POST['email'] ?? ''),
    'phone'        => htmlspecialchars($_POST['phone'] ?? ''),
    'organization' => htmlspecialchars($_POST['organization'] ?? ''),
    'address'      => htmlspecialchars($_POST['address'] ?? ''),
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — TEFA Canning SIP</title>
    <meta name="description" content="Daftarkan akun pelanggan Anda untuk melakukan pre-order sarden kaleng TEFA Canning SIP.">

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
<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4 py-10 bg-[#f8f9fb]">

    <div class="w-full max-w-[420px]">
        <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.06),0_4px_24px_rgba(0,0,0,0.06)] border border-black/[0.04] px-10 py-10">

            <!-- Logo -->
            <div class="flex flex-col items-center mb-5">
                <div class="flex items-center gap-3">
                    <img src="../assets/images/politeknik_logo_red.png" alt="Logo TEFA" class="h-10 w-auto">
                    <div class="flex flex-col justify-center">
                        <span class="text-[17px] font-bold text-navy leading-tight">TEFA Canning SIP</span>
                        <span class="text-[12px] text-slate-400 leading-tight">Politeknik Negeri Jember</span>
                    </div>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-[22px] font-bold text-navy text-center mb-1">Sign up</h1>
            <p class="text-center text-[13px] text-gray-400 mb-6">
                or <a href="login-customer.php" class="text-primary font-semibold hover:text-dark transition-colors">sign in to your account</a>
            </p>

            <!-- Error Alert -->
            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-[12px] rounded-lg px-4 py-3 mb-5 flex items-center gap-2">
                <i class="ph-fill ph-warning-circle text-red-500 text-[16px] shrink-0"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form id="register-form" method="POST" action="">

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">
                        Name<span class="text-primary ml-0.5">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400"
                        value="<?= $old['name'] ?>"
                        required
                        autocomplete="name"
                    >
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">
                        Email address<span class="text-primary ml-0.5">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400"
                        value="<?= $old['email'] ?>"
                        required
                        autocomplete="email"
                    >
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label for="phone" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">
                        No. Telepon<span class="text-primary ml-0.5">*</span>
                    </label>
                    <div class="relative">
                        <i class="ph ph-phone-call absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none"></i>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400 pl-[38px]"
                            placeholder="08xxxxxxxxxx"
                            value="<?= $old['phone'] ?>"
                            required
                            autocomplete="tel"
                        >
                    </div>
                </div>

                <!-- Organization -->
                <div class="mb-4">
                    <label for="organization" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">Organisasi / Instansi</label>
                    <div class="relative">
                        <i class="ph ph-buildings absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none"></i>
                        <input
                            type="text"
                            id="organization"
                            name="organization"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400 pl-[38px]"
                            placeholder="Nama organisasi atau instansi Anda"
                            value="<?= $old['organization'] ?>"
                            autocomplete="organization"
                        >
                    </div>
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label for="address" class="text-[12px] font-semibold text-gray-500 mb-1.5 block">
                        Alamat<span class="text-primary ml-0.5">*</span>
                    </label>
                    <textarea
                        id="address"
                        name="address"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400 min-h-[100px] resize-none"
                        placeholder="Alamat lengkap Anda"
                        required
                    ><?= $old['address'] ?></textarea>
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
                        Confirm password<span class="text-primary ml-0.5">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white placeholder:text-gray-400 pr-[42px]"
                            placeholder=""
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors bg-transparent border-none cursor-pointer" onclick="togglePassword('confirm_password', this)" aria-label="Tampilkan/Sembunyikan konfirmasi password">
                            <i class="ph ph-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" id="register-btn" class="w-full bg-primary text-white font-bold py-3 rounded-lg transition-all hover:bg-dark active:scale-[0.98]">
                    Sign up
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

        // Client-side password match validation
        document.getElementById('register-form').addEventListener('submit', function(e) {
            const pw  = document.getElementById('password').value;
            const cpw = document.getElementById('confirm_password').value;
            if (pw !== cpw) {
                e.preventDefault();
                alert('Konfirmasi password tidak cocok!');
                return false;
            }
            const btn = document.getElementById('register-btn');
            btn.textContent = 'Mendaftarkan...';
            btn.disabled = true;
            btn.style.opacity = '0.75';
        });
    </script>

</body>
</html>
