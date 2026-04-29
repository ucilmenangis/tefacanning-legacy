<?php
/**
 * Customer layout header — includes <head>, sidebar, topbar.
 * Uses Alif's design with PHP logic injected.
 *
 * Usage:
 *   $pageTitle = 'Dashboard';
 *   $currentPage = 'dashboard';
 *   include __DIR__ . '/../includes/header-customer.php';
 */
require_once __DIR__ . '/auth.php';

if (!isset($pageTitle))
    $pageTitle = 'Customer Panel';
if (!isset($currentPage))
    $currentPage = '';

$customerId = Auth::customer()->getId();
$customerData = $customerId ? Database::getInstance()->fetch("SELECT name, email, phone, organization, created_at FROM customers WHERE id = ? AND deleted_at IS NULL", [$customerId]) : null;
$customerInitial = $customerData ? strtoupper(substr($customerData['name'], 0, 1)) : 'C';
$basePath = dirname($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> — TEFA Canning SIP</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

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

    <style>
        /* ── Sidebar transition ── */
        #sidebar {
            width: 220px;
            transition: width 0.25s ease;
        }

        body.collapsed #sidebar {
            width: 64px;
        }

        .sidebar-label {
            white-space: nowrap;
            transition: opacity 0.2s ease, width 0.2s ease;
        }

        body.collapsed .sidebar-label {
            opacity: 0;
            width: 0;
            overflow: hidden;
            pointer-events: none;
        }

        /* ── Main & topbar ── */
        #main-content {
            margin-left: 220px;
            transition: margin-left 0.25s ease;
        }

        body.collapsed #main-content {
            margin-left: 64px;
        }

        #topbar {
            left: 220px;
            transition: left 0.25s ease;
        }

        body.collapsed #topbar {
            left: 64px;
        }

        /* ── Nav item ── */
        .nav-item {
            transition: background 0.15s, color 0.15s;
        }

        .nav-item:hover:not(.active) {
            background: #f9fafb;
        }

        .nav-item.active {
            background: #fef2f2;
            color: #E02424;
        }

        .nav-item.active .nav-icon {
            color: #E02424;
        }

        /* ── Thin scrollbar ── */
        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>

<body class="font-sans bg-gray-50 text-gray-800 antialiased">

    <!-- ═══════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════════ -->
    <aside id="sidebar"
        class="fixed top-0 left-0 h-screen bg-white border-r border-gray-100 z-40 flex flex-col overflow-hidden">

        <!-- Brand -->
        <div class="flex items-center gap-2.5 px-4 h-[60px] border-b border-gray-100 flex-shrink-0">
            <div class="w-8 h-8 flex-shrink-0 rounded-lg flex items-center justify-center">
                <img src="../assets/images/politeknik_logo_red.png" alt="Logo TEFA" class="h-10 w-auto">
            </div>
            <div class="sidebar-label flex flex-col leading-none overflow-hidden">
                <span class="text-[13px] font-bold text-[#B91C1C]">TEFA Canning SIP</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Politeknik Negeri Jember</span>
            </div>
            <button onclick="toggleSidebar()"
                class="ml-auto flex-shrink-0 sidebar-label text-gray-400 hover:text-[#E02424] transition-colors"
                title="Ciutkan sidebar">
                <i class="ph-bold ph-caret-left text-sm" id="collapse-icon"></i>
            </button>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

            <a href="<?php echo $basePath; ?>/dashboard.php"
                class="nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?> flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium cursor-pointer">
                <i class="ph-bold ph-house-simple nav-icon text-base flex-shrink-0"></i>
                <span class="sidebar-label">Dashboard</span>
            </a>

            <a href="<?php echo $basePath; ?>/preorder.php"
                class="nav-item <?php echo $currentPage === 'preorder' ? 'active' : ''; ?> flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium cursor-pointer">
                <i class="ph-bold ph-shopping-cart-simple nav-icon text-base flex-shrink-0"></i>
                <span class="sidebar-label">Pre-Order</span>
            </a>

            <a href="<?php echo $basePath; ?>/orders.php"
                class="nav-item <?php echo $currentPage === 'orders' ? 'active' : ''; ?> flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium text-gray-600 cursor-pointer">
                <i class="ph-bold ph-clock-counter-clockwise nav-icon text-base flex-shrink-0 text-gray-400"></i>
                <span class="sidebar-label">Riwayat Pesanan</span>
            </a>

            <a href="<?php echo $basePath; ?>/profile.php"
                class="nav-item <?php echo $currentPage === 'profile' ? 'active' : ''; ?> flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium text-gray-600 cursor-pointer">
                <i class="ph-bold ph-user-circle nav-icon text-base flex-shrink-0 text-gray-400"></i>
                <span class="sidebar-label">Profil Saya</span>
            </a>

        </nav>
    </aside>

    <!-- ═══════════════════════════════════════════
     TOPBAR
════════════════════════════════════════════ -->
    <header id="topbar" class="fixed top-0 right-0 h-[60px] bg-white border-b border-gray-100 z-30
               flex items-center justify-end px-6">
        <div class="flex items-center gap-3">
            <span
                class="text-[12px] font-medium text-navy hidden sm:block"><?php echo $customerData ? htmlspecialchars($customerData['name']) : 'Customer'; ?></span>
            <div class="w-8 h-8 rounded-full bg-[#E02424] flex items-center justify-center
                    text-white text-[11px] font-bold select-none">
                <?php echo $customerInitial; ?>
            </div>
            <a href="../auth/logout.php?type=customer"
                class="text-[11px] text-gray-400 hover:text-[#E02424] transition-colors" title="Logout">
                <i class="ph-bold ph-sign-out text-base"></i>
            </a>
        </div>
    </header>

    <!-- ═══════════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════════ -->
    <main id="main-content" class="min-h-screen pt-[60px] p-8">
        <?php echo FlashMessage::render(); ?>