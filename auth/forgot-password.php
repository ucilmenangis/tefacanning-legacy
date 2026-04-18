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

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #E02424;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.15s;
        }
        .back-link:hover { color: #9B1C1C; text-decoration: underline; }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-[420px]">
        <div class="auth-card px-10 py-10">

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
                <a href="login-customer.php" class="back-link">
                    <i class="ph ph-arrow-left" style="font-size:13px;"></i>
                    back to login
                </a>
            </div>

            <!-- Alerts -->
            <?php if ($error): ?>
            <div class="alert-error mb-5">
                <i class="ph-fill ph-warning-circle text-red-500" style="font-size:16px; flex-shrink:0;"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert-success mb-5">
                <i class="ph-fill ph-check-circle text-green-600" style="font-size:16px; flex-shrink:0;"></i>
                <?php echo $success; ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <?php if (!$success): ?>
            <form id="forgot-form" method="POST" action="">

                <!-- Email -->
                <div class="mb-5">
                    <label for="email" class="form-label">
                        Email address<span class="text-primary ml-0.5">*</span>
                    </label>
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

                <!-- Submit -->
                <button type="submit" id="send-btn" class="btn-primary">
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
