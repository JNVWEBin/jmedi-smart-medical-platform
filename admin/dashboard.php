<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/admin_header.php';

if (isDoctor()) {
    $docId = $_SESSION['admin_doctor_id'] ?? 0;

    $doctorInfo = $pdo->prepare("SELECT d.*, dep.name as department_name FROM doctors d LEFT JOIN departments dep ON d.department_id = dep.department_id WHERE d.doctor_id = :id");
    $doctorInfo->execute([':id' => $docId]);
    $doctorInfo = $doctorInfo->fetch();

    $today = date('Y-m-d');
    $todayAppts = $pdo->prepare("SELECT a.*, dep.name as department_name FROM appointments a LEFT JOIN departments dep ON a.department_id = dep.department_id WHERE a.doctor_id = :d AND a.appointment_date = :t ORDER BY a.appointment_time ASC");
    $todayAppts->execute([':d' => $docId, ':t' => $today]);
    $todayAppts = $todayAppts->fetchAll();

    $stTotal = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = :d");
    $stTotal->execute([':d' => $docId]);
    $myTotalAppts = (int)$stTotal->fetchColumn();

    $stPend = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = :d AND status = 'pending'");
    $stPend->execute([':d' => $docId]);
    $myPending = (int)$stPend->fetchColumn();

    $stConf = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = :d AND status = 'confirmed'");
    $stConf->execute([':d' => $docId]);
    $myConfirmed = (int)$stConf->fetchColumn();

    $stComp = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = :d AND status = 'completed'");
    $stComp->execute([':d' => $docId]);
    $myCompleted = (int)$stComp->fetchColumn();

    $myTodayCount = count($todayAppts);

    $upcomingAppts = $pdo->prepare("SELECT a.*, dep.name as department_name FROM appointments a LEFT JOIN departments dep ON a.department_id = dep.department_id WHERE a.doctor_id = :d AND a.appointment_date >= :t AND a.status IN ('pending','confirmed') ORDER BY a.appointment_date ASC, a.appointment_time ASC LIMIT 10");
    $upcomingAppts->execute([':d' => $docId, ':t' => $today]);
    $upcomingAppts = $upcomingAppts->fetchAll();

    $mySchedule = $pdo->prepare("SELECT * FROM doctor_schedules WHERE doctor_id = :d AND is_active = 1 ORDER BY day_of_week ASC");
    $mySchedule->execute([':d' => $docId]);
    $mySchedule = $mySchedule->fetchAll();
?>

<div class="greeting-section d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <?php
        $drName = $doctorInfo['name'] ?? $_SESSION['admin_name'];
        $drName = preg_replace('/^Dr\.?\s*/i', '', $drName);
        ?>
        <h4>Welcome, Dr. <?= e($drName) ?> 👋</h4>
        <p><?= e($doctorInfo['specialization'] ?? 'Doctor') ?> — <?= e($doctorInfo['department_name'] ?? '') ?></p>
    </div>
    <div class="greeting-date">
        <i class="fas fa-calendar-alt"></i>
        <?= date('l, jS F') ?>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card highlight">
            <div class="stat-icon-wrap" style="background:rgba(255,255,255,0.15);">
                <i class="fas fa-calendar-day" style="color:#fff;"></i>
            </div>
            <div class="stat-label">Today's Appointments</div>
            <div class="stat-number"><?= $myTodayCount ?></div>
            <div class="stat-trend" style="color:rgba(255,255,255,0.8);">
                <i class="fas fa-clock"></i> <?= date('M d, Y') ?>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card">
            <div class="stat-icon-wrap" style="background:rgba(245,158,11,0.12);">
                <i class="fas fa-hourglass-half" style="color:#f59e0b;"></i>
            </div>
            <div class="stat-label">Pending</div>
            <div class="stat-number"><?= $myPending ?></div>
            <div class="stat-trend">
                <i class="fas fa-exclamation-circle text-warning"></i> awaiting review
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card">
            <div class="stat-icon-wrap" style="background:rgba(34,197,94,0.12);">
                <i class="fas fa-check-circle" style="color:var(--admin-accent);"></i>
            </div>
            <div class="stat-label">Confirmed</div>
            <div class="stat-number"><?= $myConfirmed ?></div>
            <div class="stat-trend up">
                <i class="fas fa-arrow-up"></i> active
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card">
            <div class="stat-icon-wrap" style="background:rgba(59,130,246,0.12);">
                <i class="fas fa-clipboard-check" style="color:#3b82f6;"></i>
            </div>
            <div class="stat-label">Total Patients</div>
            <div class="stat-number"><?= $myTotalAppts ?></div>
            <div class="stat-trend up">
                <i class="fas fa-check"></i> <?= $myCompleted ?> completed
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-calendar-day me-2" style="color:var(--admin-accent);"></i>Today's Schedule</h5>
                <span class="badge bg-primary" style="border-radius:8px;padding:0.4em 0.8em;"><?= $myTodayCount ?> appointment(s)</span>
            </div>
            <?php if (empty($todayAppts)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-calendar-check" style="font-size:2.5rem;opacity:0.3;"></i>
                <p class="mt-2 mb-0">No appointments scheduled for today</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todayAppts as $ta): ?>
                        <tr>
                            <td><strong><?= $ta['appointment_time'] ? date('h:i A', strtotime($ta['appointment_time'])) : '--' ?></strong></td>
                            <td><?= e($ta['patient_name']) ?></td>
                            <td><?= e($ta['phone'] ?? '-') ?></td>
                            <td><span class="badge bg-light text-dark" style="border-radius:6px;"><?= e(ucfirst($ta['consultation_type'] ?? 'in-person')) ?></span></td>
                            <td><span class="badge badge-<?= $ta['status'] ?>" style="border-radius:6px;padding:0.35em 0.65em;"><?= ucfirst($ta['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="dash-card">
            <h6><i class="fas fa-clock me-2" style="color:var(--admin-accent);"></i>My Weekly Schedule</h6>
            <?php if (empty($mySchedule)): ?>
            <p class="text-muted text-center py-3 mb-0">No schedule configured</p>
            <?php else: ?>
            <?php
            $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            foreach ($mySchedule as $sched):
                $dayLabel = $dayNames[$sched['day_of_week']] ?? '?';
            ?>
            <div class="schedule-item">
                <span class="schedule-time" style="font-size:0.7rem;"><?= $dayLabel ?></span>
                <div class="schedule-event">
                    <h6 style="font-size:0.78rem;">
                        <?= e($sched['session_label'] ?? '') ?>: <?= date('h:iA', strtotime($sched['start_time'])) ?> - <?= date('h:iA', strtotime($sched['end_time'])) ?>
                    </h6>
                    <small><?= $sched['slot_duration_minutes'] ?? 15 ?> min slots</small>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="fas fa-list-alt me-2" style="color:var(--admin-accent);"></i>Upcoming Appointments</h5>
        <a href="/admin/appointments.php" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($upcomingAppts)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No upcoming appointments</td></tr>
                <?php else: ?>
                <?php foreach ($upcomingAppts as $ua): ?>
                <tr>
                    <td><strong><?= e($ua['patient_name']) ?></strong></td>
                    <td><?= formatDate($ua['appointment_date']) ?></td>
                    <td><?= $ua['appointment_time'] ? date('h:i A', strtotime($ua['appointment_time'])) : '--' ?></td>
                    <td><?= e($ua['department_name'] ?? '-') ?></td>
                    <td><span class="badge badge-<?= $ua['status'] ?>" style="border-radius:6px;padding:0.35em 0.65em;"><?= ucfirst($ua['status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
} else {
    $totalDoctors = getCount($pdo, 'doctors');
    $totalDepts = getCount($pdo, 'departments');
    $totalAppointments = getCount($pdo, 'appointments');
    $totalPosts = getCount($pdo, 'posts');
    $totalTestimonials = getCount($pdo, 'testimonials');
    $totalSlides = getCount($pdo, 'hero_slides');

    $recentAppointments = $pdo->query("SELECT a.*, d.name as doctor_name, dep.name as department_name FROM appointments a LEFT JOIN doctors d ON a.doctor_id = d.doctor_id LEFT JOIN departments dep ON a.department_id = dep.department_id ORDER BY a.created_at DESC LIMIT 5")->fetchAll();

    $pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
    $confirmedCount = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'confirmed'")->fetchColumn();
    $cancelledCount = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'cancelled'")->fetchColumn();
    $completedCount = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'completed'")->fetchColumn();

    $monthlyData = [];
    for ($i = 11; $i >= 0; $i--) {
        $monthStart = date('Y-m-01', strtotime("-$i months"));
        $monthEnd = date('Y-m-t', strtotime("-$i months"));
        $count = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE created_at >= '$monthStart' AND created_at <= '$monthEnd 23:59:59'")->fetchColumn();
        $monthlyData[] = [
            'label' => date('M', strtotime("-$i months")),
            'count' => $count
        ];
    }

    $today = date('Y-m-d');
    $todayAppointments = $pdo->query("SELECT a.*, d.name as doctor_name FROM appointments a LEFT JOIN doctors d ON a.doctor_id = d.doctor_id WHERE a.appointment_date = '$today' ORDER BY a.appointment_time ASC LIMIT 4")->fetchAll();

    $recentPosts = $pdo->query("SELECT title, slug, author, created_at FROM posts WHERE status='published' ORDER BY created_at DESC LIMIT 3")->fetchAll();

    $dayOfWeek = date('N');
    $weekStart = date('Y-m-d', strtotime('-' . ($dayOfWeek - 1) . ' days'));
    $calDays = [];
    for ($i = 0; $i < 7; $i++) {
        $d = date('Y-m-d', strtotime("+$i days", strtotime($weekStart)));
        $calDays[] = [
            'date' => $d,
            'num' => date('j', strtotime($d)),
            'name' => strtolower(date('D', strtotime($d))),
            'active' => $d === $today
        ];
    }

    $totalUsers = getCount($pdo, 'admins');
?>

<div class="greeting-section d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4>Hello, <?= e($_SESSION['admin_name'] ?? 'Admin') ?> 👋</h4>
        <p>There is the latest update for the last 7 days, check now</p>
    </div>
    <div class="greeting-date">
        <i class="fas fa-calendar-alt"></i>
        <?= date('l, jS F') ?>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card highlight">
            <div class="stat-icon-wrap" style="background:rgba(255,255,255,0.15);">
                <i class="fas fa-calendar-check" style="color:#fff;"></i>
            </div>
            <div class="stat-label">Appointments</div>
            <div class="stat-number"><?= number_format($totalAppointments) ?></div>
            <div class="stat-trend" style="color:rgba(255,255,255,0.8);">
                <i class="fas fa-arrow-up"></i> <?= $pendingCount ?> pending
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card">
            <div class="stat-icon-wrap" style="background:rgba(34,197,94,0.12);">
                <i class="fas fa-user-md" style="color:var(--admin-accent);"></i>
            </div>
            <div class="stat-label">Active Doctors</div>
            <div class="stat-number"><?= $totalDoctors ?></div>
            <div class="stat-trend up">
                <i class="fas fa-arrow-up"></i> across <?= $totalDepts ?> depts
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card">
            <div class="stat-icon-wrap" style="background:rgba(59,130,246,0.12);">
                <i class="fas fa-hospital" style="color:#3b82f6;"></i>
            </div>
            <div class="stat-label">Departments</div>
            <div class="stat-number"><?= $totalDepts ?></div>
            <div class="stat-trend up">
                <i class="fas fa-check-circle"></i> all active
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card">
            <div class="stat-icon-wrap" style="background:rgba(139,92,246,0.12);">
                <i class="fas fa-users" style="color:#8b5cf6;"></i>
            </div>
            <div class="stat-label">Total Patients</div>
            <div class="stat-number"><?= number_format($totalAppointments) ?></div>
            <div class="stat-trend up">
                <i class="fas fa-arrow-up"></i> <?= $confirmedCount ?> confirmed
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="dash-card">
            <div class="card-header-row">
                <h6><i class="fas fa-chart-line me-2" style="color:var(--admin-accent);"></i>Patient Statistics</h6>
                <div class="card-actions">
                    <button class="tab-btn">Week</button>
                    <button class="tab-btn">Month</button>
                    <button class="tab-btn active">Year</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="patientChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="dash-card calendar-widget">
            <div class="cal-header">
                <h6><i class="fas fa-calendar me-2" style="color:var(--admin-accent);"></i>Today <?= date('jS M Y') ?></h6>
            </div>
            <div class="cal-days">
                <?php foreach ($calDays as $cd): ?>
                <div class="cal-day <?= $cd['active'] ? 'active' : '' ?>">
                    <span class="day-num"><?= $cd['num'] ?></span>
                    <span class="day-name"><?= $cd['name'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($todayAppointments)): ?>
                <?php foreach ($todayAppointments as $ta): ?>
                <div class="schedule-item">
                    <span class="schedule-time"><?= $ta['appointment_time'] ? date('H:i', strtotime($ta['appointment_time'])) : '--:--' ?></span>
                    <div class="schedule-event">
                        <h6><?= e($ta['patient_name']) ?></h6>
                        <small><?= e($ta['doctor_name'] ?? 'Doctor TBD') ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="schedule-item">
                    <span class="schedule-time">--:--</span>
                    <div class="schedule-event">
                        <h6>No appointments today</h6>
                        <small>Schedule is clear</small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-4">
        <div class="bottom-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Overview</h6>
            </div>
            <div class="text-center mb-3">
                <div class="progress-ring-wrap">
                    <svg width="90" height="90" viewBox="0 0 90 90">
                        <circle cx="45" cy="45" r="38" fill="none" stroke="#e2ebe5" stroke-width="6"/>
                        <circle cx="45" cy="45" r="38" fill="none" stroke="#22c55e" stroke-width="6"
                            stroke-dasharray="<?= $totalAppointments > 0 ? round(($confirmedCount/$totalAppointments)*238.76, 1) : 0 ?> 238.76"
                            stroke-linecap="round" transform="rotate(-90 45 45)"/>
                    </svg>
                    <span class="progress-ring-text"><?= $totalAppointments > 0 ? round(($confirmedCount/$totalAppointments)*100) : 0 ?>%</span>
                </div>
                <small class="text-muted">Confirmation Rate</small>
            </div>
            <div class="income-row">
                <span class="income-label"><i class="fas fa-check-circle me-2 text-success"></i>Confirmed</span>
                <span class="income-value"><?= $confirmedCount ?></span>
            </div>
            <div class="income-row">
                <span class="income-label"><i class="fas fa-clock me-2 text-warning"></i>Pending</span>
                <span class="income-value"><?= $pendingCount ?></span>
            </div>
            <div class="income-row">
                <span class="income-label"><i class="fas fa-clipboard-check me-2 text-primary"></i>Completed</span>
                <span class="income-value"><?= $completedCount ?></span>
            </div>
            <div class="income-row">
                <span class="income-label"><i class="fas fa-times-circle me-2 text-danger"></i>Cancelled</span>
                <span class="income-value"><?= $cancelledCount ?></span>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="bottom-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Content Stats</h6>
            </div>
            <div class="text-center mb-3">
                <div class="occupancy-num"><?= $totalPosts + $totalSlides ?></div>
                <span class="occupancy-badge"><i class="fas fa-arrow-up me-1"></i>active</span>
            </div>
            <div class="room-row">
                <span><i class="fas fa-newspaper"></i><span class="room-label">Blog Posts</span></span>
                <span class="room-value"><?= $totalPosts ?></span>
            </div>
            <div class="room-row">
                <span><i class="fas fa-images"></i><span class="room-label">Hero Slides</span></span>
                <span class="room-value"><?= $totalSlides ?></span>
            </div>
            <div class="room-row">
                <span><i class="fas fa-comments"></i><span class="room-label">Testimonials</span></span>
                <span class="room-value"><?= $totalTestimonials ?></span>
            </div>
            <div class="room-row">
                <span><i class="fas fa-users-cog"></i><span class="room-label">Admin Users</span></span>
                <span class="room-value"><?= $totalUsers ?></span>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="bottom-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Recent Posts</h6>
                <a href="/admin/blog.php" class="report-link">View all</a>
            </div>
            <?php foreach ($recentPosts as $rp): ?>
            <div class="report-item">
                <div class="report-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="report-text">
                    <h6><?= e(mb_strimwidth($rp['title'], 0, 35, '...')) ?></h6>
                    <small>by <?= e($rp['author']) ?> · <?= date('M j', strtotime($rp['created_at'])) ?></small>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($recentPosts)): ?>
            <p class="text-muted text-center py-3 mb-0">No posts yet</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($pendingCount > 0): ?>
<div class="alert d-flex align-items-center mb-4" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); border-radius: 12px; color: #92400e;">
    <i class="fas fa-exclamation-triangle me-2" style="color:#f59e0b;"></i>
    <span>You have <strong><?= $pendingCount ?></strong> pending appointment(s) awaiting review.</span>
    <a href="/admin/appointments.php?status=pending" class="btn btn-sm ms-auto" style="background:#f59e0b;color:#fff;border-radius:8px;">View Pending</a>
</div>
<?php endif; ?>

<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="fas fa-list-alt me-2" style="color:var(--admin-accent);"></i>Recent Appointments</h5>
        <a href="/admin/appointments.php" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentAppointments)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No appointments yet</td></tr>
                <?php else: ?>
                <?php foreach ($recentAppointments as $apt): ?>
                <tr>
                    <td><strong><?= e($apt['patient_name']) ?></strong></td>
                    <td><?= e($apt['doctor_name'] ?? 'Not specified') ?></td>
                    <td><?= e($apt['department_name'] ?? 'Not specified') ?></td>
                    <td><?= formatDate($apt['appointment_date']) ?></td>
                    <td><span class="badge badge-<?= $apt['status'] ?>" style="border-radius:6px;padding:0.35em 0.65em;"><?= ucfirst($apt['status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('patientChart').getContext('2d');
const labels = <?= json_encode(array_column($monthlyData, 'label')) ?>;
const data = <?= json_encode(array_column($monthlyData, 'count')) ?>;

const gradient = ctx.createLinearGradient(0, 0, 0, 250);
gradient.addColorStop(0, 'rgba(34,197,94,0.2)');
gradient.addColorStop(1, 'rgba(34,197,94,0)');

const gradient2 = ctx.createLinearGradient(0, 0, 0, 250);
gradient2.addColorStop(0, 'rgba(24,90,58,0.15)');
gradient2.addColorStop(1, 'rgba(24,90,58,0)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Appointments',
                data: data,
                borderColor: '#185a3a',
                backgroundColor: gradient2,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#185a3a',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7
            },
            {
                label: 'Trend',
                data: data.map(function(v, i) { return Math.max(0, v + Math.sin(i) * 0.5); }),
                borderColor: '#22c55e',
                backgroundColor: gradient,
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                borderDash: [5, 5]
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: { size: 12, family: 'Plus Jakarta Sans' }
                }
            },
            tooltip: {
                backgroundColor: '#185a3a',
                titleFont: { family: 'Plus Jakarta Sans', weight: '600' },
                bodyFont: { family: 'Plus Jakarta Sans' },
                cornerRadius: 10,
                padding: 12
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { size: 11, family: 'Plus Jakarta Sans' }, color: '#6b7f71' }
            },
            y: {
                grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                ticks: {
                    font: { size: 11, family: 'Plus Jakarta Sans' },
                    color: '#6b7f71',
                    stepSize: 1
                },
                beginAtZero: true
            }
        }
    }
});
</script>

<?php } ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
