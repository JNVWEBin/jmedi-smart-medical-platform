<?php
$pageTitle = 'Website Settings';
require_once __DIR__ . '/../includes/admin_header.php';
requirePermission('settings');

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $settingKeys = ['site_name', 'tagline', 'phone', 'emergency_phone', 'email', 'address', 'facebook', 'twitter', 'instagram', 'linkedin', 'primary_color', 'secondary_color', 'footer_text', 'whatsapp_number'];
        foreach ($settingKeys as $key) {
            if (isset($_POST[$key])) {
                updateSetting($pdo, $key, trim($_POST[$key]));
            }
        }

        if (!empty($_FILES['frontend_logo']['name'])) {
            $logoPath = uploadImage($_FILES['frontend_logo'], 'logos');
            if ($logoPath) updateSetting($pdo, 'frontend_logo', $logoPath);
        }

        if (!empty($_FILES['footer_logo']['name'])) {
            $logoPath = uploadImage($_FILES['footer_logo'], 'logos');
            if ($logoPath) updateSetting($pdo, 'footer_logo', $logoPath);
        }

        if (!empty($_FILES['admin_logo']['name'])) {
            $logoPath = uploadImage($_FILES['admin_logo'], 'logos');
            if ($logoPath) updateSetting($pdo, 'admin_logo', $logoPath);
        }

        $success = 'Settings saved successfully.';
        $settings = getSettings($pdo);
    }
}

$s = getSettings($pdo);
?>

<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;"><i class="fas fa-check-circle me-2"></i><?= e($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="dash-card">
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <div class="row g-4">
            <div class="col-12">
                <div class="card-header-row">
                    <h6><i class="fas fa-building me-2" style="color:var(--admin-accent);"></i>General</h6>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Hospital / Site Name</label>
                <input type="text" name="site_name" class="form-control" value="<?= e($s['site_name'] ?? '') ?>" style="border-radius:10px;">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tagline</label>
                <input type="text" name="tagline" class="form-control" value="<?= e($s['tagline'] ?? '') ?>" style="border-radius:10px;">
            </div>

            <div class="col-12 mt-4">
                <div class="card-header-row">
                    <h6><i class="fas fa-image me-2" style="color:var(--admin-accent);"></i>Logo Management</h6>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Frontend Logo</label>
                <?php if (!empty($s['frontend_logo'])): ?>
                <div class="mb-2"><img src="<?= e($s['frontend_logo']) ?>" alt="Logo" style="max-height:50px;border-radius:8px;background:#f0f5f1;padding:8px;"></div>
                <?php endif; ?>
                <input type="file" name="frontend_logo" class="form-control" accept="image/*" style="border-radius:10px;">
                <small class="text-muted">Displayed in the website header. Leave empty to keep current.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Footer Logo</label>
                <?php if (!empty($s['footer_logo'])): ?>
                <div class="mb-2"><img src="<?= e($s['footer_logo']) ?>" alt="Footer Logo" style="max-height:50px;border-radius:8px;background:#0f2137;padding:8px;"></div>
                <?php endif; ?>
                <input type="file" name="footer_logo" class="form-control" accept="image/*" style="border-radius:10px;">
                <small class="text-muted">Displayed in the website footer. If empty, uses the frontend logo. Leave empty to keep current.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Admin Panel Logo</label>
                <?php if (!empty($s['admin_logo'])): ?>
                <div class="mb-2"><img src="<?= e($s['admin_logo']) ?>" alt="Logo" style="max-height:50px;border-radius:8px;background:#1a3a2a;padding:8px;"></div>
                <?php endif; ?>
                <input type="file" name="admin_logo" class="form-control" accept="image/*" style="border-radius:10px;">
                <small class="text-muted">Displayed in the admin sidebar. Leave empty to keep current.</small>
            </div>

            <div class="col-12 mt-4">
                <div class="card-header-row">
                    <h6><i class="fas fa-phone-alt me-2" style="color:var(--admin-accent);"></i>Contact Information</h6>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= e($s['phone'] ?? '') ?>" style="border-radius:10px;">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Emergency Phone</label>
                <input type="text" name="emergency_phone" class="form-control" value="<?= e($s['emergency_phone'] ?? '') ?>" style="border-radius:10px;">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="<?= e($s['email'] ?? '') ?>" style="border-radius:10px;">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Address</label>
                <input type="text" name="address" class="form-control" value="<?= e($s['address'] ?? '') ?>" style="border-radius:10px;">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold"><i class="fab fa-whatsapp text-success me-1"></i>WhatsApp Number</label>
                <input type="text" name="whatsapp_number" class="form-control" value="<?= e($s['whatsapp_number'] ?? '') ?>" placeholder="e.g. 18001234567 (no + or spaces)" style="border-radius:10px;">
                <small class="text-muted">Used for the floating WhatsApp chat button. Format: country code + number, no spaces.</small>
            </div>

            <div class="col-12 mt-4">
                <div class="card-header-row">
                    <h6><i class="fab fa-facebook me-2" style="color:var(--admin-accent);"></i>Social Media</h6>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Facebook</label>
                <input type="url" name="facebook" class="form-control" value="<?= e($s['facebook'] ?? '') ?>" style="border-radius:10px;">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Twitter</label>
                <input type="url" name="twitter" class="form-control" value="<?= e($s['twitter'] ?? '') ?>" style="border-radius:10px;">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Instagram</label>
                <input type="url" name="instagram" class="form-control" value="<?= e($s['instagram'] ?? '') ?>" style="border-radius:10px;">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">LinkedIn</label>
                <input type="url" name="linkedin" class="form-control" value="<?= e($s['linkedin'] ?? '') ?>" style="border-radius:10px;">
            </div>

            <div class="col-12 mt-4">
                <div class="card-header-row">
                    <h6><i class="fas fa-palette me-2" style="color:var(--admin-accent);"></i>Appearance</h6>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Primary Color</label>
                <input type="color" name="primary_color" class="form-control form-control-color" value="<?= e($s['primary_color'] ?? '#0D6EFD') ?>" style="border-radius:10px;height:45px;">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Secondary Color</label>
                <input type="color" name="secondary_color" class="form-control form-control-color" value="<?= e($s['secondary_color'] ?? '#20C997') ?>" style="border-radius:10px;height:45px;">
            </div>
            <div class="col-md-4"></div>
            <div class="col-12">
                <label class="form-label fw-semibold">Footer Text</label>
                <input type="text" name="footer_text" class="form-control" value="<?= e($s['footer_text'] ?? '') ?>" style="border-radius:10px;">
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:10px;font-weight:600;"><i class="fas fa-save me-2"></i>Save Settings</button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
