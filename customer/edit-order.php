<?php
$pageTitle = 'Edit Pesanan';
$currentPage = 'orders';
// TODO: Uncomment when auth ready
// require_once __DIR__ . '/../includes/auth.php';
// requireCustomer();
include __DIR__ . '/../includes/header-customer.php';

// Mock data for the specific order in the image
$order = [
    'number' => 'ORD-29T8TFXY',
    'status' => 'Menunggu',
    'created_at' => '15 Feb 2026, 18:34',
    'batch' => 'Batch 1 — Dies Natalis Polije',
];
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
    .form-select:disabled {
        background-color: #f9fafb;
        cursor: not-allowed;
        color: #9ca3af;
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
        margin-bottom: 12px;
        transition: outline 0.1s;
        cursor: pointer;
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
</style>

<!-- Page Title -->
<h1 class="text-[22px] font-bold text-navy mb-6">Edit Pesanan</h1>

<!-- ── Edit Info Card ── -->
<div class="section-card p-5 mb-4">
    <div class="flex gap-4">
        <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
            <i class="ph ph-note-pencil text-gray-400 text-xl"></i>
        </div>
        <div>
            <p class="text-[14px] font-bold text-navy mb-1">Edit Pesanan</p>
            <p class="text-[12px] text-gray-500 mb-4 leading-relaxed">
                Mengubah pesanan <span class="font-bold text-navy"><?php echo $order['number']; ?></span>. Anda dapat mengubah produk dan jumlah pesanan, tetapi tidak dapat mengubah batch produksi.
            </p>
            <div class="flex flex-wrap items-center gap-4 text-[11px]">
                <span class="flex items-center gap-1.5">
                    <i class="ph-fill ph-clock text-amber-500"></i>
                    <span class="text-gray-500">Status:</span>
                    <span class="badge-amber px-2 py-0.5 rounded-full font-semibold"><?php echo $order['status']; ?></span>
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="ph ph-calendar text-gray-400"></i>
                    <span class="text-gray-500">Dibuat:</span>
                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-semibold"><?php echo $order['created_at']; ?></span>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- ── Informasi Batch ── -->
<div class="section-card mb-4">
    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
        <i class="ph ph-calendar-blank text-gray-300 text-lg"></i>
        <div>
            <p class="text-[13px] font-semibold text-navy leading-none">Informasi Batch</p>
            <p class="text-[11px] text-gray-400 mt-1">Batch produksi tidak dapat diubah.</p>
        </div>
    </div>
    <div class="p-5">
        <label class="block text-[12px] font-semibold text-navy mb-1.5">
            Batch Produksi
        </label>
        <select class="form-select" disabled>
            <option selected><?php echo $order['batch']; ?></option>
        </select>
        <p class="text-[11px] text-gray-400 mt-1.5">
            Batch tidak dapat diubah setelah pesanan dibuat.
        </p>
    </div>
</div>

<!-- ── Ubah Produk ── -->
<div class="section-card mb-4">
    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
        <i class="ph ph-storefront text-gray-300 text-lg"></i>
        <div>
            <p class="text-[13px] font-semibold text-navy leading-none">Ubah Produk</p>
            <p class="text-[11px] text-gray-400 mt-1">
                Anda dapat mengubah produk dan jumlah pesanan. Min 100, Max 3000 kaleng per produk.
            </p>
        </div>
    </div>

    <div class="p-5">
        <!-- Toolbar sort / delete -->
        <div class="flex items-center gap-1 mb-3">
            <button class="icon-btn" title="Atur ulang">
                <i class="ph-bold ph-arrows-down-up text-xs"></i>
            </button>
            <button class="icon-btn" title="Ke atas">
                <i class="ph-bold ph-arrow-up text-xs"></i>
            </button>
            <button class="icon-btn" title="Ke bawah">
                <i class="ph-bold ph-arrow-down text-xs"></i>
            </button>
            <span class="flex-1"></span>
            <button class="icon-btn danger" title="Hapus baris" onclick="deleteSelected()">
                <i class="ph-bold ph-trash text-xs"></i>
            </button>
            <button class="icon-btn" onclick="toggleProductSection()">
                <i class="ph-bold ph-caret-up text-xs" id="prod-caret"></i>
            </button>
        </div>

        <!-- Rows container -->
        <div id="product-rows" class="space-y-0">
            <!-- Row 1 -->
            <div class="product-row selected" onclick="selectRow(this)">
                <div class="flex items-center gap-2 mb-3 text-[11px] text-gray-400 font-semibold uppercase tracking-wider">
                    <i class="ph ph-arrows-down-up"></i>
                    <i class="ph ph-arrow-up"></i>
                    <span class="row-label">Sarden SIP Saus Tomat x 100</span>
                    <span class="flex-1"></span>
                    <i class="ph ph-trash text-red-400 cursor-pointer hover:text-red-600" onclick="deleteThisRow(event, this)"></i>
                    <i class="ph ph-caret-up"></i>
                </div>
                
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-5">
                        <label class="block text-[11px] font-bold text-navy mb-1.5 flex items-center gap-1">
                            Produk<span class="text-red-500">*</span>
                        </label>
                        <select class="form-select prod-select" onchange="recalc(this)">
                            <option value="3" data-price="25000" selected>Sarden SIP Saus Tomat — Rp 25.000/kaleng</option>
                            <option value="1" data-price="22000">Sarden SIP Asin — Rp 22.000/kaleng</option>
                            <option value="2" data-price="25000">Sarden SIP Saus Cabai — Rp 25.000/kaleng</option>
                        </select>
                    </div>
                    <div class="col-span-4">
                        <label class="block text-[11px] font-bold text-navy mb-1.5">
                            Jumlah (Kaleng)<span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" value="100" min="100" max="3000"
                                   class="form-input prod-qty pr-14"
                                   oninput="recalc(this)">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 pointer-events-none">
                                kaleng
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1 ml-1 font-medium">Min: 100, Max: 3000</p>
                    </div>
                    <div class="col-span-3">
                        <label class="block text-[11px] font-bold text-navy mb-1.5">Subtotal</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-gray-400 pointer-events-none">Rp</span>
                            <input type="text" class="form-input prod-sub pl-8 bg-gray-50" readonly value="2.500.000,00">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Product -->
        <div class="flex justify-center mt-6">
            <button type="button" id="btn-add"
                    onclick="addRow()"
                    class="inline-flex items-center gap-1.5 text-[11px] font-bold px-4 py-1.5 rounded-full border border-gray-200 text-navy hover:bg-gray-50 transition-colors">
                <i class="ph ph-plus text-sm"></i>
                Tambah Produk
            </button>
        </div>
    </div>
</div>

<!-- ── Catatan Tambahan ── -->
<div class="section-card mb-5 text-gray-400">
    <button type="button" onclick="toggleCatatan()"
            class="w-full flex items-center gap-2 px-5 py-4 text-left">
        <i class="ph ph-chat-dots text-lg"></i>
        <span class="text-[13px] font-semibold text-navy flex-1">Catatan Tambahan</span>
        <i class="ph-bold ph-caret-down text-sm" id="catatan-caret"></i>
    </button>
    <div id="catatan-body">
        <div class="px-5 pb-5">
            <textarea rows="3" id="catatan-area"
                      class="form-input resize-none"
                      placeholder="Tambahkan catatan untuk pesanan Anda (opsional)…"></textarea>
        </div>
    </div>
</div>

<!-- ── Form Actions ── -->
<div class="flex items-center justify-between mb-10">
    <a href="orders.php" class="inline-flex items-center gap-1.5 text-[13px] text-gray-400 hover:text-navy transition-colors">
        <i class="ph ph-arrow-left"></i>
        Kembali ke Riwayat
    </a>
    
    <button type="button" 
            onclick="updateOrder()"
            class="inline-flex items-center gap-2 bg-[#d87a07] hover:bg-amber-700
                   text-white text-[13px] font-bold px-6 py-2.5 rounded-lg
                   transition-colors shadow-sm">
        <i class="ph-fill ph-check-circle text-base"></i>
        Simpan Perubahan
    </button>
</div>

<script>
    const PRODUCTS = {
        '1': { name: 'Sarden SIP Asin',       price: 22000 },
        '2': { name: 'Sarden SIP Saus Cabai', price: 25000 },
        '3': { name: 'Sarden SIP Saus Tomat', price: 25000 },
    };

    function toggleCatatan() {
        const body  = document.getElementById('catatan-body');
        const caret = document.getElementById('catatan-caret');
        body.classList.toggle('open');
        caret.classList.toggle('open');
    }

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

    let selectedRowElement = document.querySelector('.product-row.selected');
    function selectRow(row) {
        if (selectedRowElement) selectedRowElement.classList.remove('selected');
        selectedRowElement = row;
        row.classList.add('selected');
    }

    function recalc(el) {
        const row  = el.closest('.product-row');
        const sel  = row.querySelector('.prod-select');
        const qty  = parseInt(row.querySelector('.prod-qty').value) || 0;
        const sub  = row.querySelector('.prod-sub');
        const prod = PRODUCTS[sel.value];
        const total = prod ? prod.price * qty : 0;
        sub.value = total.toLocaleString('id-ID', { minimumFractionDigits: 2 });
        
        // Update header label
        const label = row.querySelector('.row-label');
        if (label && prod) {
            label.textContent = `${prod.name} x ${qty}`;
        }
    }

    function addRow() {
        const container = document.getElementById('product-rows');
        const template = container.querySelector('.product-row');
        if (template) {
            const clone = template.cloneNode(true);
            clone.classList.remove('selected');
            clone.querySelectorAll('input').forEach(input => {
                if (input.type === 'number') input.value = 100;
                else if (!input.readOnly) input.value = '';
            });
            clone.querySelector('.prod-sub').value = '2.500.000,00';
            clone.querySelector('.row-label').textContent = 'Pilih Produk x 100';
            container.appendChild(clone);
            selectRow(clone);
        }
    }

    function deleteSelected() {
        if (selectedRowElement) {
            const all = document.querySelectorAll('.product-row');
            if (all.length <= 1) return;
            selectedRowElement.remove();
            selectedRowElement = null;
        }
    }

    function deleteThisRow(event, el) {
        event.stopPropagation();
        const row = el.closest('.product-row');
        const all = document.querySelectorAll('.product-row');
        if (all.length <= 1) return;
        if (row === selectedRowElement) selectedRowElement = null;
        row.remove();
    }

    function updateOrder() {
        alert('Perubahan berhasil disimpan! (Demo — belum terhubung ke backend)');
        window.location.href = 'orders.php';
    }
</script>

<?php include __DIR__ . '/../includes/footer-customer.php'; ?>
