<?php
$pageTitle = 'Edit Profil';
$currentPage = 'profile';
// TODO: Uncomment when auth ready
// require_once __DIR__ . '/../includes/auth.php';
// requireCustomer();
include __DIR__ . '/../includes/header-customer.php';
?>

<style>
    /* ── Form Styling ── */
    .form-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 24px;
    }
    .input-group {
        margin-bottom: 16px;
    }
    .label-text {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 6px;
    }
    .input-box {
        width: 100%;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px 10px 42px;
        font-size: 13px;
        color: #374151;
        transition: border-color 0.15s, box-shadow 0.15s;
        outline: none;
    }
    .input-box:focus {
        border-color: #E02424;
        box-shadow: 0 0 0 3px rgba(224,36,36,.08);
    }
    .input-box:disabled {
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
    }

    /* ── Alert Styling ── */
    .alert-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
    }

    /* ── Submit button ── */
    .btn-submit {
        background: white;
        border: 1px solid #e2e8f0;
        color: #1a202c;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
        box-shadow: 0 1px 2px rgba(0,0,0,.05);
    }
    .btn-submit:hover {
        background: #f9fafb;
        border-color: #cbd5e1;
    }
</style>

<!-- Page Title -->
<h1 class="text-[22px] font-bold text-navy mb-6">Edit Profil</h1>

<!-- ── Alert Box ── -->
<div class="alert-box">
    <div class="text-navy">
        <i class="ph-bold ph-warning-circle text-xl"></i>
    </div>
    <div>
        <p class="text-[14px] font-bold text-navy mb-1">Profil Tidak Dapat Diubah</p>
        <p class="text-[12px] text-gray-500 mb-2">Anda memiliki pesanan yang sedang diproses. Data profil tidak dapat diubah untuk menjaga konsistensi data pesanan. Hubungi admin jika perlu mengubah data.</p>
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Diproses</span>
            <span class="text-[11px] font-bold text-navy">ORD-NAEU8W9Z</span>
        </div>
    </div>
</div>

<!-- ── Informasi Pribadi ── -->
<div class="form-card overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2.5 mb-2">
        <i class="ph ph-user text-gray-400 text-lg"></i>
        <div>
            <p class="text-[13px] font-semibold text-navy leading-none">Informasi Pribadi</p>
            <p class="text-[11px] text-[#d97706] font-medium mt-1 inline-flex items-center gap-1">
                <i class="ph-fill ph-warning-circle"></i>
                Anda memiliki pesanan yang sedang diproses. Hubungi admin untuk mengubah data profil.
            </p>
        </div>
    </div>

    <div class="p-5 space-y-4">
        <!-- Nama Lengkap -->
        <div class="input-group">
            <label class="label-text">Nama Lengkap</label>
            <div class="relative">
                <i class="ph ph-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" class="input-box" value="Customer" disabled>
            </div>
        </div>

        <!-- Email -->
        <div class="input-group">
            <label class="label-text">Email</label>
            <div class="relative">
                <i class="ph ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="email" class="input-box" value="customer@customer.com" disabled>
            </div>
        </div>

        <!-- No. Telepon -->
        <div class="input-group">
            <label class="label-text">No. Telepon</label>
            <div class="relative">
                <i class="ph ph-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" class="input-box" value="08123456789" disabled>
            </div>
        </div>

        <!-- Organisasi -->
        <div class="input-group">
            <label class="label-text">Organisasi / Instansi</label>
            <div class="relative">
                <i class="ph ph-buildings absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" class="input-box" value="customer_organization" disabled>
            </div>
        </div>

        <!-- Alamat -->
        <div class="input-group">
            <label class="label-text">Alamat</label>
            <textarea class="w-full bg-[#f8fafc] border border-e2e8f0 rounded-lg p-3 text-[13px] text-gray-400 min-h-[80px]" disabled>alamat_testing</textarea>
        </div>
    </div>
</div>

<!-- ── Ubah Password ── -->
<div class="form-card">
    <div class="px-5 py-4 border-b border-gray-50 mb-2 flex items-center gap-2.5">
        <i class="ph ph-lock-key text-gray-400 text-lg"></i>
        <div>
            <p class="text-[13px] font-semibold text-navy leading-none">Ubah Password</p>
            <p class="text-[11px] text-gray-400 mt-1">Pastikan password baru minimal 8 karakter.</p>
        </div>
    </div>

    <div class="p-5 space-y-4">
        <!-- Password Saat Ini -->
        <div class="input-group">
            <label class="label-text">Password Saat Ini <span class="text-[#E02424]">*</span></label>
            <div class="relative">
                <i class="ph ph-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="password" class="input-box" placeholder="••••••••">
                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="ph ph-eye-slash text-base"></i>
                </button>
            </div>
        </div>

        <!-- Password Baru -->
        <div class="input-group">
            <label class="label-text">Password Baru <span class="text-[#E02424]">*</span></label>
            <div class="relative">
                <i class="ph ph-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="password" class="input-box" placeholder="••••••••">
                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="ph ph-eye-slash text-base"></i>
                </button>
            </div>
        </div>

        <!-- Konfirmasi Password Baru -->
        <div class="input-group">
            <label class="label-text">Konfirmasi Password Baru <span class="text-[#E02424]">*</span></label>
            <div class="relative">
                <i class="ph ph-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="password" class="input-box" placeholder="••••••••">
                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="ph ph-eye-slash text-base"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Submit Button ── -->
<div class="flex justify-end">
    <button type="button" class="btn-submit">
        <i class="ph ph-lock-key text-base"></i>
        Ubah Password
    </button>
</div>

<?php include __DIR__ . '/../includes/footer-customer.php'; ?>
