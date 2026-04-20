<?php
$pageTitle = 'Riwayat Pesanan';
$currentPage = 'orders';
// TODO: Uncomment when auth ready
// require_once __DIR__ . '/../includes/auth.php';
// requireCustomer();
include __DIR__ . '/../includes/header-customer.php';
?>

<style>
    /* ── Table Styling ── */
    .table-container {
        background: white;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        overflow: hidden;
    }

    /* ── Search Input ── */
    .search-input {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px 8px 36px;
        font-size: 13px;
        color: #374151;
        width: 240px;
        transition: border-color 0.15s;
        outline: none;
    }
    .search-input:focus {
        border-color: #E02424;
    }

    /* ── Status Badges ── */
    .badge-blue { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
    .badge-amber { background: #fff7ed; color: #d97706; border: 1px solid #ffedd5; }

    /* ── Pagination Select ── */
    .page-select {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 12px;
        color: #64748b;
        background: white;
        outline: none;
    }

    /* ── Action Styles ── */
    .action-link {
        transition: color 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>

<!-- Page Title -->
<h1 class="text-[22px] font-bold text-navy mb-6">Riwayat Pesanan</h1>

<!-- ── Table Section ── -->
<div class="table-container">

    <!-- Header Actions -->
    <div class="p-4 flex justify-end border-b border-gray-50">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" placeholder="Search" class="search-input">
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[13px]">
            <thead>
                <tr class="text-gray-400 border-b border-gray-50 bg-[#fafafa]">
                    <th class="px-6 py-4 font-semibold">
                        <span class="flex items-center gap-1 cursor-pointer hover:text-navy">
                            No. Pesanan <i class="ph ph-caret-down text-[10px]"></i>
                        </span>
                    </th>
                    <th class="px-5 py-4 font-semibold">
                        <span class="flex items-center gap-1 cursor-pointer hover:text-navy">
                            Batch <i class="ph ph-caret-down text-[10px]"></i>
                        </span>
                    </th>
                    <th class="px-5 py-4 font-semibold">
                        <span class="flex items-center gap-1 cursor-pointer hover:text-navy">
                            Produk <i class="ph ph-caret-down text-[10px]"></i>
                        </span>
                    </th>
                    <th class="px-5 py-4 font-semibold">
                        <span class="flex items-center gap-1 cursor-pointer hover:text-navy">
                            Total <i class="ph ph-caret-down text-[10px]"></i>
                        </span>
                    </th>
                    <th class="px-5 py-4 font-semibold uppercase text-[11px] tracking-wider">Status</th>
                    <th class="px-5 py-4 font-semibold">
                        <span class="flex items-center gap-1 cursor-pointer hover:text-navy">
                            Tanggal <i class="ph ph-caret-down text-[10px]"></i>
                        </span>
                    </th>
                    <th class="px-6 py-4 font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <!-- Row 1 -->
                <tr>
                    <td class="px-6 py-5 font-bold text-navy">ORD-NAEU8W9Z</td>
                    <td class="px-5 py-5 text-gray-500">
                        <div class="flex items-center gap-2">
                            <i class="ph-bold ph-calendar-blank text-gray-300"></i>
                            Batch 1
                        </div>
                    </td>
                    <td class="px-5 py-5 text-gray-500">2 Item</td>
                    <td class="px-5 py-5 font-bold text-[#E02424]">IDR 7,500,000.00</td>
                    <td class="px-5 py-5">
                        <span class="badge-blue px-2.5 py-1 rounded-full text-[11px] font-bold flex items-center gap-1.5 w-fit">
                            <i class="ph-fill ph-gear"></i> Diproses
                        </span>
                    </td>
                    <td class="px-5 py-5 text-gray-500">15 Feb 2026, 18:34</td>
                    <td class="px-6 py-5 text-right">
                        <a href="#" class="action-link text-gray-400 hover:text-navy">
                            <i class="ph ph-file-pdf text-base"></i> PDF
                        </a>
                    </td>
                </tr>
                <!-- Row 2 -->
                <tr>
                    <td class="px-6 py-5 font-bold text-navy">ORD-29T8TFXY</td>
                    <td class="px-5 py-5 text-gray-500">
                        <div class="flex items-center gap-2">
                            <i class="ph-bold ph-calendar-blank text-gray-300"></i>
                            Batch 1
                        </div>
                    </td>
                    <td class="px-5 py-5 text-gray-500">1 Item</td>
                    <td class="px-5 py-5 font-bold text-[#E02424]">IDR 2,500,000.00</td>
                    <td class="px-5 py-5">
                        <span class="badge-amber px-2.5 py-1 rounded-full text-[11px] font-bold flex items-center gap-1.5 w-fit">
                            <i class="ph-fill ph-clock"></i> Menunggu
                        </span>
                    </td>
                    <td class="px-5 py-5 text-gray-500">15 Feb 2026, 18:34</td>
                    <td class="px-6 py-5 text-right whitespace-nowrap">
                        <div class="flex items-center gap-4 justify-end">
                            <a href="#" class="action-link text-[#d97706] hover:text-amber-700">
                                <i class="ph ph-note-pencil text-base"></i> Edit
                            </a>
                            <a href="#" class="action-link text-gray-400 hover:text-navy">
                                <i class="ph ph-file-pdf text-base"></i> PDF
                            </a>
                            <a href="#" class="action-link text-[#E02424] hover:text-dark">
                                <i class="ph ph-trash text-base"></i> Hapus
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer / Pagination -->
    <div class="px-6 py-4 border-t border-gray-50 flex flex-wrap items-center justify-between text-[13px] text-gray-500 gap-4">
        <div>
            Showing 1 to 2 of 2 results
        </div>
        <div class="flex items-center gap-3">
            <span class="text-[12px]">Per page</span>
            <select class="page-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <i class="ph ph-caret-down text-[10px] -ml-6 pointer-events-none"></i>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer-customer.php'; ?>
