<?php
/**
 * Customer layout header — includes <head>, navbar, opens content area.
 *
 * Usage:
 *   $pageTitle = 'Dashboard';
 *   include __DIR__ . '/../includes/header-customer.php';
 *   // ... page content ...
 *   include __DIR__ . '/../includes/footer-customer.php';
 */

if (!isset($pageTitle)) $pageTitle = 'Customer Panel';

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$customerId = getCustomerId();
$customerData = $customerId ? db_fetch("SELECT name FROM customers WHERE id = ? AND deleted_at IS NULL", [$customerId]) : null;
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> — TEFA Canning</title>

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
                        accent: '#F05252',
                        dark: '#9B1C1C',
                        navy: '#111827',
                    }
                }
            }
        }
    </script>

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="font-sans text-gray-800 antialiased bg-gray-50">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white border-b border-gray-100 z-50">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-[70px]">
                <!-- Logo -->
                <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/customer/dashboard.php" class="flex items-center gap-3">
                    <img src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/images/politeknik_logo_red.png" alt="Logo" class="h-10 w-auto">
                    <div class="flex flex-col justify-center">
                        <span class="text-[15px] font-bold text-[#B91C1C] leading-none mb-1">TEFA Canning</span>
                        <span class="text-[10px] font-medium text-slate-400 leading-none">Customer Panel</span>
                    </div>
                </a>

                <!-- Menu -->
                <div class="flex items-center gap-8">
                    <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/customer/dashboard.php" class="text-[12px] font-semibold text-gray-500 hover:text-primary transition-colors">Dashboard</a>
                    <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/customer/preorder.php" class="text-[12px] font-semibold text-gray-500 hover:text-primary transition-colors">Pre-Order</a>
                    <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/customer/orders.php" class="text-[12px] font-semibold text-gray-500 hover:text-primary transition-colors">Pesanan</a>
                    <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/customer/profile.php" class="text-[12px] font-semibold text-gray-500 hover:text-primary transition-colors">Profil</a>

                    <!-- User -->
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-[11px] font-bold">
                            <?php echo $customerData ? strtoupper(substr($customerData['name'], 0, 1)) : '?'; ?>
                        </div>
                        <div class="hidden sm:block">
                            <div class="text-[12px] font-semibold text-navy"><?php echo $customerData ? htmlspecialchars($customerData['name']) : 'Customer'; ?></div>
                        </div>
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/customer/logout.php" class="text-[11px] text-gray-400 hover:text-primary transition-colors ml-2">
                            <i class="ph-bold ph-sign-out text-base"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="pt-[86px] pb-12 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
