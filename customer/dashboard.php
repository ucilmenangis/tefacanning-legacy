<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
// TODO: Uncomment when auth ready
// require_once __DIR__ . '/../includes/auth.php';
// requireCustomer();
include __DIR__ . '/../includes/header-customer.php';
?>

<style>
    /* ── Stat underline bars ── */
    .bar-red    { background: #E02424; }
    .bar-green  { background: #10b981; }
    .bar-amber  { background: #f59e0b; }
    .bar-gray   { background: #d1d5db; }
</style>

<!-- Page Title -->
<h1 class="text-[22px] font-bold text-navy mb-6">Dashboard</h1>

<!-- ── Welcome Card ── -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-5">
    <p class="text-[15px] font-semibold text-navy mb-0.5">
        Selamat datang, Customer! 👋
    </p>
    <p class="text-[12px] text-gray-400 mb-3">
        Kelola pre-order sarden kaleng TEFA Anda dari sini.
    </p>
    <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 text-[11px] text-gray-400">
        <span class="flex items-center gap-1.5">
            <i class="ph-bold ph-building-office text-gray-300"></i>
            customer.organization
        </span>
        <span class="flex items-center gap-1.5">
            <i class="ph-bold ph-phone text-gray-300"></i>
            08123456789
        </span>
        <span class="flex items-center gap-1.5">
            <i class="ph-bold ph-envelope text-gray-300"></i>
            customer@customer.com
        </span>
        <span class="flex items-center gap-1.5">
            <i class="ph-bold ph-calendar-blank text-gray-300"></i>
            Member sejak 16 Feb 2026
        </span>
    </div>
</div>

<!-- ── Stats Row ── -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

    <!-- Total Pesanan -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 overflow-hidden">
        <div class="flex items-center gap-2 mb-3">
            <i class="ph-bold ph-bag-simple text-gray-300 text-[15px]"></i>
            <span class="text-[11px] font-medium text-gray-400">Total Pesanan</span>
        </div>
        <div class="text-[32px] font-extrabold text-navy leading-none mb-2">2</div>
        <p class="text-[11px] font-medium text-[#E02424] mb-3">Pesanan keseluruhan</p>
        <div class="h-[2px] rounded-full bg-gradient-to-r from-[#E02424] to-transparent opacity-20"></div>
        <div class="mt-1 h-[2px] w-10 rounded-full bar-red opacity-60"></div>
    </div>

    <!-- Total Belanja -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 overflow-hidden">
        <div class="flex items-center gap-2 mb-3">
            <i class="ph-bold ph-currency-circle-dollar text-gray-300 text-[15px]"></i>
            <span class="text-[11px] font-medium text-gray-400">Total Belanja</span>
        </div>
        <div class="text-[22px] font-extrabold text-navy leading-none mb-2">
            Rp 10.000.000
        </div>
        <p class="text-[11px] font-medium text-emerald-500 mb-3">Akumulasi pengeluaran</p>
        <div class="h-[2px] rounded-full bg-gradient-to-r from-emerald-400 to-transparent opacity-20"></div>
        <div class="mt-1 h-[2px] w-14 rounded-full bar-green opacity-60"></div>
    </div>

    <!-- Menunggu Konfirmasi -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 overflow-hidden">
        <div class="flex items-center gap-2 mb-3">
            <i class="ph-bold ph-clock text-gray-300 text-[15px]"></i>
            <span class="text-[11px] font-medium text-gray-400">Menunggu Konfirmasi</span>
        </div>
        <div class="text-[32px] font-extrabold text-navy leading-none mb-2">1</div>
        <p class="text-[11px] font-medium text-amber-500 mb-3">Pesanan belum diproses</p>
        <div class="h-[2px] rounded-full bg-gradient-to-r from-amber-400 to-transparent opacity-20"></div>
        <div class="mt-1 h-[2px] w-8 rounded-full bar-amber opacity-60"></div>
    </div>

    <!-- Siap Diambil -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 overflow-hidden">
        <div class="flex items-center gap-2 mb-3">
            <i class="ph-bold ph-package text-gray-300 text-[15px]"></i>
            <span class="text-[11px] font-medium text-gray-400">Siap Diambil</span>
        </div>
        <div class="text-[32px] font-extrabold text-navy leading-none mb-2">0</div>
        <p class="text-[11px] font-medium text-gray-400 mb-3">Belum ada pesanan siap</p>
        <div class="h-[2px] rounded-full bg-gradient-to-r from-gray-300 to-transparent opacity-20"></div>
        <div class="mt-1 h-[2px] w-4 rounded-full bar-gray opacity-60"></div>
    </div>

</div>

<!-- ── Bottom: Batch + Products ── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    <!-- Batch Produksi Terbaru -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
            <i class="ph-bold ph-calendar-dots text-gray-300 text-base"></i>
            <h2 class="text-[13px] font-semibold text-navy">Batch Produksi Terbaru</h2>
        </div>

        <div class="p-5">
            <div class="bg-gray-50 rounded-xl border border-gray-100 p-4">
                <!-- Batch header -->
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[13px] font-semibold text-navy">Batch 1</span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold
                                 text-emerald-600 bg-emerald-50 border border-emerald-100
                                 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                        Buka
                    </span>
                </div>

                <!-- Info grid -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Acara</p>
                        <p class="text-[12px] font-semibold text-[#E02424]">Dies Natalis Polije</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Tanggal</p>
                        <p class="text-[12px] font-semibold text-navy">15 Feb 2026</p>
                    </div>
                </div>

                <!-- Order count -->
                <a href="<?php echo $basePath; ?>/preorder.php"
                   class="inline-flex items-center gap-1.5 text-[11px] font-medium text-gray-500 hover:text-[#E02424] transition-colors">
                    <i class="ph-bold ph-bag-simple text-sm"></i>
                    2 pesanan masuk
                </a>
            </div>
        </div>
    </div>

    <!-- Produk Tersedia -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
            <i class="ph-bold ph-storefront text-gray-300 text-base"></i>
            <h2 class="text-[13px] font-semibold text-navy">Produk Tersedia</h2>
        </div>

        <div class="divide-y divide-gray-50">

            <!-- Product 1 -->
            <div class="flex items-center gap-3 px-5 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-gray-50 border border-gray-100
                            flex items-center justify-center flex-shrink-0">
                    <i class="ph-bold ph-gear text-gray-300 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12px] font-semibold text-[#E02424] truncate">Sarden SIP Asin</p>
                    <p class="text-[10px] text-gray-400">TEFA-ASN-001</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-[12px] font-bold text-navy">Rp 22.000</p>
                    <p class="text-[10px] text-gray-400">per kaleng</p>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="flex items-center gap-3 px-5 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-gray-50 border border-gray-100
                            flex items-center justify-center flex-shrink-0">
                    <i class="ph-bold ph-gear text-gray-300 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12px] font-semibold text-[#E02424] truncate">Sarden SIP Saus Cabai</p>
                    <p class="text-[10px] text-gray-400">TEFA-SSC-001</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-[12px] font-bold text-navy">Rp 25.000</p>
                    <p class="text-[10px] text-gray-400">per kaleng</p>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="flex items-center gap-3 px-5 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-gray-50 border border-gray-100
                            flex items-center justify-center flex-shrink-0">
                    <i class="ph-bold ph-gear text-gray-300 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12px] font-semibold text-[#E02424] truncate">Sarden SIP Saus Tomat</p>
                    <p class="text-[10px] text-gray-400">TEFA-SST-001</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-[12px] font-bold text-navy">Rp 25.000</p>
                    <p class="text-[10px] text-gray-400">per kaleng</p>
                </div>
            </div>

        </div>
    </div>

</div><!-- /bottom grid -->

<?php include __DIR__ . '/../includes/footer-customer.php'; ?>
