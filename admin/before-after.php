<?php
$pageTitle = 'Before & After Gallery';
require_once __DIR__ . '/../includes/admin_header.php';
requirePermission('content');

$success = '';
$errors  = [];
$editItem = null;
$mode = 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'] ?? '')) {

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $delId = (int)($_POST['gallery_id'] ?? 0);
        if ($delId) {
            $row = $pdo->prepare("SELECT before_image, after_image FROM before_after_gallery WHERE gallery_id = :id");
            $row->execute([':id' => $delId]);
            $old = $row->fetch();
            if ($old) {
                foreach (['before_image','after_image'] as $imgKey) {
                    if (!empty($old[$imgKey])) {
                        $abs = __DIR__ . '/..' . $old[$imgKey];
                        if (file_exists($abs)) @unlink($abs);
                    }
                }
            }
            $pdo->prepare("DELETE FROM before_after_gallery WHERE gallery_id = :id")->execute([':id' => $delId]);
            $success = 'Gallery entry deleted.';
        }
    } elseif ($action === 'save') {
        $gid           = (int)($_POST['gallery_id'] ?? 0);
        $title         = trim($_POST['title'] ?? '');
        $procedure     = trim($_POST['procedure_name'] ?? '');
        $patient_note  = trim($_POST['patient_note'] ?? '');
        $sort_order    = (int)($_POST['sort_order'] ?? 0);
        $status        = (int)($_POST['status'] ?? 1);
        $before_image  = trim($_POST['existing_before'] ?? '');
        $after_image   = trim($_POST['existing_after'] ?? '');

        if (empty($title)) $errors[] = 'Title is required.';

        if (!empty($_FILES['before_image']['name'])) {
            $res = uploadImageDetailed($_FILES['before_image'], 'gallery');
            if (substr($res, 0, 1) === '/') $before_image = $res;
            else $errors[] = 'Before image: ' . $res;
        }
        if (!empty($_FILES['after_image']['name'])) {
            $res = uploadImageDetailed($_FILES['after_image'], 'gallery');
            if (substr($res, 0, 1) === '/') $after_image = $res;
            else $errors[] = 'After image: ' . $res;
        }

        if (empty($errors)) {
            if ($gid) {
                $stmt = $pdo->prepare("UPDATE before_after_gallery SET title=:t, procedure_name=:p, before_image=:bi, after_image=:ai, patient_note=:n, sort_order=:s, status=:st WHERE gallery_id=:id");
                $stmt->execute([':t'=>$title,':p'=>$procedure,':bi'=>$before_image,':ai'=>$after_image,':n'=>$patient_note,':s'=>$sort_order,':st'=>$status,':id'=>$gid]);
                $success = 'Entry updated successfully.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO before_after_gallery (title, procedure_name, before_image, after_image, patient_note, sort_order, status) VALUES (:t,:p,:bi,:ai,:n,:s,:st)");
                $stmt->execute([':t'=>$title,':p'=>$procedure,':bi'=>$before_image,':ai'=>$after_image,':n'=>$patient_note,':s'=>$sort_order,':st'=>$status]);
                $success = 'Entry added successfully.';
            }
        } else {
            $mode = 'form';
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM before_after_gallery WHERE gallery_id = :id");
    $stmt->execute([':id' => (int)$_GET['edit']]);
    $editItem = $stmt->fetch();
    if ($editItem) $mode = 'form';
}
if (isset($_GET['add'])) {
    $mode = 'form';
}

$items = $pdo->query("SELECT * FROM before_after_gallery ORDER BY sort_order ASC, gallery_id ASC")->fetchAll();
?>

<style>
.ba-card{border-radius:14px;border:1px solid var(--admin-border);background:var(--admin-card);overflow:hidden;}
.ba-img-pair{display:grid;grid-template-columns:1fr 1fr;gap:2px;background:var(--admin-border);}
.ba-img-pair img{width:100%;height:100px;object-fit:cover;display:block;}
.ba-img-placeholder{width:100%;height:100px;display:flex;align-items:center;justify-content:center;background:var(--admin-bg);color:var(--admin-text-muted);font-size:1.5rem;}
.ba-label{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:2px 6px;background:rgba(0,0,0,.5);color:#fff;position:absolute;bottom:4px;left:4px;border-radius:4px;}
.ba-img-cell{position:relative;}
.ba-meta{padding:.8rem;}
.ba-title{font-weight:700;font-size:.92rem;margin-bottom:.15rem;}
.ba-proc{font-size:.76rem;color:var(--admin-text-muted);}
.ba-actions{display:flex;gap:.5rem;padding:.6rem .8rem;border-top:1px solid var(--admin-border);}
.upload-zone{border:2px dashed var(--admin-border);border-radius:10px;padding:1.5rem;text-align:center;cursor:pointer;transition:.2s;background:var(--admin-bg);}
.upload-zone:hover{border-color:var(--admin-accent);}
.upload-preview{max-height:120px;border-radius:8px;object-fit:contain;margin-top:.5rem;}
</style>

<?php if ($success): ?>
<div class="alert alert-success d-flex align-items-center gap-2" style="border-radius:12px;"><i class="fas fa-check-circle"></i><?= e($success) ?></div>
<?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-danger" style="border-radius:12px;"><ul class="mb-0 ps-3"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<?php if ($mode === 'form'): ?>
<div class="dash-card mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0 fw-700"><?= $editItem ? 'Edit Entry' : 'Add New Entry' ?></h5>
        <a href="/admin/before-after.php" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="fas fa-arrow-left me-1"></i>Back to List</a>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="gallery_id" value="<?= (int)($editItem['gallery_id'] ?? 0) ?>">
        <input type="hidden" name="existing_before" value="<?= e($editItem['before_image'] ?? '') ?>">
        <input type="hidden" name="existing_after"  value="<?= e($editItem['after_image']  ?? '') ?>">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="<?= e($editItem['title'] ?? '') ?>" placeholder="e.g. Rhinoplasty Transformation" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Procedure Name</label>
                <input type="text" name="procedure_name" class="form-control" value="<?= e($editItem['procedure_name'] ?? '') ?>" placeholder="e.g. Rhinoplasty">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Before Image</label>
                <?php if (!empty($editItem['before_image'])): ?>
                <div class="mb-2"><img src="<?= e($editItem['before_image']) ?>" class="upload-preview"> <small class="text-muted d-block mt-1">Current image — upload a new one to replace</small></div>
                <?php endif; ?>
                <label class="upload-zone d-block" for="before_img_input">
                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-1"></i>
                    <div class="text-muted" style="font-size:.85rem;">Click to upload Before image</div>
                    <input type="file" name="before_image" id="before_img_input" class="d-none" accept="image/*" onchange="previewImg(this,'prev-before')">
                    <img id="prev-before" class="upload-preview d-none">
                </label>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">After Image</label>
                <?php if (!empty($editItem['after_image'])): ?>
                <div class="mb-2"><img src="<?= e($editItem['after_image']) ?>" class="upload-preview"> <small class="text-muted d-block mt-1">Current image — upload a new one to replace</small></div>
                <?php endif; ?>
                <label class="upload-zone d-block" for="after_img_input">
                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-1"></i>
                    <div class="text-muted" style="font-size:.85rem;">Click to upload After image</div>
                    <input type="file" name="after_image" id="after_img_input" class="d-none" accept="image/*" onchange="previewImg(this,'prev-after')">
                    <img id="prev-after" class="upload-preview d-none">
                </label>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Patient Note / Caption <small class="text-muted">(optional)</small></label>
                <input type="text" name="patient_note" class="form-control" value="<?= e($editItem['patient_note'] ?? '') ?>" placeholder="e.g. 3 months post-op, natural results">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="<?= (int)($editItem['sort_order'] ?? 0) ?>" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="1" <?= (($editItem['status'] ?? 1) == 1) ? 'selected' : '' ?>>Active (shown on site)</option>
                    <option value="0" <?= (($editItem['status'] ?? 1) == 0) ? 'selected' : '' ?>>Inactive (hidden)</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i><?= $editItem ? 'Update Entry' : 'Add Entry' ?></button>
                <a href="/admin/before-after.php" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </div>
    </form>
</div>

<?php else: ?>

<div class="dash-card">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="mb-0 fw-700"><i class="fas fa-images me-2 text-primary"></i>Before &amp; After Gallery</h5>
            <small class="text-muted"><?= count($items) ?> entries — used on the Plastic Surgery template homepage</small>
        </div>
        <a href="/admin/before-after.php?add=1" class="btn btn-primary" style="border-radius:10px;"><i class="fas fa-plus me-2"></i>Add Entry</a>
    </div>

    <?php if (empty($items)): ?>
    <div class="text-center py-5">
        <i class="fas fa-images fa-3x text-muted mb-3" style="opacity:.3;"></i>
        <p class="text-muted">No gallery entries yet. Click "Add Entry" to get started.</p>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($items as $item): ?>
        <div class="col-md-4 col-sm-6">
            <div class="ba-card">
                <div class="ba-img-pair">
                    <div class="ba-img-cell">
                        <?php if (!empty($item['before_image'])): ?>
                        <img src="<?= e($item['before_image']) ?>" alt="Before">
                        <?php else: ?>
                        <div class="ba-img-placeholder"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                        <span class="ba-label">Before</span>
                    </div>
                    <div class="ba-img-cell">
                        <?php if (!empty($item['after_image'])): ?>
                        <img src="<?= e($item['after_image']) ?>" alt="After">
                        <?php else: ?>
                        <div class="ba-img-placeholder"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                        <span class="ba-label">After</span>
                    </div>
                </div>
                <div class="ba-meta">
                    <div class="ba-title"><?= e($item['title']) ?></div>
                    <?php if (!empty($item['procedure_name'])): ?><div class="ba-proc"><i class="fas fa-tag me-1"></i><?= e($item['procedure_name']) ?></div><?php endif; ?>
                    <?php if (!empty($item['patient_note'])): ?><div class="ba-proc mt-1"><i class="fas fa-comment-dots me-1"></i><?= e($item['patient_note']) ?></div><?php endif; ?>
                </div>
                <div class="ba-actions">
                    <a href="/admin/before-after.php?edit=<?= $item['gallery_id'] ?>" class="btn btn-sm btn-outline-primary" style="border-radius:7px;font-size:.78rem;"><i class="fas fa-pen me-1"></i>Edit</a>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this entry?')">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="gallery_id" value="<?= $item['gallery_id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" style="border-radius:7px;font-size:.78rem;"><i class="fas fa-trash me-1"></i>Delete</button>
                    </form>
                    <span class="ms-auto badge" style="font-size:.68rem;padding:.3em .65em;border-radius:6px;background:<?= $item['status'] ? '#dcfce7' : '#fee2e2' ?>;color:<?= $item['status'] ? '#166534' : '#991b1b' ?>;"><?= $item['status'] ? 'Active' : 'Inactive' ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<script>
function previewImg(input, previewId) {
    const prev = document.getElementById(previewId);
    if (input.files && input.files[0] && prev) {
        const reader = new FileReader();
        reader.onload = e => { prev.src = e.target.result; prev.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
