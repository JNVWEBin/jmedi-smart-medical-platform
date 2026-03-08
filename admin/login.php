<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid form submission.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (loginAdmin($pdo, $username, $password)) {
            header('Location: /admin/dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – JMedi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0D6EFD, #20C997); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 420px; width: 100%; background: white; border-radius: 20px; padding: 3rem; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .login-card h2 { font-weight: 700; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h2><span class="text-primary">J</span><span class="text-success">Medi</span></h2>
            <p class="text-muted">Admin Panel Login</p>
        </div>
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <?= csrfField() ?>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Login</button>
        </form>
        <p class="text-center text-muted small mt-3">Powered by JNVWeb</p>
    </div>
</body>
</html>
