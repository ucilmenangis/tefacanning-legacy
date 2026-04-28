<?php
/**
 * Admin Create Batch Page
 */

$pageTitle   = 'New Batch';
$currentPage = 'batches';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

require_once __DIR__ . '/../classes/BatchService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';

$batchService = new BatchService();
$activityLogService = new ActivityLogService();

// ── POST Handler ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash('error', 'Token CSRF tidak valid.');
        header('Location: create-batch.php');
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $eventName = trim($_POST['event_name'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');

    if (empty($name) || empty($eventDate)) {
        setFlash('error', 'Nama batch dan tanggal event wajib diisi.');
        header('Location: create-batch.php');
        exit;
    }

    $newId = $batchService->create([
        'name' => $name,
        'event_name' => $eventName,
        'event_date' => $eventDate,
    ]);

    $activityLogService->log('created', 'App\Models\Batch', $newId, 'created', [
        'name' => $name, 'event_date' => $eventDate,
    ]);

    setFlash('success', 'Batch berhasil ditambahkan.');
    header('Location: batches.php');
    exit;
}

include __DIR__ . '/../includes/header-admin.php';
?>



<!-- Breadcrumb & Header -->
<div class="flex items-center justify-between mb-2">
    <div>
        <div class="flex items-center gap-2 text-[12px] text-gray-400 mb-3">
            <a href="batches.php" class="hover:text-primary transition-colors">Batches</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="text-slate-600 font-medium">Create</span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">New Batch</h1>
    </div>
</div>

<form action="create-batch.php" method="POST">
    <?php echo csrfField(); ?>

    <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
        <div class="text-[14px] font-bold text-navy mb-1 flex items-center gap-2">
            <i class="ph ph-calendar-blank text-lg text-slate-400"></i>
            Informasi Batch
        </div>
        <span class="text-[11px] text-gray-400 font-medium block mb-5">Periode pre-order baru</span>

        <div class="grid grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Batch<span class="text-primary ml-0.5">*</span></label>
                <input type="text" name="name" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" placeholder="Contoh: Batch 5" required>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Event</label>
                <input type="text" name="event_name" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" placeholder="Contoh: Dies Natalis Polije">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Tanggal Event<span class="text-primary ml-0.5">*</span></label>
                <input type="date" name="event_date" class="w-full border border-gray-200 rounded-lg py-2.5 px-3.5 text-[13px] text-navy bg-white outline-none transition-all focus:border-primary" required>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-3 mt-4">
        <button type="submit" class="bg-primary text-white text-[13px] font-bold px-6 py-2.5 rounded-lg transition-colors hover:bg-dark shadow-sm shadow-red-100 border-none cursor-pointer">Create Batch</button>
        <a href="batches.php" class="inline-flex items-center justify-center bg-white border border-gray-200 text-slate-500 text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors hover:bg-gray-50 hover:text-navy">Cancel</a>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
