<?php
/**
 * Admin Edit Batch Page
 */

$pageTitle   = 'Edit Batch';
$currentPage = 'batches';

require_once __DIR__ . '/../includes/auth.php';
Auth::admin()->requireAuth();

require_once __DIR__ . '/../classes/BatchService.php';
require_once __DIR__ . '/../classes/ActivityLogService.php';
require_once __DIR__ . '/../classes/FormatHelper.php';

$batchService = new BatchService();
$activityLogService = new ActivityLogService();

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    FlashMessage::set('error', 'ID batch tidak valid.');
    header('Location: batches.php');
    exit;
}

$batch = $batchService->getById($id);
if (!$batch) {
    FlashMessage::set('error', 'Batch tidak ditemukan.');
    header('Location: batches.php');
    exit;
}

// ── POST Handler ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfService::verify()) {
        FlashMessage::set('error', 'Token CSRF tidak valid.');
        header('Location: edit-batch.php?id=' . $id);
        exit;
    }

    // Determine action: save or delete
    if (isset($_POST['_action']) && $_POST['_action'] === 'delete') {
        $batchService->softDelete($id);
        $activityLogService->log('deleted', 'App\Models\Batch', $id, 'deleted');
        FlashMessage::set('success', 'Batch berhasil dihapus.');
        header('Location: batches.php');
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $eventName = trim($_POST['event_name'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $status = trim($_POST['status'] ?? $batch['status']);

    if (empty($name) || empty($eventDate)) {
        FlashMessage::set('error', 'Nama batch dan tanggal event wajib diisi.');
        header('Location: edit-batch.php?id=' . $id);
        exit;
    }

    // Validate status transition
    $validStatuses = ['open', 'processing', 'ready', 'closed'];
    if (!in_array($status, $validStatuses)) {
        $status = $batch['status'];
    }

    $batchService->updateById($id, [
        'name' => $name,
        'event_name' => $eventName,
        'event_date' => $eventDate,
        'status' => $status,
    ]);

    $activityLogService->log('updated', 'App\Models\Batch', $id, 'updated', [
        'name' => $name, 'status' => $status,
    ]);

    FlashMessage::set('success', 'Batch berhasil diperbarui.');
    header('Location: edit-batch.php?id=' . $id);
    exit;
}

include __DIR__ . '/../includes/header-admin.php';

$statusOptions = [
    'open'       => 'Open',
    'processing' => 'Processing',
    'ready'      => 'Ready',
    'closed'     => 'Closed',
];
?>

<style>
    .card { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
    .card-title { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .card-subtitle { font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: -12px; margin-bottom: 20px; display: block; }

    .label { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .label .required { color: #E02424; margin-left: 2px; }

    .input { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #1e293b; background: #fff; transition: all 0.2s; outline: none; }
    .input:focus { border-color: #E02424; box-shadow: 0 0 0 3px rgba(224, 36, 36, 0.05); }

    .select-wrapper { position: relative; }
    .select-wrapper i.select-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #94a3b8; pointer-events: none; }
    .select { appearance: none; cursor: pointer; padding-right: 36px !important; }

    .btn-save { background: #E02424; color: #fff; font-size: 13px; font-weight: 700; padding: 10px 24px; border-radius: 8px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-save:hover { background: #9B1C1C; transform: translateY(-1px); }

    .btn-cancel { background: #fff; border: 1px solid #e2e8f0; color: #64748b; font-size: 13px; font-weight: 600; padding: 10px 20px; border-radius: 8px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .btn-cancel:hover { background: #f8fafc; color: #1e293b; }

    .btn-delete { background: #fff; border: 1px solid #fecaca; color: #dc2626; font-size: 13px; font-weight: 600; padding: 10px 20px; border-radius: 8px; transition: all 0.2s; cursor: pointer; }
    .btn-delete:hover { background: #fef2f2; }

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
            <span class="active"><?php echo htmlspecialchars($batch['name']); ?></span>
        </div>
        <h1 class="text-[24px] font-extrabold text-navy">Edit Batch</h1>
    </div>
</div>

<form action="edit-batch.php?id=<?php echo $id; ?>" method="POST">
    <?php echo CsrfService::field(); ?>

    <div class="card shadow-sm">
        <div class="card-title">
            <i class="ph ph-calendar-blank text-lg text-slate-400"></i>
            Informasi Batch
        </div>
        <span class="card-subtitle">Edit detail batch dan status</span>

        <div class="grid grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <label class="label">Nama Batch<span class="required">*</span></label>
                <input type="text" name="name" class="input" value="<?php echo htmlspecialchars($batch['name']); ?>" required>
            </div>
            <div>
                <label class="label">Nama Event</label>
                <input type="text" name="event_name" class="input" value="<?php echo htmlspecialchars($batch['event_name']); ?>" placeholder="Contoh: Dies Natalis Polije">
            </div>
            <div>
                <label class="label">Tanggal Event<span class="required">*</span></label>
                <input type="date" name="event_date" class="input" value="<?php echo htmlspecialchars($batch['event_date']); ?>" required>
            </div>
            <div>
                <label class="label">Status</label>
                <div class="select-wrapper">
                    <select name="status" class="input select">
                        <?php foreach ($statusOptions as $val => $label): ?>
                        <option value="<?php echo $val; ?>" <?php echo $batch['status'] === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="ph ph-caret-down select-icon"></i>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block italic">Status: open → processing → ready → closed</span>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-3 mt-4">
        <button type="submit" class="btn-save shadow-sm shadow-red-100">Simpan Perubahan</button>
        <a href="batches.php" class="btn-cancel">Cancel</a>
        <button type="button" class="btn-delete ml-auto" onclick="confirmDelete()">Hapus Batch</button>
    </div>
</form>

<!-- Hidden delete form -->
<form id="delete-form" method="POST" action="edit-batch.php?id=<?php echo $id; ?>">
    <?php echo CsrfService::field(); ?>
    <input type="hidden" name="_action" value="delete">
</form>

<script>
function confirmDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus batch ini?')) {
        document.getElementById('delete-form').submit();
    }
}
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
