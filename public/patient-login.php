<?php
$pageTitle = 'Patient Login';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['patient_id'])) {
    header('Location: /public/patient-dashboard.php');
    exit;
}

$loginError = $registerError = $registerSuccess = '';
$activeTab = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $loginError = 'Invalid form submission. Please try again.';
    } elseif (isset($_POST['action'])) {

        if ($_POST['action'] === 'login') {
            $activeTab = 'login';
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $loginError = 'Please enter both email and password.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $loginError = 'Please enter a valid email address.';
            } else {
                $stmt = $pdo->prepare("SELECT patient_id, name, email, password FROM patients WHERE email = :email");
                $stmt->execute([':email' => $email]);
                $patient = $stmt->fetch();

                if ($patient && password_verify($password, $patient['password'])) {
                    $_SESSION['patient_id'] = $patient['patient_id'];
                    $_SESSION['patient_name'] = $patient['name'];
                    $_SESSION['patient_email'] = $patient['email'];

                    $pdo->prepare("UPDATE patients SET last_login = CURRENT_TIMESTAMP WHERE patient_id = :id")
                        ->execute([':id' => $patient['patient_id']]);

                    $_SESSION['csrf_token'] = '';
                    header('Location: /public/patient-dashboard.php');
                    exit;
                } else {
                    $loginError = 'Invalid email or password.';
                }
            }
        }

        if ($_POST['action'] === 'register') {
            $activeTab = 'register';
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
                $registerError = 'Please fill in all required fields.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $registerError = 'Please enter a valid email address.';
            } elseif (strlen($password) < 6) {
                $registerError = 'Password must be at least 6 characters.';
            } elseif ($password !== $confirmPassword) {
                $registerError = 'Passwords do not match.';
            } else {
                $existing = $pdo->prepare("SELECT patient_id FROM patients WHERE email = :email");
                $existing->execute([':email' => $email]);
                if ($existing->fetch()) {
                    $registerError = 'An account with this email already exists. Please login instead.';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO patients (name, email, phone, password) VALUES (:name, :email, :phone, :password) RETURNING patient_id");
                    $stmt->execute([
                        ':name' => $name,
                        ':email' => $email,
                        ':phone' => $phone,
                        ':password' => $hashedPassword
                    ]);
                    $newPatient = $stmt->fetch();

                    $linkStmt = $pdo->prepare("UPDATE appointments SET patient_id = :pid WHERE email = :email AND patient_id IS NULL");
                    $linkStmt->execute([':pid' => $newPatient['patient_id'], ':email' => $email]);

                    $_SESSION['patient_id'] = $newPatient['patient_id'];
                    $_SESSION['patient_name'] = $name;
                    $_SESSION['patient_email'] = $email;

                    $pdo->prepare("UPDATE patients SET last_login = CURRENT_TIMESTAMP WHERE patient_id = :id")
                        ->execute([':id' => $newPatient['patient_id']]);

                    $_SESSION['csrf_token'] = '';
                    header('Location: /public/patient-dashboard.php');
                    exit;
                }
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.patient-auth-page {
    background: linear-gradient(135deg, #f0f4ff 0%, #e8f4fd 50%, #f0fdf4 100%);
    min-height: 80vh;
    display: flex;
    align-items: center;
    padding: 60px 0;
}
.auth-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(13,110,253,0.1);
    overflow: hidden;
    max-width: 480px;
    margin: 0 auto;
    width: 100%;
}
.auth-header {
    background: linear-gradient(135deg, #0D6EFD 0%, #0a58ca 100%);
    padding: 32px 32px 24px;
    text-align: center;
    color: #fff;
}
.auth-header h3 {
    color: #fff;
    font-weight: 800;
    margin-bottom: 6px;
    font-size: 1.5rem;
}
.auth-header p {
    opacity: 0.85;
    margin: 0;
    font-size: 0.95rem;
}
.auth-tabs {
    display: flex;
    border-bottom: 2px solid #f0f0f0;
}
.auth-tabs button {
    flex: 1;
    padding: 14px;
    border: none;
    background: transparent;
    font-weight: 700;
    font-size: 0.95rem;
    color: #999;
    cursor: pointer;
    position: relative;
    transition: all 0.3s;
}
.auth-tabs button.active {
    color: #0D6EFD;
}
.auth-tabs button.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background: #0D6EFD;
    border-radius: 3px 3px 0 0;
}
.auth-body {
    padding: 28px 32px 32px;
}
.auth-body .form-control {
    border-radius: 10px;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    font-size: 0.95rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.auth-body .form-control:focus {
    border-color: #0D6EFD;
    box-shadow: 0 0 0 3px rgba(13,110,253,0.1);
}
.auth-body .input-group-text {
    border-radius: 10px 0 0 10px;
    border: 2px solid #e9ecef;
    border-right: none;
    background: #f8f9fa;
    color: #999;
}
.auth-body .input-group .form-control {
    border-radius: 0 10px 10px 0;
    border-left: none;
}
.auth-body .input-group:focus-within .input-group-text {
    border-color: #0D6EFD;
    color: #0D6EFD;
}
.auth-body .input-group:focus-within .form-control {
    border-color: #0D6EFD;
}
.btn-auth {
    background: linear-gradient(135deg, #0D6EFD 0%, #0a58ca 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 13px;
    font-size: 1rem;
    font-weight: 700;
    width: 100%;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-auth:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13,110,253,0.35);
}
.tab-panel {
    display: none;
}
.tab-panel.active {
    display: block;
}
</style>

<div class="patient-auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <div style="font-size:2.5rem;margin-bottom:10px;"><i class="fas fa-user-circle"></i></div>
                <h3>Patient Portal</h3>
                <p>Access your appointments and medical records</p>
            </div>

            <div class="auth-tabs">
                <button class="<?= $activeTab === 'login' ? 'active' : '' ?>" onclick="switchTab('login')" id="tabLogin"><i class="fas fa-sign-in-alt me-1"></i> Login</button>
                <button class="<?= $activeTab === 'register' ? 'active' : '' ?>" onclick="switchTab('register')" id="tabRegister"><i class="fas fa-user-plus me-1"></i> Register</button>
            </div>

            <div class="auth-body">
                <div id="panelLogin" class="tab-panel <?= $activeTab === 'login' ? 'active' : '' ?>">
                    <?php if ($loginError): ?>
                    <div class="alert alert-danger py-2 mb-3"><i class="fas fa-exclamation-circle me-1"></i> <?= e($loginError) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="login">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="your@email.com" required value="<?= e($_POST['action'] ?? '' === 'login' ? ($_POST['email'] ?? '') : '') ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-auth"><i class="fas fa-sign-in-alt me-2"></i>Sign In</button>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">Don't have an account? <a href="javascript:void(0)" onclick="switchTab('register')" style="color:#0D6EFD;font-weight:600;">Register here</a></small>
                    </div>
                </div>

                <div id="panelRegister" class="tab-panel <?= $activeTab === 'register' ? 'active' : '' ?>">
                    <?php if ($registerError): ?>
                    <div class="alert alert-danger py-2 mb-3"><i class="fas fa-exclamation-circle me-1"></i> <?= e($registerError) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="register">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required value="<?= e(($_POST['action'] ?? '') === 'register' ? ($_POST['name'] ?? '') : '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="your@email.com" required value="<?= e(($_POST['action'] ?? '') === 'register' ? ($_POST['email'] ?? '') : '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" name="phone" class="form-control" placeholder="+1 (555) 123-4567" value="<?= e(($_POST['action'] ?? '') === 'register' ? ($_POST['phone'] ?? '') : '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required minlength="6">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" required minlength="6">
                            </div>
                        </div>
                        <button type="submit" class="btn-auth"><i class="fas fa-user-plus me-2"></i>Create Account</button>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">Already have an account? <a href="javascript:void(0)" onclick="switchTab('login')" style="color:#0D6EFD;font-weight:600;">Login here</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('tabLogin').classList.toggle('active', tab === 'login');
    document.getElementById('tabRegister').classList.toggle('active', tab === 'register');
    document.getElementById('panelLogin').classList.toggle('active', tab === 'login');
    document.getElementById('panelRegister').classList.toggle('active', tab === 'register');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
