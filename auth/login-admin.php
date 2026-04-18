<?php
session_start();
// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: ../admin/dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/functions.php';

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $user = db_fetch_one(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [$email]
        );

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_email'] = $user['email'];

            if ($remember) {
                // Set a remember-me cookie for 30 days
                setcookie('admin_remember', base64_encode($user['id']), time() + (86400 * 30), '/');
            }

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

        .input-wrapper { position: relative; }
        .input-wrapper .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }
        .input-wrapper .form-input.has-icon { padding-left: 38px; }

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

        .checkbox-custom {
            width: 15px;
            height: 15px;
            border: 1.5px solid #cbd5e1;
            border-radius: 4px;
            cursor: pointer;
            accent-color: #E02424;
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
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-[420px]">
        <div class="auth-card px-10 py-10">

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
            <div class="alert-error mb-5">
                <i class="ph-fill ph-warning-circle text-red-500" style="font-size:16px; flex-shrink:0;"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form id="admin-login-form" method="POST" action="">
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="form-label">
                        Email address<span class="text-primary ml-0.5">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder=""
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required
                            autocomplete="email"
                        >
                    </div>
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
                            autocomplete="current-password"
                            style="padding-right: 42px;"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password', this)" id="toggle-pwd-btn" aria-label="Tampilkan/Sembunyikan password">
                            <i class="ph ph-eye" id="eye-icon-password"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" id="remember" name="remember" class="checkbox-custom">
                    <label for="remember" class="text-[13px] text-gray-600 cursor-pointer select-none">Remember me</label>
                </div>

                <!-- Submit -->
                <button type="submit" id="login-btn" class="btn-primary">
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
