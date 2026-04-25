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

<style>
    .card { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
    .card-title { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .card-subtitle { font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: -12px; margin-bottom: 20px; display: block; }

    .label { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .label .required { color: #E02424; margin-left: 2px; }

    .input { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #1e293b; background: #fff; transition: all 0.2s; outline: none; }
    .input:focus { border-color: #E02424; box-shadow: 0 0 0 3px rgba(224, 36, 36, 0.05); }

    .btn-save { background: #E02424; color: #fff; font-size: 13px; font-weight: 700; padding: 10px 24px; border-radius: 8px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-save:hover { background: #9B1C1C; transform: translateY(-1px); }

    .btn-cancel { background: #fff; border: 1px solid #e2e8f0; color: #64748b; font-size: 13px; font-weight: 600; padding: 10px 20px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .btn-cancel:hover { background: #f8fafc; color: #1e293b; }

    .breadcrumb { font-size: 12px; color: #94a3b8; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .breadcrumb a { color: #94a3b8; text-decoration: none; }
    .breadcrumb a:hover { color: #E02424; }
    .breadcrumb .active { color: #475569; font-weight: 500; }
</style>

<!-- Breadcrumb & Header -->
<div class="flex items-center justify-between mb-2">
    <div>
        <div class="breadcrumb">
            <a href="batches.php">Batches</a>
            <i class="ph ph-caret-right text-[10px]"></i>
            <span class="active">Create</span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">New Batch</h1>
    </div>
</div>

<form action="create-batch.php" method="POST">
    <?php echo csrfField(); ?>

    <div class="card shadow-sm">
        <div class="card-title">
            <i class="ph ph-calendar-blank text-lg text-slate-400"></i>
            Informasi Batch
        </div>
        <span class="card-subtitle">Periode pre-order baru</span>

        <div class="grid grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <label class="label">Nama Batch<span class="required">*</span></label>
                <input type="text" name="name" class="input" placeholder="Contoh: Batch 5" required>
            </div>
            <div>
                <label class="label">Nama Event</label>
                <input type="text" name="event_name" class="input" placeholder="Contoh: Dies Natalis Polije">
            </div>
            <div>
                <label class="label">Tanggal Event<span class="required">*</span></label>
                <input type="date" name="event_date" class="input" required>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-3 mt-4">
        <button type="submit" class="btn-save shadow-sm shadow-red-100">Create Batch</button>
        <a href="batches.php" class="btn-cancel">Cancel</a>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
