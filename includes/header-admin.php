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

// RBAC: check if current admin is super_admin
$isAdminSuperAdmin = isSuperAdmin();
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
        /* Accordion + dynamic states — can't be done with Tailwind alone */
        .nav-sub { overflow:hidden; max-height:0; transition:max-height .25s ease; }
        .nav-sub.open { max-height:300px; }
        .nav-parent.open .nav-caret { transform:rotate(180deg); }
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
                <a href="dashboard.php" id="nav-dashboard"
                    class="flex items-center gap-[9px] py-[7px] px-3 rounded-[7px] text-[13px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 select-none no-underline hover:bg-white/[.08] hover:text-white <?php echo $currentPage === 'dashboard' ? '!text-primary bg-primary/10 [&_i]:text-primary' : ''; ?>">
                    <i class="ph-bold ph-squares-four text-base"></i>
                    Dashboard
                </a>

                <!-- Transaksi -->
                <div>
                    <div class="nav-parent flex items-center gap-[9px] py-[7px] px-3 rounded-[7px] text-[13px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 select-none hover:bg-white/[.08] hover:text-white open:!text-white <?php echo in_array($currentPage, ['orders']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-transaksi', this)">
                        <i class="ph-bold ph-receipt text-base"></i>
                        Transaksi
                        <i class="ph-bold ph-caret-down nav-caret ml-auto transition-transform duration-200 text-[11px]"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['orders']) ? 'open' : ''; ?>"
                        id="grp-transaksi">
                        <a href="orders.php"
                            class="flex items-center gap-2 py-1.5 px-3 pl-9 rounded-md text-[12.5px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 no-underline hover:bg-white/[.08] hover:text-white <?php echo $currentPage === 'orders' ? '!text-primary' : ''; ?>">
                            <span class="w-[5px] h-[5px] rounded-full bg-current shrink-0"></span> Pesanan
                        </a>
                    </div>
                </div>

                <!-- Master Data -->
                <div>
                    <div class="nav-parent flex items-center gap-[9px] py-[7px] px-3 rounded-[7px] text-[13px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 select-none hover:bg-white/[.08] hover:text-white open:!text-white <?php echo in_array($currentPage, ['customers', 'products']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-master', this)">
                        <i class="ph-bold ph-database text-base"></i>
                        Master Data
                        <i class="ph-bold ph-caret-down nav-caret ml-auto transition-transform duration-200 text-[11px]"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['customers', 'products']) ? 'open' : ''; ?>"
                        id="grp-master">
                        <a href="customers.php"
                            class="flex items-center gap-2 py-1.5 px-3 pl-9 rounded-md text-[12.5px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 no-underline hover:bg-white/[.08] hover:text-white <?php echo $currentPage === 'customers' ? '!text-primary' : ''; ?>">
                            <span class="w-[5px] h-[5px] rounded-full bg-current shrink-0"></span> Pelanggan
                        </a>
                        <a href="products.php"
                            class="flex items-center gap-2 py-1.5 px-3 pl-9 rounded-md text-[12.5px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 no-underline hover:bg-white/[.08] hover:text-white <?php echo $currentPage === 'products' ? '!text-primary' : ''; ?>">
                            <span class="w-[5px] h-[5px] rounded-full bg-current shrink-0"></span> Produk
                            <span class="ml-auto bg-primary text-white text-[10px] font-bold py-[1px] px-1.5 rounded-full min-w-[18px] text-center">3</span>
                        </a>
                    </div>
                </div>

                <!-- Manajemen Produksi -->
                <div>
                    <div class="nav-parent flex items-center gap-[9px] py-[7px] px-3 rounded-[7px] text-[13px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 select-none hover:bg-white/[.08] hover:text-white open:!text-white <?php echo in_array($currentPage, ['batches']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-produksi', this)">
                        <i class="ph-bold ph-factory text-base"></i>
                        Manajemen Produksi
                        <i class="ph-bold ph-caret-down nav-caret ml-auto transition-transform duration-200 text-[11px]"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['batches']) ? 'open' : ''; ?>"
                        id="grp-produksi">
                        <a href="batches.php"
                            class="flex items-center gap-2 py-1.5 px-3 pl-9 rounded-md text-[12.5px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 no-underline hover:bg-white/[.08] hover:text-white <?php echo $currentPage === 'batches' ? '!text-primary' : ''; ?>">
                            <span class="w-[5px] h-[5px] rounded-full bg-current shrink-0"></span> Batches
                            <span class="ml-auto bg-primary text-white text-[10px] font-bold py-[1px] px-1.5 rounded-full min-w-[18px] text-center">1</span>
                        </a>
                    </div>
                </div>

                <?php if ($isAdminSuperAdmin): ?>
                <!-- Audit & Log -->
                <div>
                    <div class="nav-parent flex items-center gap-[9px] py-[7px] px-3 rounded-[7px] text-[13px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 select-none hover:bg-white/[.08] hover:text-white open:!text-white <?php echo in_array($currentPage, ['activity-log']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-audit', this)">
                        <i class="ph-bold ph-shield-check text-base"></i>
                        Audit & Log
                        <i class="ph-bold ph-caret-down nav-caret ml-auto transition-transform duration-200 text-[11px]"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['activity-log']) ? 'open' : ''; ?>"
                        id="grp-audit">
                        <a href="activity-log.php"
                            class="flex items-center gap-2 py-1.5 px-3 pl-9 rounded-md text-[12.5px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 no-underline hover:bg-white/[.08] hover:text-white <?php echo $currentPage === 'activity-log' ? '!text-primary' : ''; ?>">
                            <span class="w-[5px] h-[5px] rounded-full bg-current shrink-0"></span> Log Aktivitas
                        </a>
                    </div>
                </div>

                <!-- Pengaturan -->
                <div>
                    <div class="nav-parent flex items-center gap-[9px] py-[7px] px-3 rounded-[7px] text-[13px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 select-none hover:bg-white/[.08] hover:text-white open:!text-white <?php echo in_array($currentPage, ['users']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-settings', this)">
                        <i class="ph-bold ph-gear text-base"></i>
                        Pengaturan
                        <i class="ph-bold ph-caret-down nav-caret ml-auto transition-transform duration-200 text-[11px]"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['users']) ? 'open' : ''; ?>"
                        id="grp-settings">
                        <a href="users.php"
                            class="flex items-center gap-2 py-1.5 px-3 pl-9 rounded-md text-[12.5px] font-medium text-slate-400 cursor-pointer transition-colors duration-150 no-underline hover:bg-white/[.08] hover:text-white <?php echo $currentPage === 'users' ? '!text-primary' : ''; ?>">
                            <span class="w-[5px] h-[5px] rounded-full bg-current shrink-0"></span> Pengguna
                            <span class="ml-auto bg-primary text-white text-[10px] font-bold py-[1px] px-1.5 rounded-full min-w-[18px] text-center">2</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

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
                    <input type="text" placeholder="Search" id="topbar-search-input"
                        class="border border-gray-200 rounded-lg py-1.5 pl-[34px] pr-3 text-[13px] text-gray-700 bg-gray-50 outline-none w-[220px] transition-all duration-150 focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white">
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