import re
import os

admin_file = "d:/freelance/tefacanning-legacy/includes/header-admin.php"
customer_file = "d:/freelance/tefacanning-legacy/includes/header-customer.php"
footer_admin_file = "d:/freelance/tefacanning-legacy/includes/footer-admin.php"
footer_customer_file = "d:/freelance/tefacanning-legacy/includes/footer-customer.php"

with open(admin_file, "r", encoding="utf-8") as f:
    admin_content = f.read()

# Replace PHP logic at the top
php_logic = """<?php
/**
 * Customer layout header — includes <head>, sidebar, opens content area.
 */

require_once __DIR__ . '/auth.php';

if (!isset($pageTitle)) {
    $pageTitle = 'Customer Panel';
}
if (!isset($currentPage)) {
    $currentPage = '';
}

$customerId = Auth::customer()->getId();
$customerData = $customerId ? Database::getInstance()->fetch("SELECT name, email, phone, organization, created_at FROM customers WHERE id = ? AND deleted_at IS NULL", [$customerId]) : null;
$fullName = $customerData['name'] ?? 'Customer';
$email = $customerData['email'] ?? '';
$roleName = 'Customer';
$words = explode(' ', $fullName);
$initials = '';
if (count($words) >= 2) {
    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
} else {
    $initials = strtoupper(substr($words[0], 0, 2));
}

$basePath = dirname($_SERVER['SCRIPT_NAME']);
?>"""

# Extract everything from <!DOCTYPE html> onwards
html_part = admin_content[admin_content.find("<!DOCTYPE html>"):]

# Replace admin-theme with customer-theme
html_part = html_part.replace("'admin-theme'", "'customer-theme'")

# Replace auth/logout.php?type=admin with auth/logout.php?type=customer
html_part = html_part.replace("auth/logout.php?type=admin", "auth/logout.php?type=customer")

# Replace $currentUser['email'] ?? '' with $email
html_part = html_part.replace("<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>", "<?php echo htmlspecialchars($email); ?>")

# Remove Pengaturan Akun link in topbar
# We'll just replace the whole profile dropdown area links
profile_links_old = """                            <!-- <a href="pengaturan.php"
                                class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-navy dark:hover:text-white transition-colors">
                                <i class="ph ph-user-circle text-[18px] text-gray-400"></i>
                                Profil Saya
                            </a> -->
                            <a href="pengaturan.php"
                                class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-navy dark:hover:text-white transition-colors">
                                <i class="ph ph-gear text-[18px] text-gray-400"></i>
                                Pengaturan Akun
                            </a>"""

profile_links_new = """                            <a href="<?php echo $basePath; ?>/profile.php"
                                class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-navy dark:hover:text-white transition-colors">
                                <i class="ph ph-user-circle text-[18px] text-gray-400"></i>
                                Profil Saya
                            </a>"""

html_part = html_part.replace(profile_links_old, profile_links_new)

# Replace the Navigation section
nav_start = html_part.find("<!-- Navigation -->")
nav_end = html_part.find("</nav>")

nav_new = """<!-- Navigation -->
            <nav class="flex-1 py-3 px-3 space-y-1">

                <a href="<?php echo $basePath; ?>/dashboard.php"
                    class="flex items-center gap-[12px] py-[10px] px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-gray-400 cursor-pointer transition-all duration-150 select-none no-underline hover:bg-primary/5 dark:hover:bg-primary/10 hover:text-primary dark:hover:text-primary <?php echo $currentPage === 'dashboard' ? '!text-primary bg-primary/10 dark:bg-primary/20 [&_i]:text-primary' : ''; ?>">
                    <i class="ph-bold ph-house-simple text-[20px] shrink-0"></i>
                    <span class="nav-text whitespace-nowrap">Dashboard</span>
                </a>

                <a href="<?php echo $basePath; ?>/preorder.php"
                    class="flex items-center gap-[12px] py-[10px] px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-gray-400 cursor-pointer transition-all duration-150 select-none no-underline hover:bg-primary/5 dark:hover:bg-primary/10 hover:text-primary dark:hover:text-primary <?php echo $currentPage === 'preorder' ? '!text-primary bg-primary/10 dark:bg-primary/20 [&_i]:text-primary' : ''; ?>">
                    <i class="ph-bold ph-shopping-cart-simple text-[20px] shrink-0"></i>
                    <span class="nav-text whitespace-nowrap">Pre-Order</span>
                </a>

                <a href="<?php echo $basePath; ?>/orders.php"
                    class="flex items-center gap-[12px] py-[10px] px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-gray-400 cursor-pointer transition-all duration-150 select-none no-underline hover:bg-primary/5 dark:hover:bg-primary/10 hover:text-primary dark:hover:text-primary <?php echo $currentPage === 'orders' ? '!text-primary bg-primary/10 dark:bg-primary/20 [&_i]:text-primary' : ''; ?>">
                    <i class="ph-bold ph-clock-counter-clockwise text-[20px] shrink-0"></i>
                    <span class="nav-text whitespace-nowrap">Riwayat Pesanan</span>
                </a>

                <a href="<?php echo $basePath; ?>/profile.php"
                    class="flex items-center gap-[12px] py-[10px] px-3 rounded-xl text-[13px] font-medium text-slate-500 dark:text-gray-400 cursor-pointer transition-all duration-150 select-none no-underline hover:bg-primary/5 dark:hover:bg-primary/10 hover:text-primary dark:hover:text-primary <?php echo $currentPage === 'profile' ? '!text-primary bg-primary/10 dark:bg-primary/20 [&_i]:text-primary' : ''; ?>">
                    <i class="ph-bold ph-user-circle text-[20px] shrink-0"></i>
                    <span class="nav-text whitespace-nowrap">Profil Saya</span>
                </a>

            """

html_part = html_part[:nav_start] + nav_new + html_part[nav_end:]

final_content = php_logic + "\n" + html_part

with open(customer_file, "w", encoding="utf-8") as f:
    f.write(final_content)

# Update footer-customer.php
with open(footer_admin_file, "r", encoding="utf-8") as f:
    footer_content = f.read()

footer_content = footer_content.replace("Admin layout footer", "Customer layout footer")
footer_content = footer_content.replace("every admin page", "every customer page")

with open(footer_customer_file, "w", encoding="utf-8") as f:
    f.write(footer_content)

print("Headers updated successfully!")
