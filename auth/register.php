<?php
session_start();
// Redirect if already logged in
if (isset($_SESSION['customer_id'])) {
    header('Location: ../customer/dashboard.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/functions.php';

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
        $existing = db_fetch_one(
            "SELECT id FROM customers WHERE email = ? AND deleted_at IS NULL LIMIT 1",
            [$email]
        );

        if ($existing) {
            $error = 'Email sudah terdaftar. Silakan gunakan email lain atau masuk.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            $inserted = db_execute(
                "INSERT INTO customers (name, email, password, phone, organization, address, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())",
                [$name, $email, $hashed, $phone, $organization, $address]
            );

            if ($inserted) {
                $newCustomer = db_fetch_one(
                    "SELECT * FROM customers WHERE email = ? LIMIT 1",
                    [$email]
                );
                $_SESSION['customer_id']    = $newCustomer['id'];
                $_SESSION['customer_name']  = $newCustomer['name'];
                $_SESSION['customer_email'] = $newCustomer['email'];
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

    <style>
        body { background-color: #f1f5f9; }

        .auth-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 24px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            font-family: 'Inter', sans-serif;
        }
        .form-input:focus {
            border-color: #E02424;
            box-shadow: 0 0 0 3px rgba(224,36,36,0.08);
        }
        .form-input::placeholder { color: #94a3b8; }

        textarea.form-input {
            resize: vertical;
            min-height: 90px;
            padding-top: 10px;
        }

        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
            pointer-events: none;
        }
        .input-wrapper .form-input.with-icon { padding-left: 38px; }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 18px;
            background: none;
            border: none;
            padding: 0;
            transition: color 0.2s;
        }
        .toggle-password:hover { color: #64748b; }

        .btn-primary {
            width: 100%;
            padding: 11px 20px;
            background: #E02424;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .btn-primary:hover {
            background: #c81e1e;
            box-shadow: 0 4px 12px rgba(224,36,36,0.25);
        }
        .btn-primary:active { transform: translateY(1px); }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .link-red {
            color: #E02424;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s;
        }
        .link-red:hover { color: #9B1C1C; text-decoration: underline; }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4 py-10">

    <div class="w-full max-w-[420px]">
        <div class="auth-card px-10 py-10">

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
                or <a href="login-customer.php" class="link-red">sign in to your account</a>
            </p>

            <!-- Error Alert -->
            <?php if ($error): ?>
            <div class="alert-error mb-5">
                <i class="ph-fill ph-warning-circle text-red-500" style="font-size:16px; flex-shrink:0;"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form id="register-form" method="POST" action="">

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="form-label">
                        Name<span class="text-primary ml-0.5">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        value="<?= $old['name'] ?>"
                        required
                        autocomplete="name"
                    >
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="form-label">
                        Email address<span class="text-primary ml-0.5">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        value="<?= $old['email'] ?>"
                        required
                        autocomplete="email"
                    >
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label for="phone" class="form-label">
                        No. Telepon<span class="text-primary ml-0.5">*</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="ph ph-phone-call input-icon"></i>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-input with-icon"
                            placeholder="08xxxxxxxxxx"
                            value="<?= $old['phone'] ?>"
                            required
                            autocomplete="tel"
                        >
                    </div>
                </div>

                <!-- Organization -->
                <div class="mb-4">
                    <label for="organization" class="form-label">Organisasi / Instansi</label>
                    <div class="input-wrapper">
                        <i class="ph ph-buildings input-icon"></i>
                        <input
                            type="text"
                            id="organization"
                            name="organization"
                            class="form-input with-icon"
                            placeholder="Nama organisasi atau instansi Anda"
                            value="<?= $old['organization'] ?>"
                            autocomplete="organization"
                        >
                    </div>
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label for="address" class="form-label">
                        Alamat<span class="text-primary ml-0.5">*</span>
                    </label>
                    <textarea
                        id="address"
                        name="address"
                        class="form-input"
                        placeholder="Alamat lengkap Anda"
                        required
                    ><?= $old['address'] ?></textarea>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="form-label">
                        Password<span class="text-primary ml-0.5">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder=""
                            required
                            autocomplete="new-password"
                            style="padding-right: 42px;"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Tampilkan/Sembunyikan password">
                            <i class="ph ph-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="confirm_password" class="form-label">
                        Confirm password<span class="text-primary ml-0.5">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            class="form-input"
                            placeholder=""
                            required
                            autocomplete="new-password"
                            style="padding-right: 42px;"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)" aria-label="Tampilkan/Sembunyikan konfirmasi password">
                            <i class="ph ph-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" id="register-btn" class="btn-primary">
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
