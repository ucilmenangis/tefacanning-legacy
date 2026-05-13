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
$isAdminSuperAdmin = Auth::admin()->isSuperAdmin();
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> — TEFA Canning Admin</title>

    <!-- Theme Detection -->
    <script>
        if (localStorage.getItem('admin-theme') === 'dark' || (!('admin-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

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
            darkMode: 'class',
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
        /* Accordion + dynamic states */
        .nav-sub {
            overflow: hidden;
            max-height: 0;
            transition: max-height .25s ease;
        }

        .nav-sub.open {
            max-height: 400px;
        }

        .nav-parent.open .nav-caret {
            transform: rotate(180deg);
        }

        /* Sidebar Collapse logic */
        #sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #main-wrapper {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (min-width: 768px) {
            .sidebar-collapsed #sidebar {
                width: 70px;
            }

            .sidebar-collapsed #main-wrapper {
                margin-left: 70px;
            }

            .sidebar-collapsed .logo-text,
            .sidebar-collapsed .nav-text,
            .sidebar-collapsed .nav-caret,
            .sidebar-collapsed .nav-sub,
            .sidebar-collapsed .badge-text {
                display: none !important;
            }

            .sidebar-collapsed #sidebar nav {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .sidebar-collapsed .nav-item-container {
                justify-content: center;
            }

            /* Hover expansion when collapsed */
            .sidebar-collapsed aside:hover {
                width: 220px !important;
                box-shadow: 10px 0 30px rgba(0, 0, 0, 0.05);
            }

            .sidebar-collapsed aside:hover .logo-text,
            .sidebar-collapsed aside:hover .nav-text,
            .sidebar-collapsed aside:hover .nav-caret,
            .sidebar-collapsed aside:hover .badge-text,
            .sidebar-collapsed aside:hover .nav-sub {
                display: block !important;
            }
        }

        /* Dark Mode Overrides for global elements */
        .dark .bg-white,
        .dark .bg-slate-50,
        .dark .bg-gray-50 {
            background-color: #1e293b !important;
        }

        .dark .bg-[#f8f9fb],
        .dark .bg-gray-100 {
            background-color: #0f172a !important;
        }

        .dark .bg-gray-50\/50,
        .dark .hover\:bg-gray-50\/50:hover {
            background-color: #334155 !important;
        }

        .dark .border-gray-100,
        .dark .border-gray-50,
        .dark .border-gray-200 {
            border-color: #334155 !important;
        }

        /* Text Overrides */
        .dark .text-navy,
        .dark .text-slate-800,
        .dark .text-slate-700,
        .dark .text-gray-800,
        .dark .text-gray-700 {
            color: #f1f5f9 !important;
            /* slate-100 */
        }

        .dark .text-slate-600,
        .dark .text-slate-500,
        .dark .text-gray-600,
        .dark .text-gray-500 {
            color: #cbd5e1 !important;
            /* slate-300 */
        }

        .dark .text-slate-400,
        .dark .text-gray-400 {
            color: #94a3b8 !important;
            /* slate-400 */
        }

        .dark .text-gray-300,
        .dark .text-slate-300 {
            color: #64748b !important;
            /* slate-500 */
        }

        /* Table & Specifics */
        .dark thead th {
            background-color: #1e293b !important;
            color: #94a3b8 !important;
        }

        .dark tbody tr {
            border-color: #334155 !important;
        }

        /* Badges & Accents */
        .dark .bg-emerald-50 {
            background-color: rgba(16, 185, 129, 0.1) !important;
            color: #34d399 !important;
        }

        .dark .bg-primary\/5 {
            background-color: rgba(224, 36, 36, 0.1) !important;
        }

        /* Inputs */
        .dark input,
        .dark select,
        .dark textarea {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        .dark input::placeholder {
            color: #64748b !important;
        }
    </style>
</head>

<body class="font-sans text-gray-800 dark:text-gray-200 antialiased bg-[#f8f9fb] dark:bg-[#0f172a]">
    <script>
        // Check sidebar state before body renders to prevent flicker
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>

    <div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity opacity-0 duration-300" onclick="toggleMobileSidebar()"></div>
    <div class="flex min-h-screen">

        <!-- ═══ Sidebar ═══ -->
        <aside id="sidebar"
            class="w-[220px] bg-white dark:bg-[#1e293b] border-r border-gray-100 dark:border-gray-800 flex-shrink-0 flex flex-col fixed top-0 left-0 h-full z-50 overflow-y-auto group/sidebar transform -translate-x-full md:translate-x-0">

            <!-- Logo Area -->
            <div class="h-[60px] flex items-center px-4 border-b border-gray-50 dark:border-gray-800 flex-shrink-0">
                <img src="../assets/images/politeknik_logo_red.png" alt="Logo TEFA" class="h-8 w-auto min-w-[29px]">
                <div class="ml-2 logo-text overflow-hidden whitespace-nowrap flex-1">
                    <div class="text-[13px] font-bold text-navy dark:text-white leading-none">TEFA Canning SIP</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Politeknik Negeri Jember</div>
                </div>
                <!-- Collapse toggle -->
                <button onclick="toggleSidebarCollapse()"
                    class="ml-auto w-8 h-8 rounded-lg hidden md:flex items-center justify-center text-gray-400 hover:text-navy dark:hover:!text-red-500 transition-all">
                    <i class="ph-bold ph-caret-left transition-transform duration-300" id="collapse-icon"></i>
                </button>
                <button onclick="toggleMobileSidebar()"
                    class="ml-auto w-8 h-8 rounded-lg flex md:hidden items-center justify-center text-gray-400 hover:text-navy dark:hover:!text-red-500 transition-all">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-3 px-3 space-y-1">

                <!-- Dashboard -->
                <a href="dashboard.php" id="nav-dashboard"
                    class="flex items-center gap-[12px] py-[10px] px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-gray-400 cursor-pointer transition-all duration-150 select-none no-underline hover:bg-primary/5 dark:hover:bg-primary/10 hover:text-primary dark:hover:text-primary <?php echo $currentPage === 'dashboard' ? '!text-primary bg-primary/10 dark:bg-primary/20 [&_i]:text-primary' : ''; ?>">
                    <i class="ph-bold ph-squares-four text-[20px] shrink-0"></i>
                    <span class="nav-text whitespace-nowrap">Dashboard</span>
                </a>

                <!-- Transaksi -->
                <div>
                    <div class="nav-parent flex items-center gap-[12px] py-[10px] px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-gray-400 cursor-pointer transition-all duration-150 select-none hover:bg-primary/5 dark:hover:bg-primary/10 hover:text-primary dark:hover:text-primary <?php echo in_array($currentPage, ['orders']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-transaksi', this)">
                        <i class="ph-bold ph-receipt text-[20px] shrink-0"></i>
                        <span class="nav-text whitespace-nowrap">Transaksi</span>
                        <i
                            class="ph-bold ph-caret-down nav-caret ml-auto transition-transform duration-200 text-[10px]"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['orders']) ? 'open' : ''; ?>"
                        id="grp-transaksi">
                        <a href="orders.php"
                            class="flex items-center gap-3 py-2 px-3 pl-11 rounded-lg text-[13px] font-medium text-slate-400 dark:text-gray-500 cursor-pointer transition-colors duration-150 no-underline hover:text-primary dark:hover:!text-red-500 <?php echo $currentPage === 'orders' ? '!text-primary font-bold dark:!text-primary' : ''; ?>">
                            <span class="nav-text">Pesanan</span>
                        </a>
                    </div>
                </div>

                <!-- Master Data -->
                <div>
                    <div class="nav-parent flex items-center gap-[12px] py-[10px] px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-gray-400 cursor-pointer transition-all duration-150 select-none hover:bg-primary/5 dark:hover:bg-primary/10 hover:text-primary dark:hover:text-primary <?php echo in_array($currentPage, ['customers', 'products']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-master', this)">
                        <i class="ph-bold ph-database text-[20px] shrink-0"></i>
                        <span class="nav-text whitespace-nowrap">Master Data</span>
                        <i
                            class="ph-bold ph-caret-down nav-caret ml-auto transition-transform duration-200 text-[10px]"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['customers', 'products']) ? 'open' : ''; ?>"
                        id="grp-master">
                        <a href="customers.php"
                            class="flex items-center gap-3 py-2 px-3 pl-11 rounded-lg text-[13px] font-medium text-slate-400 dark:text-gray-500 cursor-pointer transition-colors duration-150 no-underline hover:text-primary dark:hover:!text-red-500 <?php echo $currentPage === 'customers' ? '!text-primary font-bold dark:!text-primary' : ''; ?>">
                            <span class="nav-text">Pelanggan</span>
                        </a>
                        <a href="products.php"
                            class="flex items-center gap-3 py-2 px-3 pl-11 rounded-lg text-[13px] font-medium text-slate-400 dark:text-gray-500 cursor-pointer transition-colors duration-150 no-underline hover:text-primary dark:hover:!text-red-500 <?php echo $currentPage === 'products' ? '!text-primary font-bold dark:!text-primary' : ''; ?>">
                            <span class="nav-text">Produk</span>
                        </a>
                    </div>
                </div>

                <!-- Manajemen Produksi -->
                <div>
                    <div class="nav-parent flex items-center gap-[12px] py-[10px] px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-gray-400 cursor-pointer transition-all duration-150 select-none hover:bg-primary/5 dark:hover:bg-primary/10 hover:text-primary dark:hover:text-primary <?php echo in_array($currentPage, ['batches']) ? 'open' : ''; ?>"
                        onclick="toggleGroup('grp-produksi', this)">
                        <i class="ph-bold ph-factory text-[20px] shrink-0"></i>
                        <span class="nav-text whitespace-nowrap">Manajemen Produksi</span>
                        <i
                            class="ph-bold ph-caret-down nav-caret ml-auto transition-transform duration-200 text-[10px]"></i>
                    </div>
                    <div class="nav-sub <?php echo in_array($currentPage, ['batches']) ? 'open' : ''; ?>"
                        id="grp-produksi">
                        <a href="batches.php"
                            class="flex items-center gap-3 py-2 px-3 pl-11 rounded-lg text-[13px] font-medium text-slate-400 dark:text-gray-500 cursor-pointer transition-colors duration-150 no-underline hover:text-primary dark:hover:!text-red-500 <?php echo $currentPage === 'batches' ? '!text-primary font-bold dark:!text-primary' : ''; ?>">
                            <span class="nav-text">Batches</span>
                        </a>
                    </div>
                </div>

                <?php if ($isAdminSuperAdmin): ?>
                    <!-- Audit & Log -->
                    <div>
                        <div class="nav-parent flex items-center gap-[12px] py-[10px] px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-gray-400 cursor-pointer transition-all duration-150 select-none hover:bg-primary/5 dark:hover:bg-primary/10 hover:text-primary dark:hover:text-primary <?php echo in_array($currentPage, ['activity-log']) ? 'open' : ''; ?>"
                            onclick="toggleGroup('grp-audit', this)">
                            <i class="ph-bold ph-shield-check text-[20px] shrink-0"></i>
                            <span class="nav-text whitespace-nowrap">Audit & Log</span>
                            <i
                                class="ph-bold ph-caret-down nav-caret ml-auto transition-transform duration-200 text-[10px]"></i>
                        </div>
                        <div class="nav-sub <?php echo in_array($currentPage, ['activity-log']) ? 'open' : ''; ?>"
                            id="grp-audit">
                            <a href="activity-log.php"
                                class="flex items-center gap-3 py-2 px-3 pl-11 rounded-lg text-[13px] font-medium text-slate-400 dark:text-gray-500 cursor-pointer transition-colors duration-150 no-underline hover:text-primary dark:hover:!text-red-500 <?php echo $currentPage === 'activity-log' ? '!text-primary font-bold dark:!text-primary' : ''; ?>">
                                <span class="nav-text">Log Aktivitas</span>
                            </a>
                        </div>
                    </div>

                    <!-- Pengaturan -->
                    <div>
                        <div class="nav-parent flex items-center gap-[12px] py-[10px] px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-gray-400 cursor-pointer transition-all duration-150 select-none hover:bg-primary/5 dark:hover:bg-primary/10 hover:text-primary dark:hover:text-primary <?php echo in_array($currentPage, ['users']) ? 'open' : ''; ?>"
                            onclick="toggleGroup('grp-settings', this)">
                            <i class="ph-bold ph-gear text-[20px] shrink-0"></i>
                            <span class="nav-text whitespace-nowrap">Pengaturan</span>
                            <i
                                class="ph-bold ph-caret-down nav-caret ml-auto transition-transform duration-200 text-[10px]"></i>
                        </div>
                        <div class="nav-sub <?php echo in_array($currentPage, ['users']) ? 'open' : ''; ?>"
                            id="grp-settings">
                            <a href="pengaturan.php"
                                class="flex items-center gap-3 py-2 px-3 pl-11 rounded-lg text-[13px] font-medium text-slate-400 dark:text-gray-500 cursor-pointer transition-colors duration-150 no-underline hover:text-primary dark:hover:!text-red-500 <?php echo $currentPage === 'users' ? '!text-primary font-bold dark:!text-primary' : ''; ?>">
                                <span class="nav-text">Pengguna</span>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </nav>
        </aside>

        <!-- ═══ Main Wrapper ═══ -->
        <div id="main-wrapper" class="flex-1 flex flex-col ml-0 md:ml-[220px] min-w-0 w-full overflow-hidden">

            <!-- Top Bar -->
            <header
                class="h-[60px] bg-white dark:bg-[#1e293b] border-b border-gray-100 dark:border-gray-800 flex items-center justify-between px-4 md:px-6 flex-shrink-0 sticky top-0 z-20">

                <!-- Left Mobile Hamburger -->
                <button onclick="toggleMobileSidebar()"
                    class="w-9 h-9 rounded-lg flex md:hidden items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all mr-3">
                    <i class="ph ph-list text-[24px]"></i>
                </button>
                
                <div class="flex-1"></div>

                <!-- Right -->
                <div class="flex items-center gap-2 ml-auto">
                    <!-- Theme Toggle -->
                    <button onclick="toggleDarkMode()"
                        class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-navy dark:hover:text-white transition-all group"
                        title="Toggle Light/Dark Mode">
                        <i class="ph ph-sun-dim text-[20px] dark:hidden"></i>
                        <i class="ph ph-moon text-[20px] hidden dark:block"></i>
                    </button>

                    <div class="h-6 w-px bg-gray-100 dark:bg-gray-800 mx-1"></div>

                    <!-- Profile Dropdown -->
                    <div class="relative ml-1" id="profile-dropdown-container">
                        <?php
                        $currentUser = Auth::admin()->user();
                        $fullName = $currentUser['name'] ?? 'Admin';
                        $roleName = $currentUser['role'] ?? 'Administrator';
                        // Get Initials
                        $words = explode(' ', $fullName);
                        $initials = '';
                        if (count($words) >= 2) {
                            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                        } else {
                            $initials = strtoupper(substr($words[0], 0, 2));
                        }
                        ?>
                        <button onclick="toggleProfileDropdown(event)"
                            class="flex items-center gap-2 group p-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-all cursor-pointer">
                            <div
                                class="w-9 h-9 rounded-full bg-navy dark:bg-slate-700 flex items-center justify-center text-white text-[13px] font-bold shadow-sm group-hover:scale-105 transition-transform">
                                <?php echo htmlspecialchars($initials); ?>
                            </div>
                            <div class="hidden md:block text-left mr-1">
                                <div class="text-[12px] font-bold text-navy dark:text-white leading-none">
                                    <?php echo htmlspecialchars($fullName); ?>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1"><?php echo htmlspecialchars($roleName); ?>
                                </div>
                            </div>
                            <i
                                class="ph ph-caret-down text-[10px] text-gray-400 transition-transform group-hover:translate-y-0.5"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="profile-menu"
                            class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-[#1e293b] border border-gray-100 dark:border-gray-800 rounded-xl shadow-xl z-[100] py-2 animate-in fade-in slide-in-from-top-2 duration-200">
                            <div class="px-4 py-3 border-b border-gray-50 dark:border-gray-800 mb-1">
                                <div class="text-[13px] font-bold text-navy dark:text-white">

                                    <?php echo htmlspecialchars($fullName); ?>
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5">
                                    <?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>
                                </div>
                            </div>

                            <!-- <a href="pengaturan.php"
                                class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-navy dark:hover:text-white transition-colors">
                                <i class="ph ph-user-circle text-[18px] text-gray-400"></i>
                                Profil Saya
                            </a> -->
                            <a href="pengaturan.php"
                                class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-navy dark:hover:text-white transition-colors">
                                <i class="ph ph-gear text-[18px] text-gray-400"></i>
                                Pengaturan Akun
                            </a>

                            <div class="h-px bg-gray-50 dark:bg-gray-800 my-1"></div>

                            <a href="../auth/logout.php?type=admin"
                                class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-red-500 hover:bg-red-50 dark:hover:bg-red-950 font-bold transition-colors">
                                <i class="ph ph-sign-out text-[18px]"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <script>
                function toggleDarkMode() {
                    const html = document.documentElement;
                    const isDark = html.classList.toggle('dark');
                    localStorage.setItem('admin-theme', isDark ? 'dark' : 'light');
                }

                function toggleProfileDropdown(e) {
                    e.stopPropagation();
                    document.getElementById('profile-menu').classList.toggle('hidden');
                }

                                function toggleMobileSidebar() {
                    const sidebar = document.getElementById('sidebar');
                    const backdrop = document.getElementById('sidebar-backdrop');
                    
                    if (sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.remove('-translate-x-full');
                        backdrop.classList.remove('hidden');
                        setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
                        document.body.style.overflow = 'hidden';
                    } else {
                        sidebar.classList.add('-translate-x-full');
                        backdrop.classList.add('opacity-0');
                        setTimeout(() => backdrop.classList.add('hidden'), 300);
                        document.body.style.overflow = '';
                    }
                }

                function toggleSidebarCollapse() {
                    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', isCollapsed);
                    updateCollapseIcon();
                }

                function updateCollapseIcon() {
                    const icon = document.getElementById('collapse-icon');
                    if (document.body.classList.contains('sidebar-collapsed')) {
                        icon.style.transform = 'rotate(180deg)';
                    } else {
                        icon.style.transform = 'rotate(0deg)';
                    }
                }

                // Clock functionality
                function updateClock() {
                    const now = new Date();
                    const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    const clockEl = document.getElementById('realtime-clock');
                    if (clockEl) clockEl.textContent = timeString;
                }
                setInterval(updateClock, 1000);
                updateClock();
                updateCollapseIcon();

                // Close dropdown on click outside
                window.addEventListener('click', function (e) {
                    const menu = document.getElementById('profile-menu');
                    if (menu && !menu.contains(e.target) && !e.target.closest('#profile-dropdown-container')) {
                        menu.classList.add('hidden');
                    }
                });
            </script>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <?php
                // Render flash messages
                echo FlashMessage::render();
                ?>
                <script>
                    function toggleGroup(id, btn) {
                        const sub = document.getElementById(id);
                        const isOpen = sub.classList.contains('open');

                        // Toggle logic
                        sub.classList.toggle('open', !isOpen);
                        btn.classList.toggle('open', !isOpen);
                    }
                </script>
