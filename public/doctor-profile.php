<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$doctor = getDoctor($pdo, $id);

if (!$doctor) {
    header('Location: /public/doctors.php');
    exit;
}

$pageTitle = $doctor['name'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1><?= e($doctor['name']) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/public/doctors.php">Doctors</a></li>
                <li class="breadcrumb-item active"><?= e($doctor['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5 profile-page">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="doctor-photo mb-4">
                    <?php if ($doctor['photo']): ?>
                        <img src="<?= e($doctor['photo']) ?>" alt="<?= e($doctor['name']) ?>" class="img-fluid rounded-3 w-100" style="max-height:400px;object-fit:cover;">
                    <?php else: ?>
                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="height:350px;">
                            <i class="fas fa-user-md" style="font-size:8rem;color:var(--primary);opacity:0.3;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="doctor-info">
                    <h5 class="mb-3">Contact Info</h5>
                    <ul class="list-unstyled info-list">
                        <li><i class="fas fa-envelope"></i> <?= e($doctor['email'] ?? 'N/A') ?></li>
                        <li><i class="fas fa-phone"></i> <?= e($doctor['phone'] ?? 'N/A') ?></li>
                        <li><i class="fas fa-calendar-check"></i> <?= e($doctor['available_days'] ?? 'N/A') ?></li>
                        <li><i class="fas fa-clock"></i> <?= e($doctor['available_time'] ?? 'N/A') ?></li>
                    </ul>
                    <a href="/public/appointment.php?doctor=<?= $doctor['doctor_id'] ?>" class="btn btn-primary w-100 mt-2">Book Appointment</a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="doctor-info">
                    <span class="dept-badge mb-3 d-inline-block"><?= e($doctor['department_name'] ?? '') ?></span>
                    <h3 class="mb-1"><?= e($doctor['name']) ?></h3>
                    <p class="text-muted mb-4"><?= e($doctor['qualification'] ?? '') ?></p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-briefcase-medical text-primary fs-4 mb-2 d-block"></i>
                                <small class="text-muted d-block">Experience</small>
                                <strong><?= e($doctor['experience'] ?? 'N/A') ?></strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-stethoscope text-primary fs-4 mb-2 d-block"></i>
                                <small class="text-muted d-block">Specialization</small>
                                <strong><?= e($doctor['specialization'] ?? 'N/A') ?></strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-hospital text-primary fs-4 mb-2 d-block"></i>
                                <small class="text-muted d-block">Department</small>
                                <strong><?= e($doctor['department_name'] ?? 'N/A') ?></strong>
                            </div>
                        </div>
                    </div>

                    <h5>About</h5>
                    <p class="text-muted"><?= nl2br(e($doctor['bio'] ?? 'No biography available.')) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
