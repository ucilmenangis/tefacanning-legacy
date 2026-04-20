<?php
$pageTitle = 'Pre-Order Sarden';
$currentPage = 'preorder';
// TODO: Uncomment when auth ready
// require_once __DIR__ . '/../includes/auth.php';
// requireCustomer();
include __DIR__ . '/../includes/header-customer.php';
?>

<style>
    /* ── Form elements ── */
    .form-select,
    .form-input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 13px;
        font-family: inherit;
        color: #374151;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-select:focus,
    .form-input:focus {
        border-color: #E02424;
        box-shadow: 0 0 0 3px rgba(224,36,36,.08);
    }
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 14px;
        padding-right: 36px;
        cursor: pointer;
    }

    /* ── Section card ── */
    .section-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    /* ── Product row ── */
    .product-row {
        background: #fafafa;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 12px 14px;
        transition: outline 0.1s;
    }
    .product-row.selected {
        outline: 2px solid #E02424;
    }

    /* ── Icon button ── */
    .icon-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, color 0.15s;
        color: #9ca3af;
        cursor: pointer;
    }
    .icon-btn:hover { background: #f1f5f9; }
    .icon-btn.danger:hover { background: #fef2f2; color: #E02424; }

    /* ── Catatan accordion ── */
    #catatan-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.25s ease;
    }
    #catatan-body.open { max-height: 200px; }
    #catatan-caret { transition: transform 0.2s ease; }
    #catatan-caret.open { transform: rotate(180deg); }

    /* ── Status badges ── */
    .badge-blue   { color: #2563eb; background: #eff6ff; border: 1px solid #dbeafe; }
    .badge-amber  { color: #d97706; background: #fffbeb; border: 1px solid #fde68a; }
    .badge-green  { color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; }
    .badge-gray   { color: #6b7280; background: #f9fafb; border: 1px solid #e5e7eb; }
</style>

<!-- Page Title -->
<h1 class="text-[22px] font-bold text-navy mb-6">Pre-Order Sarden</h1>

<!-- ── Intro Card ── -->
<div class="section-card p-5 mb-4">
    <p class="text-[14px] font-semibold text-navy mb-1">Pre-Order Sarden Kaleng</p>
    <p class="text-[12px] text-gray-500 mb-3">
        Selamat datang, <span class="font-semibold text-[#E02424]">Customer</span>!
        Silakan pilih <span class="font-semibold">batch</span> produksi dan
        <span class="font-semibold">produk</span> yang tersedia untuk melakukan pre-order.
    </p>
    <div class="flex flex-wrap items-center gap-4 text-[11px] text-gray-500">
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-700 inline-block"></span>
            Min. 100 kaleng
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-700 inline-block"></span>
            Max. 3000 kaleng
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-700 inline-block"></span>
            Ambil di Kampus
        </span>
    </div>
</div>

<!-- ── Informasi Batch ── -->
<div class="section-card mb-4">
    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
        <i class="ph-bold ph-calendar-dots text-gray-300 text-base"></i>
        <div>
            <p class="text-[13px] font-semibold text-navy leading-none">Informasi Batch</p>
            <p class="text-[11px] text-[#E02424] mt-0.5">Pilih batch produksi yang tersedia untuk pre-order.</p>
        </div>
    </div>
    <div class="p-5">
        <label class="block text-[12px] font-semibold text-navy mb-1.5">
            Batch Produksi <span class="text-[#E02424]">*</span>
        </label>
        <select id="batch-select" class="form-select">
            <option value="" disabled selected>Pilih batch produksi…</option>
            <option value="1">Batch 1 — Dies Natalis Polije</option>
            <option value="2">Batch 2 — Wisuda 2026</option>
        </select>
        <p class="text-[11px] text-[#E02424] mt-1.5">
            Pilih batch produksi yang sedang dibuka untuk pre-order.
        </p>
    </div>
</div>

<!-- ── Pilih Produk ── -->
<div class="section-card mb-4">
    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
        <i class="ph-bold ph-storefront text-gray-300 text-base"></i>
        <div>
            <p class="text-[13px] font-semibold text-navy leading-none">Pilih Produk</p>
            <p class="text-[11px] text-[#E02424] mt-0.5">
                Tambahkan produk yang ingin Anda pesan. Minimal
                <strong>100</strong> kaleng dan maksimal
                <strong>3000</strong> kaleng per produk.
            </p>
        </div>
    </div>

    <div class="p-5">
        <!-- Toolbar sort / delete -->
        <div class="flex items-center gap-1 mb-3">
            <button class="icon-btn" title="Atur ulang" onclick="/* sort */">
                <i class="ph-bold ph-arrows-down-up text-xs"></i>
            </button>
            <button class="icon-btn" title="Ke atas" onclick="moveSelected('up')">
                <i class="ph-bold ph-arrow-up text-xs"></i>
            </button>
            <button class="icon-btn" title="Ke bawah" onclick="moveSelected('down')">
                <i class="ph-bold ph-arrow-down text-xs"></i>
            </button>
            <span class="flex-1"></span>
            <button class="icon-btn danger" title="Hapus baris" onclick="deleteSelected()">
                <i class="ph-bold ph-trash text-xs"></i>
            </button>
            <button class="icon-btn" title="Ciutkan" onclick="toggleProductSection()">
                <i class="ph-bold ph-caret-up text-xs" id="prod-caret"></i>
            </button>
        </div>

        <!-- Column headers -->
        <div class="grid grid-cols-12 gap-3 px-1 mb-2">
            <div class="col-span-5 text-[11px] font-semibold text-navy">
                Produk <span class="text-[#E02424]">*</span>
            </div>
            <div class="col-span-4 text-[11px] font-semibold text-navy">
                Jumlah (Kaleng) <span class="text-[#E02424]">*</span>
            </div>
            <div class="col-span-3 text-[11px] font-semibold text-navy">Subtotal</div>
        </div>

        <!-- Rows container -->
        <div id="product-rows" class="space-y-2">
            <!-- Row 1 (initial) -->
            <div class="product-row grid grid-cols-12 gap-3 items-start" onclick="selectRow(this)">
                <div class="col-span-5">
                    <select class="form-select prod-select" onchange="recalc(this)">
                        <option value="" disabled selected>Select an option</option>
                        <option value="1" data-price="22000">Sarden SIP Asin</option>
                        <option value="2" data-price="25000">Sarden SIP Saus Cabai</option>
                        <option value="3" data-price="25000">Sarden SIP Saus Tomat</option>
                    </select>
                </div>
                <div class="col-span-4">
                    <div class="relative">
                        <input type="number" value="100" min="100" max="3000"
                               class="form-input prod-qty pr-14"
                               oninput="recalc(this)">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 pointer-events-none">
                            kaleng
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5 ml-1">Min: 100, Max: 3000</p>
                </div>
                <div class="col-span-3">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-gray-400 pointer-events-none">Rp</span>
                        <input type="text" class="form-input prod-sub pl-8" readonly value="0">
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Product -->
        <button type="button" id="btn-add"
                onclick="addRow()"
                class="mt-3 flex items-center gap-1 text-[12px] font-semibold text-[#E02424] hover:text-[#9B1C1C] transition-colors">
            <i class="ph-bold ph-plus text-sm"></i>
            Tambah Produk
        </button>
    </div>
</div>

<!-- ── Catatan Tambahan ── -->
<div class="section-card mb-5">
    <button type="button" onclick="toggleCatatan()"
            class="w-full flex items-center gap-2 px-5 py-4 text-left">
        <i class="ph-bold ph-chat-dots text-gray-300 text-base"></i>
        <span class="text-[13px] font-semibold text-navy flex-1">Catatan Tambahan</span>
        <i class="ph-bold ph-caret-down text-gray-400 text-sm" id="catatan-caret"></i>
    </button>
    <div id="catatan-body">
        <div class="px-5 pb-5">
            <textarea rows="3" id="catatan-area"
                      class="form-input resize-none"
                      placeholder="Tambahkan catatan untuk pesanan Anda (opsional)…"></textarea>
        </div>
    </div>
</div>

<!-- ── Kirim Pre-Order ── -->
<div class="flex justify-end mb-8">
    <button type="button" id="btn-submit"
            onclick="submitOrder()"
            class="inline-flex items-center gap-2 bg-[#E02424] hover:bg-[#9B1C1C]
                   text-white text-[13px] font-semibold px-6 py-2.5 rounded-lg
                   transition-colors shadow-sm">
        <i class="ph-bold ph-paper-plane-tilt text-base"></i>
        Kirim Pre-Order
    </button>
</div>

<!-- ── Riwayat Pesanan ── -->
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <i class="ph-bold ph-clock-counter-clockwise text-gray-300 text-base"></i>
        <h2 class="text-[14px] font-semibold text-navy">Riwayat Pesanan Anda</h2>
    </div>

    <div class="space-y-3">
        <!-- Order 1 -->
        <div class="section-card px-5 py-4 flex items-center gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2.5 mb-1 flex-wrap">
                    <span class="text-[13px] font-semibold text-navy">ORD-NAT18W97</span>
                    <span class="badge-blue inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded-full">
                        Diproses
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[11px] text-gray-400">
                    <span>Batch: Batch 1</span>
                    <span class="text-gray-200">•</span>
                    <span>2 produk</span>
                    <span class="text-gray-200">•</span>
                    <span>15 Feb 2026 18:34</span>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-[13px] font-bold text-navy">Rp 7.500.000</p>
            </div>
        </div>

        <!-- Order 2 -->
        <div class="section-card px-5 py-4 flex items-center gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2.5 mb-1 flex-wrap">
                    <span class="text-[13px] font-semibold text-navy">ORD-2918LLXY</span>
                    <span class="badge-amber inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded-full">
                        Menunggu
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[11px] text-gray-400">
                    <span>Batch: Batch 1</span>
                    <span class="text-gray-200">•</span>
                    <span>1 produk</span>
                    <span class="text-gray-200">•</span>
                    <span>15 Feb 2026 18:34</span>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-[13px] font-bold text-navy">Rp 2.500.000</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Products lookup table (for subtotal calc)
    const PRODUCTS = {
        '1': { name: 'Sarden SIP Asin',       price: 22000 },
        '2': { name: 'Sarden SIP Saus Cabai', price: 25000 },
        '3': { name: 'Sarden SIP Saus Tomat', price: 25000 },
    };

    // ── Catatan accordion ──
    function toggleCatatan() {
        const body  = document.getElementById('catatan-body');
        const caret = document.getElementById('catatan-caret');
        body.classList.toggle('open');
        caret.classList.toggle('open');
    }

    // ── Product section collapse ──
    let prodVisible = true;
    function toggleProductSection() {
        prodVisible = !prodVisible;
        const rows  = document.getElementById('product-rows');
        const addBtn = document.getElementById('btn-add');
        const caret = document.getElementById('prod-caret');
        rows.style.display  = prodVisible ? '' : 'none';
        addBtn.style.display = prodVisible ? '' : 'none';
        caret.style.transform = prodVisible ? '' : 'rotate(180deg)';
    }

    // ── Row selection ──
    let selectedRow = null;
    function selectRow(row) {
        if (selectedRow) selectedRow.classList.remove('selected');
        selectedRow = row;
        row.classList.add('selected');
    }

    // ── Subtotal recalc ──
    function recalc(el) {
        const row  = el.closest('.product-row');
        const sel  = row.querySelector('.prod-select');
        const qty  = parseInt(row.querySelector('.prod-qty').value) || 0;
        const sub  = row.querySelector('.prod-sub');
        const prod = PRODUCTS[sel.value];
        const total = prod ? prod.price * qty : 0;
        sub.value = total > 0
            ? total.toLocaleString('id-ID')
            : '0';
    }

    // ── Add product row ──
    let rowCount = 1;
    function addRow() {
        rowCount++;
        const container = document.getElementById('product-rows');

        const optionsHtml = Object.entries(PRODUCTS).map(([id, p]) =>
            `<option value="${id}" data-price="${p.price}">${p.name}</option>`
        ).join('');

        const div = document.createElement('div');
        div.className = 'product-row grid grid-cols-12 gap-3 items-start';
        div.onclick = () => selectRow(div);
        div.innerHTML = `
            <div class="col-span-5">
                <select class="form-select prod-select" onchange="recalc(this)">
                    <option value="" disabled selected>Select an option</option>
                    ${optionsHtml}
                </select>
            </div>
            <div class="col-span-4">
                <div class="relative">
                    <input type="number" value="100" min="100" max="3000"
                           class="form-input prod-qty pr-14"
                           oninput="recalc(this)">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 pointer-events-none">
                        kaleng
                    </span>
                </div>
                <p class="text-[10px] text-gray-400 mt-0.5 ml-1">Min: 100, Max: 3000</p>
            </div>
            <div class="col-span-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-gray-400 pointer-events-none">Rp</span>
                    <input type="text" class="form-input prod-sub pl-8" readonly value="0">
                </div>
            </div>
        `;
        container.appendChild(div);
        selectRow(div);
    }

    // ── Delete selected row ──
    function deleteSelected() {
        const all = document.querySelectorAll('.product-row');
        if (all.length <= 1) return;
        if (selectedRow) {
            selectedRow.remove();
            selectedRow = null;
        }
    }

    // ── Move selected row ──
    function moveSelected(dir) {
        if (!selectedRow) return;
        const container = document.getElementById('product-rows');
        if (dir === 'up' && selectedRow.previousElementSibling) {
            container.insertBefore(selectedRow, selectedRow.previousElementSibling);
        } else if (dir === 'down' && selectedRow.nextElementSibling) {
            container.insertBefore(selectedRow.nextElementSibling, selectedRow);
        }
    }

    // ── Submit Pre-Order (demo) ──
    function submitOrder() {
        const batchSel = document.getElementById('batch-select');
        if (!batchSel.value) {
            alert('Silakan pilih batch produksi terlebih dahulu.');
            return;
        }
        const rows = document.querySelectorAll('.product-row');
        let valid = true;
        rows.forEach(row => {
            const sel = row.querySelector('.prod-select');
            if (!sel.value) valid = false;
        });
        if (!valid) {
            alert('Silakan pilih produk untuk semua baris.');
            return;
        }
        alert('Pre-Order berhasil dikirim! (Demo — belum terhubung ke backend)');
    }
</script>

<?php include __DIR__ . '/../includes/footer-customer.php'; ?>
