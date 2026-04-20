<?php
$pageTitle   = 'Produk';
$currentPage = 'products';
// TODO: Uncomment when auth ready
// require_once __DIR__ . '/../includes/auth.php';
// requireAdmin();
include __DIR__ . '/../includes/header-admin.php';

// ── Mock Data ──
$products = [
    [
        'id'     => 1,
        'name'   => 'Sarden SIP Asin',
        'sku'    => 'TEFA-ASN-001',
        'price'  => 'IDR 22,000.00',
        'stock'  => 500,
        'satuan' => 'kaleng',
        'aktif'  => true,
        'img'    => '../assets/images/product.jpeg',
    ],
    [
        'id'     => 2,
        'name'   => 'Sarden SIP Saus Cabai',
        'sku'    => 'TEFA-SSC-001',
        'price'  => 'IDR 25,000.00',
        'stock'  => 500,
        'satuan' => 'kaleng',
        'aktif'  => true,
        'img'    => '../assets/images/product.jpeg',
    ],
    [
        'id'     => 3,
        'name'   => 'Sarden SIP Saus Tomat',
        'sku'    => 'TEFA-SST-001',
        'price'  => 'IDR 25,000.00',
        'stock'  => 500,
        'satuan' => 'kaleng',
        'aktif'  => true,
        'img'    => '../assets/images/product.jpeg',
    ],
];
?>

<style>
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.breadcrumb  { font-size:12px; color:#9ca3af; margin-bottom:4px; }
.breadcrumb span { color:#374151; }

.btn-primary { display:inline-flex; align-items:center; gap:6px; background:#E02424; color:#fff;
    font-size:13px; font-weight:700; padding:8px 18px; border-radius:8px;
    border:none; cursor:pointer; transition:background .15s; text-decoration:none; }
.btn-primary:hover { background:#9B1C1C; }

.table-wrap    { background:#fff; border:1px solid #f1f5f9; border-radius:12px; overflow:hidden; }
.table-toolbar { display:flex; align-items:center; justify-content:flex-end; gap:8px; padding:12px 16px; border-bottom:1px solid #f8fafc; }
.mini-search { border:1px solid #e5e7eb; border-radius:7px; padding:6px 10px 6px 30px; font-size:12px; outline:none; background:#f9fafb; width:200px; transition:border-color .15s; }
.mini-search:focus { border-color:#E02424; background:#fff; }
.icon-btn-sm { width:30px; height:30px; border-radius:6px; border:1px solid #e5e7eb; background:#fff;
    display:inline-flex; align-items:center; justify-content:center; color:#9ca3af; cursor:pointer; transition:background .15s; }
.icon-btn-sm:hover { background:#f8fafc; color:#374151; }

.data-table { width:100%; text-align:left; font-size:12.5px; border-collapse:collapse; }
.data-table th { font-size:11.5px; font-weight:600; color:#9ca3af; padding:10px 14px; border-bottom:1px solid #f1f5f9; white-space:nowrap; background:#fafafa; }
.data-table td { padding:12px 14px; border-bottom:1px solid #f8fafc; color:#374151; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tbody tr:hover td { background:#fafafa; }

.cb-cell { width:36px; }
.cb { width:15px; height:15px; accent-color:#E02424; cursor:pointer; }

.stock-badge { display:inline-flex; align-items:center; justify-content:center;
    min-width:38px; padding:2px 8px; border-radius:999px; font-size:11.5px; font-weight:700;
    background:#ecfdf5; color:#059669; }

.table-footer { padding:12px 16px; border-top:1px solid #f8fafc; display:flex; align-items:center;
    justify-content:space-between; font-size:12px; color:#9ca3af; gap:12px; flex-wrap:wrap; }
.per-page-select { border:1px solid #e5e7eb; border-radius:6px; padding:4px 24px 4px 8px; font-size:12px;
    outline:none; background:#fff; appearance:none; cursor:pointer; }
</style>

<!-- Page Header -->
<div class="breadcrumb">Produk &rsaquo; <span>List</span></div>
<div class="page-header">
    <h1 class="text-[22px] font-extrabold text-navy">Produk</h1>
    <a href="create-product.php" class="btn-primary" id="btn-new-product">
        <i class="ph-bold ph-plus text-sm"></i> New Produk
    </a>
</div>

<!-- Table -->
<div class="table-wrap">
    <!-- Toolbar -->
    <div class="table-toolbar">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search" class="mini-search" id="product-search">
        </div>
        <button class="icon-btn-sm" title="Filter">
            <i class="ph ph-funnel text-sm"></i>
        </button>
        <button class="icon-btn-sm" title="Kolom" style="color:#E02424;">
            <i class="ph-bold ph-squares-four text-sm"></i>
        </button>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="cb-cell"><input type="checkbox" class="cb" id="cb-all" onchange="toggleAll(this)"></th>
                    <th></th><!-- product image col -->
                    <th>Produk <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Harga <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Stok <i class="ph ph-caret-up-down text-[10px]"></i></th>
                    <th>Satuan</th>
                    <th>Aktif</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                <tr>
                    <td class="cb-cell"><input type="checkbox" class="cb cb-row"></td>
                    <td style="width:44px">
                        <img src="<?php echo $prod['img']; ?>" alt="product" class="w-8 h-8 rounded object-cover border border-gray-100">
                    </td>
                    <td>
                        <div class="font-bold text-[12.5px]" style="color:#E02424"><?php echo htmlspecialchars($prod['name']); ?></div>
                        <div class="text-[11px] text-gray-400"><?php echo htmlspecialchars($prod['sku']); ?></div>
                    </td>
                    <td class="font-semibold" style="color:#E02424"><?php echo $prod['price']; ?></td>
                    <td><span class="stock-badge"><?php echo $prod['stock']; ?></span></td>
                    <td class="text-gray-500"><?php echo $prod['satuan']; ?></td>
                    <td>
                        <?php if ($prod['aktif']): ?>
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full border-2 border-teal-400 text-teal-400">
                            <i class="ph ph-check text-xs"></i>
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full border-2 border-gray-300 text-gray-300">
                            <i class="ph ph-x text-xs"></i>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <button class="icon-btn-sm" title="Opsi lainnya">
                            <i class="ph ph-dots-three-vertical text-sm"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="table-footer">
        <span>Showing 1 to <?php echo count($products); ?> of <?php echo count($products); ?> results</span>
        <div class="flex items-center gap-2">
            <span>Per page</span>
            <div class="relative">
                <select class="per-page-select">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                <i class="ph ph-caret-down absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAll(master) {
        document.querySelectorAll('.cb-row').forEach(cb => cb.checked = master.checked);
    }
    document.getElementById('product-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.data-table tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
