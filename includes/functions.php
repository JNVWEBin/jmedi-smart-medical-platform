<?php
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

function getSettings(PDO $pdo): array {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function getSetting(PDO $pdo, string $key, string $default = ''): string {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = :key");
    $stmt->execute([':key' => $key]);
    $result = $stmt->fetch();
    return $result ? ($result['setting_value'] ?? $default) : $default;
}

function updateSetting(PDO $pdo, string $key, string $value): void {
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON CONFLICT (setting_key) DO UPDATE SET setting_value = EXCLUDED.setting_value");
    $stmt->execute([':key' => $key, ':value' => $value]);
}

function getDepartments(PDO $pdo, bool $activeOnly = true): array {
    $sql = "SELECT * FROM departments";
    if ($activeOnly) $sql .= " WHERE status = 1";
    $sql .= " ORDER BY sort_order, name";
    return $pdo->query($sql)->fetchAll();
}

function getDepartment(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM departments WHERE department_id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function getDepartmentBySlug(PDO $pdo, string $slug): ?array {
    $stmt = $pdo->prepare("SELECT * FROM departments WHERE slug = :slug");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch() ?: null;
}

function getDoctors(PDO $pdo, ?int $departmentId = null, bool $activeOnly = true): array {
    $sql = "SELECT d.*, dep.name as department_name FROM doctors d LEFT JOIN departments dep ON d.department_id = dep.department_id";
    $conditions = [];
    $params = [];
    if ($activeOnly) {
        $conditions[] = "d.status = 1";
    }
    if ($departmentId) {
        $conditions[] = "d.department_id = :dept_id";
        $params[':dept_id'] = $departmentId;
    }
    if ($conditions) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    $sql .= " ORDER BY d.sort_order, d.name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getDoctor(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT d.*, dep.name as department_name FROM doctors d LEFT JOIN departments dep ON d.department_id = dep.department_id WHERE d.doctor_id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function getAppointments(PDO $pdo, ?string $status = null, ?string $search = null, ?int $doctorId = null): array {
    $sql = "SELECT a.*, d.name as doctor_name, dep.name as department_name FROM appointments a LEFT JOIN doctors d ON a.doctor_id = d.doctor_id LEFT JOIN departments dep ON a.department_id = dep.department_id";
    $conditions = [];
    $params = [];
    if ($doctorId) {
        $conditions[] = "a.doctor_id = :doctor_id";
        $params[':doctor_id'] = $doctorId;
    }
    if ($status) {
        $conditions[] = "a.status = :status";
        $params[':status'] = $status;
    }
    if ($search) {
        $conditions[] = "(a.patient_name LIKE :search OR a.email LIKE :search2 OR a.phone LIKE :search3)";
        $params[':search'] = "%$search%";
        $params[':search2'] = "%$search%";
        $params[':search3'] = "%$search%";
    }
    if ($conditions) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    $sql .= " ORDER BY a.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getPosts(PDO $pdo, bool $publishedOnly = false, int $limit = 0): array {
    $sql = "SELECT * FROM posts";
    if ($publishedOnly) $sql .= " WHERE status = 'published'";
    $sql .= " ORDER BY created_at DESC";
    $params = [];
    if ($limit > 0) {
        $sql .= " LIMIT :lim";
        $params[':lim'] = $limit;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getPost(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function getPostBySlug(PDO $pdo, string $slug): ?array {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = :slug AND status = 'published'");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch() ?: null;
}

function getTestimonials(PDO $pdo, bool $activeOnly = true): array {
    $sql = "SELECT * FROM testimonials";
    if ($activeOnly) $sql .= " WHERE status = 1";
    $sql .= " ORDER BY created_at DESC";
    return $pdo->query($sql)->fetchAll();
}

function getCount(PDO $pdo, string $table): int {
    $allowed = ['doctors', 'departments', 'appointments', 'posts', 'testimonials', 'hero_slides', 'menus', 'pages'];
    if (!in_array($table, $allowed)) return 0;
    return (int)$pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
}

function getMenus(PDO $pdo, bool $activeOnly = true): array {
    $sql = "SELECT * FROM menus";
    if ($activeOnly) $sql .= " WHERE status = 1";
    $sql .= " ORDER BY menu_order ASC, id ASC";
    try {
        return $pdo->query($sql)->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function getPage(PDO $pdo, string $slug): ?array {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE page_slug = :slug");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch() ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function getHomeSection(PDO $pdo, string $key): array {
    try {
        $stmt = $pdo->prepare("SELECT section_data FROM home_sections WHERE section_key = :key");
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();
        return $row ? json_decode($row['section_data'], true) : [];
    } catch (Exception $e) {
        return [];
    }
}

function saveHomeSection(PDO $pdo, string $key, array $data): bool {
    try {
        $json = json_encode($data);
        $upd = $pdo->prepare("UPDATE home_sections SET section_data = :data, updated_at = CURRENT_TIMESTAMP WHERE section_key = :key");
        $upd->execute([':key' => $key, ':data' => $json]);
        if ($upd->rowCount() === 0) {
            $ins = $pdo->prepare("INSERT INTO home_sections (section_key, section_data, updated_at) VALUES (:key, :data, CURRENT_TIMESTAMP)");
            $ins->execute([':key' => $key, ':data' => $json]);
        }
        return true;
    } catch (Exception $e) {
        error_log('saveHomeSection [' . $key . ']: ' . $e->getMessage());
        return false;
    }
}

function getHeroSlides(PDO $pdo, bool $activeOnly = false): array {
    $sql = "SELECT * FROM hero_slides";
    if ($activeOnly) $sql .= " WHERE status = 1";
    $sql .= " ORDER BY sort_order ASC, id ASC";
    return $pdo->query($sql)->fetchAll();
}

function getHeroSlide(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM hero_slides WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function uploadImage(array $file, string $dir = 'uploads'): ?string {
    $result = uploadImageDetailed($file, $dir);
    return (str_starts_with($result, '/')) ? $result : null;
}

/**
 * Upload an image and return the web path on success, or a human-readable error string on failure.
 */
function uploadImageDetailed(array $file, string $dir = 'uploads'): string {
    $uploadDir = __DIR__ . '/../assets/' . $dir . '/';

    if (!is_dir($uploadDir)) {
        if (!@mkdir($uploadDir, 0755, true)) {
            $err = 'Cannot create upload directory: ' . $uploadDir . ' — check server permissions.';
            error_log('[JMedi Upload] ' . $err);
            return $err;
        }
    }
    if (!is_writable($uploadDir)) {
        $err = 'Upload directory not writable: ' . $uploadDir;
        error_log('[JMedi Upload] ' . $err);
        return $err;
    }

    $allowedMimes = ['image/jpeg','image/png','image/gif','image/webp','image/x-icon','image/vnd.microsoft.icon'];
    $allowedExts  = ['jpg','jpeg','png','gif','webp','ico'];

    $phpErrMap = [
        UPLOAD_ERR_INI_SIZE   => 'File too large — exceeds PHP upload_max_filesize (' . ini_get('upload_max_filesize') . '). Ask host to increase it or use a smaller image.',
        UPLOAD_ERR_FORM_SIZE  => 'File too large — exceeds the HTML form MAX_FILE_SIZE.',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded. Please try again.',
        UPLOAD_ERR_NO_FILE    => 'No file was sent.',
        UPLOAD_ERR_NO_TMP_DIR => 'Server has no temporary folder configured.',
        UPLOAD_ERR_CANT_WRITE => 'Server failed to write to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension blocked the upload.',
    ];
    if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
        $err = $phpErrMap[$file['error']] ?? 'Unknown PHP upload error code: ' . $file['error'];
        error_log('[JMedi Upload] ' . $err . ' | file: ' . ($file['name'] ?? '?'));
        return $err;
    }

    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $err = 'No valid uploaded file found (tmp_name missing or not a real upload).';
        error_log('[JMedi Upload] ' . $err);
        return $err;
    }

    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $realMime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($realMime, $allowedMimes)) {
        $err = 'Invalid file type detected: ' . $realMime . '. Allowed: JPG, PNG, GIF, WEBP, ICO.';
        error_log('[JMedi Upload] ' . $err);
        return $err;
    }
    if ($file['size'] > 10 * 1024 * 1024) {
        $err = 'File too large (' . round($file['size'] / 1024 / 1024, 1) . ' MB). Max 10 MB.';
        error_log('[JMedi Upload] ' . $err);
        return $err;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        $ext = explode('/', $realMime)[1] ?? 'jpg';
        $ext = str_replace(['jpeg','x-icon','vnd.microsoft.icon'], ['jpg','ico','ico'], $ext);
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        error_log('[JMedi Upload] SUCCESS: ' . $filepath);
        return '/assets/' . $dir . '/' . $filename;
    }

    $err = 'move_uploaded_file() failed. Target: ' . $filepath . ' — check directory write permissions.';
    error_log('[JMedi Upload] ' . $err);
    return $err;
}

function getDoctorSchedules(PDO $pdo, int $doctorId): array {
    $stmt = $pdo->prepare("SELECT * FROM doctor_schedules WHERE doctor_id = :id ORDER BY day_of_week, session_label");
    $stmt->execute([':id' => $doctorId]);
    return $stmt->fetchAll();
}

function getDoctorSchedulesByDay(PDO $pdo, int $doctorId): array {
    $schedules = getDoctorSchedules($pdo, $doctorId);
    $byDay = [];
    foreach ($schedules as $s) {
        $byDay[$s['day_of_week']][] = $s;
    }
    return $byDay;
}

function getAvailableSlots(PDO $pdo, int $doctorId, string $date): array {
    $dayOfWeek = (int)date('w', strtotime($date));

    $stmt = $pdo->prepare("SELECT * FROM doctor_schedules WHERE doctor_id = :id AND day_of_week = :dow AND is_active = 1 ORDER BY start_time");
    $stmt->execute([':id' => $doctorId, ':dow' => $dayOfWeek]);
    $sessions = $stmt->fetchAll();

    if (empty($sessions)) return [];

    $bookedStmt = $pdo->prepare("SELECT appointment_time FROM appointments WHERE doctor_id = :id AND appointment_date = :date AND status != 'cancelled'");
    $bookedStmt->execute([':id' => $doctorId, ':date' => $date]);
    $booked = array_column($bookedStmt->fetchAll(), 'appointment_time');

    $result = [];
    foreach ($sessions as $session) {
        $slots = [];
        $start = strtotime($session['start_time']);
        $end = strtotime($session['end_time']);
        $dur = (int)$session['slot_duration_minutes'] * 60;

        while ($start < $end) {
            $timeStr = date('H:i:s', $start);
            $display = date('h:i A', $start);
            $isBooked = in_array($timeStr, $booked) || in_array(date('H:i', $start) . ':00', $booked);
            if (!$isBooked) {
                $slots[] = ['time' => $timeStr, 'display' => $display];
            }
            $start += $dur;
        }

        if (!empty($slots)) {
            $result[] = [
                'session' => $session['session_label'],
                'slots' => $slots,
                'count' => count($slots)
            ];
        }
    }
    return $result;
}

function saveDoctorSchedule(PDO $pdo, int $doctorId, int $dayOfWeek, string $sessionLabel, array $data): bool {
    try {
        $stmt = $pdo->prepare("INSERT INTO doctor_schedules (doctor_id, day_of_week, session_label, start_time, end_time, slot_duration_minutes, is_active) VALUES (:did, :dow, :label, :start, :end, :dur, :active) ON CONFLICT (doctor_id, day_of_week, session_label) DO UPDATE SET start_time = EXCLUDED.start_time, end_time = EXCLUDED.end_time, slot_duration_minutes = EXCLUDED.slot_duration_minutes, is_active = EXCLUDED.is_active");
        $stmt->execute([
            ':did' => $doctorId, ':dow' => $dayOfWeek, ':label' => $sessionLabel,
            ':start' => $data['start_time'], ':end' => $data['end_time'],
            ':dur' => $data['slot_duration'], ':active' => $data['is_active']
        ]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function deleteDoctorSchedule(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("DELETE FROM doctor_schedules WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}

function getAppointment(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT a.*, d.name as doctor_name, d.photo as doctor_photo, d.phone as doctor_phone, d.email as doctor_email, d.specialization as doctor_specialization, d.consultation_fee, dep.name as department_name FROM appointments a LEFT JOIN doctors d ON a.doctor_id = d.doctor_id LEFT JOIN departments dep ON a.department_id = dep.department_id WHERE a.appointment_id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function updateAppointment(PDO $pdo, int $id, array $data): bool {
    $fields = [];
    $params = [':id' => $id];
    $allowed = ['status', 'admin_notes', 'appointment_date', 'appointment_time', 'doctor_id', 'department_id', 'consultation_type'];
    foreach ($allowed as $field) {
        if (array_key_exists($field, $data)) {
            $fields[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }
    if (empty($fields)) return false;
    $sql = "UPDATE appointments SET " . implode(', ', $fields) . " WHERE appointment_id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function getAppointmentStats(PDO $pdo): array {
    $today      = date('Y-m-d');
    $monthStart = date('Y-m-01');
    $monthEnd   = date('Y-m-t');

    $stmtToday = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = :d");
    $stmtToday->execute([':d' => $today]);

    $stmtMonth = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date BETWEEN :start AND :end");
    $stmtMonth->execute([':start' => $monthStart, ':end' => $monthEnd]);

    $stmtStatus = $pdo->prepare("SELECT status, COUNT(*) AS cnt FROM appointments GROUP BY status");
    $stmtStatus->execute();
    $statusCounts = [];
    foreach ($stmtStatus->fetchAll() as $row) {
        $statusCounts[$row['status']] = (int)$row['cnt'];
    }

    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM appointments");

    return [
        'today'      => (int)$stmtToday->fetchColumn(),
        'pending'    => $statusCounts['pending']    ?? 0,
        'confirmed'  => $statusCounts['confirmed']  ?? 0,
        'completed'  => $statusCounts['completed']  ?? 0,
        'cancelled'  => $statusCounts['cancelled']  ?? 0,
        'this_month' => (int)$stmtMonth->fetchColumn(),
        'total'      => (int)$stmtTotal->fetchColumn(),
    ];
}

function formatDate(string $date): string {
    return date('M d, Y', strtotime($date));
}

function truncateText(string $text, int $length = 150): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}
