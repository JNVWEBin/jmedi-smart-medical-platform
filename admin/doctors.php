<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
requirePermission('doctors');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $pdo->prepare("DELETE FROM doctors WHERE doctor_id = :id")->execute([':id' => (int)$_POST['delete_id']]);
    }
    header('Location: /admin/doctors.php?msg=deleted');
    exit;
}

$pageTitle = 'Manage Doctors';
require_once __DIR__ . '/../includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$departments = getDepartments($pdo, false);
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid form submission.';
    } else {
        $data = [
            ':name' => trim($_POST['name'] ?? ''),
            ':department_id' => (int)($_POST['department_id'] ?? 0) ?: null,
            ':qualification' => trim($_POST['qualification'] ?? ''),
            ':experience' => trim($_POST['experience'] ?? ''),
            ':specialization' => trim($_POST['specialization'] ?? ''),
            ':bio' => trim($_POST['bio'] ?? ''),
            ':email' => trim($_POST['email'] ?? ''),
            ':phone' => trim($_POST['phone'] ?? ''),
            ':available_days' => trim($_POST['available_days'] ?? ''),
            ':available_time' => trim($_POST['available_time'] ?? ''),
            ':status' => (int)($_POST['status'] ?? 1),
            ':consultation_fee' => trim($_POST['consultation_fee'] ?? '') !== '' ? (float)$_POST['consultation_fee'] : null,
            ':clinic_address' => trim($_POST['clinic_address'] ?? ''),
            ':consultation_types' => trim($_POST['consultation_types'] ?? 'both'),
        ];

        $photo = null;
        if (!empty($_FILES['photo']['name'])) {
            $photo = uploadImage($_FILES['photo']);
        }

        if (empty($data[':name'])) {
            $error = 'Doctor name is required.';
        } else {
            if (isset($_POST['doctor_id']) && $_POST['doctor_id']) {
                $sql = "UPDATE doctors SET name=:name, department_id=:department_id, qualification=:qualification, experience=:experience, specialization=:specialization, bio=:bio, email=:email, phone=:phone, available_days=:available_days, available_time=:available_time, status=:status, consultation_fee=:consultation_fee, clinic_address=:clinic_address, consultation_types=:consultation_types";
                if ($photo) {
                    $sql .= ", photo=:photo";
                    $data[':photo'] = $photo;
                }
                $sql .= " WHERE doctor_id=:id";
                $data[':id'] = (int)$_POST['doctor_id'];
                $pdo->prepare($sql)->execute($data);
                $success = 'Doctor updated successfully.';
            } else {
                $data[':photo'] = $photo;
                $pdo->prepare("INSERT INTO doctors (name, photo, department_id, qualification, experience, specialization, bio, email, phone, available_days, available_time, status, consultation_fee, clinic_address, consultation_types) VALUES (:name, :photo, :department_id, :qualification, :experience, :specialization, :bio, :email, :phone, :available_days, :available_time, :status, :consultation_fee, :clinic_address, :consultation_types)")->execute($data);
                $success = 'Doctor added successfully.';
            }
            $action = 'list';
        }
    }
}

$editDoctor = null;
if ($action === 'edit' && $id) {
    $editDoctor = getDoctor($pdo, $id);
    if (!$editDoctor) {
        header('Location: /admin/doctors.php');
        exit;
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') $success = 'Doctor deleted successfully.';
$allDoctors = getDoctors($pdo, null, false);
?>

<?php if ($success): ?>
<div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="form-card">
    <h5 class="mb-4"><?= $editDoctor ? 'Edit' : 'Add New' ?> Doctor</h5>
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <?php if ($editDoctor): ?>
        <input type="hidden" name="doctor_id" value="<?= $editDoctor['doctor_id'] ?>">
        <?php endif; ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= e($editDoctor['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                    <option value="<?= $dept['department_id'] ?>" <?= ($editDoctor['department_id'] ?? '') == $dept['department_id'] ? 'selected' : '' ?>><?= e($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Qualification</label>
                <input type="text" name="qualification" class="form-control" value="<?= e($editDoctor['qualification'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Experience</label>
                <input type="text" name="experience" class="form-control" value="<?= e($editDoctor['experience'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Specialization</label>
                <input type="text" name="specialization" class="form-control" value="<?= e($editDoctor['specialization'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Photo</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                <?php if (!empty($editDoctor['photo'])): ?>
                <small class="text-muted">Current: <?= e(basename($editDoctor['photo'])) ?></small>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= e($editDoctor['email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= e($editDoctor['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Available Days</label>
                <input type="text" name="available_days" class="form-control" value="<?= e($editDoctor['available_days'] ?? '') ?>" placeholder="Mon, Tue, Wed...">
            </div>
            <div class="col-md-6">
                <label class="form-label">Available Time</label>
                <input type="text" name="available_time" class="form-control" value="<?= e($editDoctor['available_time'] ?? '') ?>" placeholder="09:00 AM - 05:00 PM">
            </div>
            <div class="col-12">
                <label class="form-label">Bio</label>
                <textarea name="bio" class="form-control" rows="4"><?= e($editDoctor['bio'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Consultation Fee</label>
                <input type="number" name="consultation_fee" class="form-control" step="0.01" min="0" value="<?= e($editDoctor['consultation_fee'] ?? '') ?>" placeholder="e.g. 500.00">
            </div>
            <div class="col-md-6">
                <label class="form-label">Consultation Types</label>
                <select name="consultation_types" class="form-select">
                    <option value="both" <?= ($editDoctor['consultation_types'] ?? 'both') === 'both' ? 'selected' : '' ?>>Both (Online & Clinic)</option>
                    <option value="online" <?= ($editDoctor['consultation_types'] ?? '') === 'online' ? 'selected' : '' ?>>Online Only</option>
                    <option value="clinic" <?= ($editDoctor['consultation_types'] ?? '') === 'clinic' ? 'selected' : '' ?>>Clinic Only</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Clinic Address</label>
                <textarea name="clinic_address" class="form-control" rows="2" placeholder="Enter clinic address"><?= e($editDoctor['clinic_address'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" <?= ($editDoctor['status'] ?? 1) == 1 ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= ($editDoctor['status'] ?? 1) == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4">Save Doctor</button>
                <a href="/admin/doctors.php" class="btn btn-secondary px-4">Cancel</a>
            </div>
        </div>
    </form>
</div>
<?php else: ?>
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">All Doctors (<?= count($allDoctors) ?>)</h5>
        <a href="/admin/doctors.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Doctor</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Specialization</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allDoctors as $doc): ?>
                <tr>
                    <td>
                        <?php if ($doc['photo']): ?>
                        <img src="<?= e($doc['photo']) ?>" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        <?php else: ?>
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="fas fa-user-md text-primary"></i></div>
                        <?php endif; ?>
                    </td>
                    <td><?= e($doc['name']) ?></td>
                    <td><?= e($doc['department_name'] ?? '–') ?></td>
                    <td><?= e($doc['specialization'] ?? '–') ?></td>
                    <td><span class="badge <?= $doc['status'] ? 'bg-success' : 'bg-secondary' ?>"><?= $doc['status'] ? 'Active' : 'Inactive' ?></span></td>
                    <td>
                        <a href="/admin/doctors.php?action=edit&id=<?= $doc['doctor_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= e(generateCSRFToken()) ?>">
                            <input type="hidden" name="delete_id" value="<?= $doc['doctor_id'] ?>">
                            <button type="button" class="btn btn-sm btn-outline-danger" data-delete-trigger data-delete-label="Dr. <?= e($doc['name']) ?>"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
