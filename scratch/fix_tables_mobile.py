import os
import glob
import re

admin_dir = "d:/freelance/tefacanning-legacy/admin"

# 1. Fix dashboard.php
dashboard_file = os.path.join(admin_dir, "dashboard.php")
with open(dashboard_file, "r", encoding="utf-8") as f:
    dashboard_content = f.read()

# Fix top banner
dashboard_content = dashboard_content.replace(
    '<div class="bg-white border border-gray-100 rounded-xl px-6 py-4 mb-5 flex items-center justify-between shadow-sm">',
    '<div class="bg-white border border-gray-100 rounded-xl px-4 sm:px-6 py-4 mb-5 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 shadow-sm">'
)
# Fix form wrap
dashboard_content = dashboard_content.replace(
    '<div class="flex items-center gap-3">',
    '<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">'
)
dashboard_content = dashboard_content.replace(
    '<form method="GET" action="" class="flex items-center gap-2">',
    '<form method="GET" action="" class="flex flex-col sm:flex-row sm:items-center gap-2">'
)
# Fix stats grids
dashboard_content = dashboard_content.replace(
    '<div class="grid grid-cols-3 gap-4 mb-4">',
    '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">'
)
dashboard_content = dashboard_content.replace(
    '<div class="grid grid-cols-3 gap-4 mb-6">',
    '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">'
)
# Fix tables - wrap with overflow-x-auto
# There are 3 tables in dashboard.php
tables = dashboard_content.split('<table class="w-full text-left text-[12.5px] border-collapse">')
if len(tables) > 1:
    new_content = tables[0]
    for i in range(1, len(tables)):
        # find the end of the table
        end_idx = tables[i].find('</table>') + 8
        if end_idx > 7:
            part1 = tables[i][:end_idx]
            part2 = tables[i][end_idx:]
            new_content += '<div class="overflow-x-auto">\n    <table class="w-full text-left text-[12.5px] border-collapse">' + part1 + '\n    </div>' + part2
    dashboard_content = new_content

with open(dashboard_file, "w", encoding="utf-8") as f:
    f.write(dashboard_content)


# 2. Fix Toolbars in all CRUD pages
def fix_toolbar(filepath):
    with open(filepath, "r", encoding="utf-8") as f:
        content = f.read()
    
    # Old toolbar pattern
    toolbar_start = '<div class="flex items-center justify-end gap-2 px-4 py-3 border-b border-gray-50">'
    search_start = '<div class="relative group mr-auto">'
    
    if toolbar_start in content and search_start in content:
        # replace toolbar wrapper
        content = content.replace(
            toolbar_start,
            '<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 px-4 py-3 border-b border-gray-50">'
        )
        # replace search wrapper
        content = content.replace(
            search_start,
            '<div class="relative group flex-1 w-full sm:max-w-[240px]">'
        )
        # add input width fix
        content = re.sub(r'bg-white w-\[240px\]', 'bg-white w-full', content)
        
        # Now we need to wrap the rest (filter, layout toggle) in a div
        # Find where the search ends: </div>
        search_idx = content.find('<div class="relative group flex-1 w-full sm:max-w-[240px]">')
        if search_idx != -1:
            end_search_idx = content.find('</div>', search_idx) + 6
            content = content[:end_search_idx] + '\n        <div class="flex items-center justify-end gap-2">' + content[end_search_idx:]
            
            # Find the end of the toolbar
            # The next '<!-- Data Table View -->' or '</div>\n\n    <!-- Data Table View -->'
            table_idx = content.find('<!-- Data Table View -->')
            if table_idx != -1:
                # insert closing div for the new wrapper before the final closing div of the toolbar
                close_idx = content.rfind('</div>', 0, table_idx)
                if close_idx != -1:
                    content = content[:close_idx] + '    </div>\n' + content[close_idx:]

    with open(filepath, "w", encoding="utf-8") as f:
        f.write(content)

pages_to_fix = ['orders.php', 'customers.php', 'batches.php', 'users.php', 'activity-log.php']
for p in pages_to_fix:
    fpath = os.path.join(admin_dir, p)
    if os.path.exists(fpath):
        fix_toolbar(fpath)

print("Dashboards and Toolbars updated for mobile view.")
