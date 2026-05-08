import os

def update_responsive_layout(filepath):
    with open(filepath, "r", encoding="utf-8") as f:
        content = f.read()

    # 1. Update CSS
    css_old = """        /* Sidebar Collapse logic */
        #sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #main-wrapper {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

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
        }"""
        
    css_new = """        /* Sidebar Collapse logic */
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
        }"""
    content = content.replace(css_old, css_new)

    # 2. Add backdrop
    if '<div id="sidebar-backdrop"' not in content:
        content = content.replace('<div class="flex min-h-screen">', '<div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity opacity-0 duration-300" onclick="toggleMobileSidebar()"></div>\n    <div class="flex min-h-screen">')

    # 3. Update sidebar classes
    aside_old = 'class="w-[220px] bg-white dark:bg-[#1e293b] border-r border-gray-100 dark:border-gray-800 flex-shrink-0 flex flex-col fixed top-0 left-0 h-full z-30 overflow-y-auto group/sidebar"'
    aside_new = 'class="w-[220px] bg-white dark:bg-[#1e293b] border-r border-gray-100 dark:border-gray-800 flex-shrink-0 flex flex-col fixed top-0 left-0 h-full z-50 overflow-y-auto group/sidebar transform -translate-x-full md:translate-x-0"'
    content = content.replace(aside_old, aside_new)

    # 4. Update collapse toggle
    toggle_old = """                <!-- Collapse toggle -->
                <button onclick="toggleSidebarCollapse()"
                    class="ml-auto w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-navy transition-all">"""
    toggle_new = """                <!-- Collapse toggle -->
                <button onclick="toggleSidebarCollapse()"
                    class="ml-auto w-8 h-8 rounded-lg hidden md:flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-navy transition-all">
                    <i class="ph-bold ph-caret-left transition-transform duration-300" id="collapse-icon"></i>
                </button>
                <button onclick="toggleMobileSidebar()"
                    class="ml-auto w-8 h-8 rounded-lg flex md:hidden items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-navy transition-all">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>"""
    # Using a clever replace to handle the <i> inside
    content = content.replace("""                <!-- Collapse toggle -->
                <button onclick="toggleSidebarCollapse()"
                    class="ml-auto w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-navy transition-all">
                    <i class="ph-bold ph-caret-left transition-transform duration-300" id="collapse-icon"></i>
                </button>""", toggle_new)

    # 5. Update main wrapper
    wrapper_old = 'id="main-wrapper" class="flex-1 flex flex-col ml-[220px] min-w-0"'
    wrapper_new = 'id="main-wrapper" class="flex-1 flex flex-col ml-0 md:ml-[220px] min-w-0 w-full overflow-hidden"'
    content = content.replace(wrapper_old, wrapper_new)

    # 6. Topbar hamburger
    topbar_old = """            <!-- Top Bar -->
            <header
                class="h-[60px] bg-white dark:bg-[#1e293b] border-b border-gray-100 dark:border-gray-800 flex items-center justify-between px-6 flex-shrink-0 sticky top-0 z-20">

                <!-- Right -->"""
    topbar_new = """            <!-- Top Bar -->
            <header
                class="h-[60px] bg-white dark:bg-[#1e293b] border-b border-gray-100 dark:border-gray-800 flex items-center justify-between px-4 md:px-6 flex-shrink-0 sticky top-0 z-20">

                <!-- Left Mobile Hamburger -->
                <button onclick="toggleMobileSidebar()"
                    class="w-9 h-9 rounded-lg flex md:hidden items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all mr-3">
                    <i class="ph ph-list text-[24px]"></i>
                </button>
                
                <div class="flex-1"></div>

                <!-- Right -->"""
    content = content.replace(topbar_old, topbar_new)

    # 7. Add JS function
    js_func = """                function toggleMobileSidebar() {
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
"""
    if "function toggleMobileSidebar()" not in content:
        content = content.replace("function toggleSidebarCollapse() {", js_func + "\n                function toggleSidebarCollapse() {")

    with open(filepath, "w", encoding="utf-8") as f:
        f.write(content)

update_responsive_layout("d:/freelance/tefacanning-legacy/includes/header-admin.php")
update_responsive_layout("d:/freelance/tefacanning-legacy/includes/header-customer.php")
print("Responsive updates applied to both headers.")
