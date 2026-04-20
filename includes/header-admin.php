<?php
/**
 * Admin layout header — includes <head>, sidebar, opens content area.
 *
 * Variables expected from page:
 *   $pageTitle   — Page title string (required)
 *   $currentPage — Active nav key: dashboard, orders, customers, products, batches, activity-log, users
 */

if (!isset($pageTitle)) {
    $pageTitle = 'Admin Panel';
}
if (!isset($currentPage)) {
    $currentPage = '';
}

// Resolve base URL path (works from /admin/ subdirectory)
$base = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> — TEFA Canning Admin</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800&display=swap"
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
        /* ── Sidebar nav groups ── */
        .nav-group-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            color: #64748b;
            padding: 6px 12px 4px;
            text-transform: uppercase;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 12px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
            cursor: pointer;
            transition: background .15s, color .15s;
            user-select: none;
            text-decoration: none;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, .08);
            color: #fff;
        }

        .nav-item.active {
            color: #E02424;
            background: rgba(224, 36, 36, .12);
        }

        .nav-item.active i {
            color: #E02424;
        }

        /* submenu parent toggle */
        .nav-parent {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 12px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
            cursor: pointer;
            transition: background .15s, color .15s;
            user-select: none;
        }

        .nav-parent:hover {
            background: rgba(255, 255, 255, .08);
            color: #fff;
        }

        .nav-parent.open {
            color: #fff;
        }

        .nav-caret {
            margin-left: auto;
            transition: transform .2s;
            font-size: 11px;
        }

        .nav-parent.open .nav-caret {
            transform: rotate(180deg);
        }

        .nav-sub {
            overflow: hidden;
            max-height: 0;
            transition: max-height .25s ease;
        }

        .nav-sub.open {
            max-height: 300px;
        }

        .nav-subitem {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px 6px 36px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 500;
            color: #94a3b8;
            cursor: pointer;
            transition: background .15s, color .15s;
            text-decoration: none;
        }

        .nav-subitem:hover {
            background: rgba(255, 255, 255, .08);
            color: #fff;
        }

        .nav-subitem.active {
            color: #E02424;
        }

        .nav-subitem .dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: #E02424;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 999px;
            min-width: 18px;
            text-align: center;
        }

        /* ── Top bar search ── */
        .topbar-search {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 12px 6px 34px;
            font-size: 13px;
            color: #374151;
            background: #f9fafb;
            outline: none;
            width: 220px;
            transition: border-color .15s, box-shadow .15s;
        }

        .topbar-search:focus {
            border-color: #E02424;
            box-shadow: 0 0 0 3px rgba(224, 36, 36, .08);
            background: #fff;
        }
    </style>
</head>

<body class="font-sans text-gray-800 antialiased bg-[#f8f9fb]">

    <div class="flex min-h-screen">

        <!-- ═══ Sidebar ═══ -->
        <aside id="sidebar"
            class="w-[220px] bg-[#F8F8FF] text-white flex-shrink-0 flex flex-col fixed top-0 left-0 h-full z-30 overflow-y-auto">

            <!-- Logo -->
            <div class="h-[60px] flex items-center px-4 border-b border-white/10 flex-shrink-0">
                <img src="../assets/images/politeknik_logo_red.png" alt="Logo TEFA" class="h-8 w-auto mr-2">
                <div>
                    <div class="text-[13px] font-bold text-black leading-none">TEFA Canning SIP</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Politeknik Negeri Jember</div>
                </div>
                <!-- collapse btn (mobile) -->
                <button onclick="toggleSidebar()" class="ml-auto text-slate-500 hover:text-white lg:hidden">
                    <i class="ph-bold ph-x text-base"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-3 px-2 space-y-0.5">

                <!-- Dashboard -->
                <a href="<?php echo $base; ?>/admin/dashboard.php" id="nav-dashboard"
                    class="nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <i class="ph-bold ph-squares-four text-base"></i>
                    Dashboard
                </a>

                <!-- Transaksi -->
                <div>
                    <div class="nav-parent <?php echo in_array($currentPage, ['orders']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-transaksi', this)">
                        <i class="ph-bold ph-receipt text-base"></i>
                        Transaksi
                        <i class="ph-bold ph-caret-down nav-caret"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['orders']) ? 'open' : ''; ?>"
                        id="grp-transaksi">
                        <a href="<?php echo $base; ?>/admin/orders.php"
                            class="nav-subitem <?php echo $currentPage === 'orders' ? 'active' : ''; ?>">
                            <span class="dot"></span> Pesanan
                        </a>
                    </div>
                </div>

                <!-- Master Data -->
                <div>
                    <div class="nav-parent <?php echo in_array($currentPage, ['customers', 'products']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-master', this)">
                        <i class="ph-bold ph-database text-base"></i>
                        Master Data
                        <i class="ph-bold ph-caret-down nav-caret"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['customers', 'products']) ? 'open' : ''; ?>"
                        id="grp-master">
                        <a href="<?php echo $base; ?>/admin/customers.php"
                            class="nav-subitem <?php echo $currentPage === 'customers' ? 'active' : ''; ?>">
                            <span class="dot"></span> Pelanggan
                        </a>
                        <a href="<?php echo $base; ?>/admin/products.php"
                            class="nav-subitem <?php echo $currentPage === 'products' ? 'active' : ''; ?>">
                            <span class="dot"></span> Produk
                            <span class="nav-badge">3</span>
                        </a>
                    </div>
                </div>

                <!-- Manajemen Produksi -->
                <div>
                    <div class="nav-parent <?php echo in_array($currentPage, ['batches']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-produksi', this)">
                        <i class="ph-bold ph-factory text-base"></i>
                        Manajemen Produksi
                        <i class="ph-bold ph-caret-down nav-caret"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['batches']) ? 'open' : ''; ?>"
                        id="grp-produksi">
                        <a href="<?php echo $base; ?>/admin/batches.php"
                            class="nav-subitem <?php echo $currentPage === 'batches' ? 'active' : ''; ?>">
                            <span class="dot"></span> Batches
                            <span class="nav-badge">1</span>
                        </a>
                    </div>
                </div>

                <!-- Audit & Log -->
                <div>
                    <div class="nav-parent <?php echo in_array($currentPage, ['activity-log']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-audit', this)">
                        <i class="ph-bold ph-shield-check text-base"></i>
                        Audit & Log
                        <i class="ph-bold ph-caret-down nav-caret"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['activity-log']) ? 'open' : ''; ?>"
                        id="grp-audit">
                        <a href="<?php echo $base; ?>/admin/activity-log.php"
                            class="nav-subitem <?php echo $currentPage === 'activity-log' ? 'active' : ''; ?>">
                            <span class="dot"></span> Log Aktivitas
                        </a>
                    </div>
                </div>

                <!-- Pengaturan -->
                <div>
                    <div class="nav-parent <?php echo in_array($currentPage, ['users']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-settings', this)">
                        <i class="ph-bold ph-gear text-base"></i>
                        Pengaturan
                        <i class="ph-bold ph-caret-down nav-caret"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['users']) ? 'open' : ''; ?>"
                        id="grp-settings">
                        <a href="<?php echo $base; ?>/admin/users.php"
                            class="nav-subitem <?php echo $currentPage === 'users' ? 'active' : ''; ?>">
                            <span class="dot"></span> Pengguna
                            <span class="nav-badge">2</span>
                        </a>
                    </div>
                </div>

            </nav>
        </aside>

        <!-- ═══ Main Wrapper ═══ -->
        <div class="flex-1 flex flex-col ml-[220px]">

            <!-- Top Bar -->
            <header
                class="h-[60px] bg-white border-b border-gray-100 flex items-center justify-between px-6 flex-shrink-0 sticky top-0 z-20">
                <!-- Hamburger (mobile) -->
                <button onclick="toggleSidebar()" class="lg:hidden mr-3 text-gray-400 hover:text-navy">
                    <i class="ph-bold ph-list text-xl"></i>
                </button>

                <!-- Search -->
                <div class="relative">
                    <i
                        class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" placeholder="Search" class="topbar-search" id="topbar-search-input">
                </div>

                <!-- Right -->
                <div class="flex items-center gap-3 ml-auto">
                    <!-- Notification bell -->
                    <button
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-navy transition-colors relative">
                        <i class="ph ph-bell text-lg"></i>
                    </button>
                    <!-- Avatar -->
                    <div
                        class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-white text-[12px] font-bold cursor-pointer">
                        SA
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <?php
                // Render flash messages if function exists
                if (function_exists('renderFlash')) {
                    echo renderFlash();
                }
                ?>
                <script>
                    function toggleGroup(id, btn) {
                        const sub = document.getElementById(id);
                        const isOpen = sub.classList.contains('open');
                        sub.classList.toggle('open', !isOpen);
                        btn.classList.toggle('open', !isOpen);
                    }
                </script>