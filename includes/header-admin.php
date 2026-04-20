<?php
/**
 * Admin layout header — includes <head>, sidebar, opens content area.
 *
 * Usage:
 *   $pageTitle = 'Dashboard';
 *   include __DIR__ . '/../includes/header-admin.php';
 *   // ... page content ...
 *   include __DIR__ . '/../includes/footer-admin.php';
 */

if (!isset($pageTitle)) {
  $pageTitle = "Admin Panel";
} ?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(
      $pageTitle,
    ); ?> — TEFA Canning Admin</title>

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

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-navy text-white flex-shrink-0 flex flex-col">
        <!-- Logo -->
        <div class="h-[70px] flex items-center px-6 border-b border-white/10">
            <img src="<?php echo dirname(
              $_SERVER["SCRIPT_NAME"],
            ); ?>/assets/images/politeknik_logo.png" alt="Logo" class="h-8 w-auto mr-3">
            <div>
                <div class="text-[14px] font-bold leading-none">TEFA Canning</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Admin Panel</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-4 px-3 space-y-1">
            <a href="<?php echo dirname(
              $_SERVER["SCRIPT_NAME"],
            ); ?>/admin/dashboard.php" class="flex items-center px-3 py-2.5 rounded-lg text-[13px] font-medium text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                <i class="ph-bold ph-squares-four text-lg mr-3"></i> Dashboard
            </a>
            <a href="<?php echo dirname(
              $_SERVER["SCRIPT_NAME"],
            ); ?>/admin/products.php" class="flex items-center px-3 py-2.5 rounded-lg text-[13px] font-medium text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                <i class="ph-bold ph-package text-lg mr-3"></i> Produk
            </a>
            <a href="<?php echo dirname(
              $_SERVER["SCRIPT_NAME"],
            ); ?>/admin/batches.php" class="flex items-center px-3 py-2.5 rounded-lg text-[13px] font-medium text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                <i class="ph-bold ph-calendar-blank text-lg mr-3"></i> Batch
            </a>
            <a href="<?php echo dirname(
              $_SERVER["SCRIPT_NAME"],
            ); ?>/admin/orders.php" class="flex items-center px-3 py-2.5 rounded-lg text-[13px] font-medium text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                <i class="ph-bold ph-shopping-bag text-lg mr-3"></i> Pesanan
            </a>
            <a href="<?php echo dirname(
              $_SERVER["SCRIPT_NAME"],
            ); ?>/admin/customers.php" class="flex items-center px-3 py-2.5 rounded-lg text-[13px] font-medium text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                <i class="ph-bold ph-users text-lg mr-3"></i> Pelanggan
            </a>

            <!-- Super admin only -->
            <div class="pt-3 mt-3 border-t border-white/10">
                <a href="<?php echo dirname(
                  $_SERVER["SCRIPT_NAME"],
                ); ?>/admin/users.php" class="flex items-center px-3 py-2.5 rounded-lg text-[13px] font-medium text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                    <i class="ph-bold ph-user-circle-gear text-lg mr-3"></i> Manajemen User
                </a>
                <a href="<?php echo dirname(
                  $_SERVER["SCRIPT_NAME"],
                ); ?>/admin/activity-log.php" class="flex items-center px-3 py-2.5 rounded-lg text-[13px] font-medium text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                    <i class="ph-bold ph-list-bullets text-lg mr-3"></i> Activity Log
                </a>
            </div>
        </nav>

        <!-- Logout -->
        <div class="p-3 border-t border-white/10">
            <?php
            require_once __DIR__ . "/auth.php";
            require_once __DIR__ . "/functions.php";
            $adminId = getAdminId();
            $adminUser = $adminId
              ? db_fetch("SELECT name FROM users WHERE id = ?", [$adminId])
              : null;
            ?>
            <div class="flex items-center px-3 py-2 mb-2">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-[12px] font-bold mr-3">
                    <?php echo $adminUser
                      ? strtoupper(substr($adminUser["name"], 0, 1))
                      : "?"; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[12px] font-medium text-white truncate"><?php echo $adminUser
                      ? htmlspecialchars($adminUser["name"])
                      : "Admin"; ?></div>
                    <div class="text-[10px] text-slate-400">Administrator</div>
                </div>
            </div>
            <a href="<?php echo dirname(
              $_SERVER["SCRIPT_NAME"],
            ); ?>/admin/logout.php" class="flex items-center justify-center px-3 py-2 rounded-lg text-[12px] font-medium text-slate-400 hover:text-white hover:bg-white/10 transition-colors">
                <i class="ph-bold ph-sign-out text-base mr-2"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Bar -->
        <header class="h-[70px] bg-white border-b border-gray-100 flex items-center justify-between px-8 flex-shrink-0">
            <h1 class="text-[18px] font-bold text-navy"><?php echo htmlspecialchars(
              $pageTitle,
            ); ?></h1>
            <div class="flex items-center gap-4">
                <a href="<?php echo dirname(
                  $_SERVER["SCRIPT_NAME"],
                ); ?>/index.php" target="_blank" class="text-[12px] text-gray-400 hover:text-primary transition-colors">
                    <i class="ph-bold ph-arrow-square-out mr-1"></i> Lihat Situs
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-8">
            <?php echo renderFlash(); ?>
