<?php
// Plastic Surgery & Aesthetics Homepage Template
// Used when site_template = 'plastic_surgery'
// All data variables are already loaded in public/index.php

$ps_siteName  = $settings['site_name'] ?? 'Praveen Plastic Surgery Centre';
$ps_phone     = $settings['phone'] ?? '';
$ps_email     = $settings['email'] ?? '';
$ps_address   = $settings['address'] ?? '';
$ps_logo      = $settings['frontend_logo'] ?? '';

// Primary surgeon — first doctor in DB
$ps_surgeon   = !empty($doctors) ? $doctors[0] : null;
?>

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ============================================================
   PLASTIC SURGERY TEMPLATE  (.ps-*)
   Palette: navy #0c1a2e | gold #c9a777 | cream #faf8f4
   ============================================================ */
:root {
    --ps-navy:  #0c1a2e;
    --ps-navy2: #112240;
    --ps-gold:  #c9a777;
    --ps-gold2: #b08a5b;
    --ps-cream: #faf8f4;
    --ps-text:  #1a1a2e;
    --ps-muted: #6b7280;
    --ps-white: #ffffff;
}
.ps-font-serif  { font-family: 'Cormorant Garamond', Georgia, serif; }
.ps-font-sans   { font-family: 'Plus Jakarta Sans', sans-serif; }

/* ---- HERO ---- */
.ps-hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: var(--ps-navy);
}
.ps-hero-bg {
    position: absolute; inset: 0;
    background-size: cover;
    background-position: center top;
    opacity: .35;
}
.ps-hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(120deg, rgba(12,26,46,.92) 40%, rgba(12,26,46,.65) 100%);
}
.ps-hero-content {
    position: relative;
    z-index: 2;
    padding: 120px 0 80px;
    color: #fff;
}
.ps-eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: .78rem; font-weight: 700;
    letter-spacing: .18em; text-transform: uppercase;
    color: var(--ps-gold); margin-bottom: 1.25rem;
}
.ps-eyebrow::before, .ps-eyebrow::after {
    content: ''; width: 28px; height: 1px; background: var(--ps-gold); display: block;
}
.ps-hero h1 {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(2.8rem, 6vw, 5rem);
    font-weight: 600; line-height: 1.1;
    color: #fff; margin-bottom: .75rem;
}
.ps-hero h1 em { color: var(--ps-gold); font-style: italic; }
.ps-hero-sub {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.05rem; color: rgba(255,255,255,.7);
    margin-bottom: 2.25rem; max-width: 520px;
}
.ps-btn-gold {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--ps-gold); color: #0c1a2e;
    font-weight: 700; font-size: .9rem;
    border: none; border-radius: 50px;
    padding: .75rem 2rem; text-decoration: none;
    transition: background .2s, transform .2s;
}
.ps-btn-gold:hover { background: var(--ps-gold2); color: #0c1a2e; transform: translateY(-2px); }
.ps-btn-outline {
    display: inline-flex; align-items: center; gap: 8px;
    background: transparent; color: #fff;
    font-weight: 600; font-size: .9rem;
    border: 1.5px solid rgba(255,255,255,.4);
    border-radius: 50px; padding: .72rem 1.8rem;
    text-decoration: none;
    transition: border-color .2s, background .2s;
}
.ps-btn-outline:hover { border-color: var(--ps-gold); color: var(--ps-gold); }
.ps-hero-deco {
    position: absolute; right: -60px; top: 50%;
    transform: translateY(-50%);
    width: 520px; height: 520px;
    border-radius: 50%;
    border: 1px solid rgba(201,167,119,.15);
    pointer-events: none;
}
.ps-hero-deco::before {
    content: ''; position: absolute;
    inset: 40px; border-radius: 50%;
    border: 1px solid rgba(201,167,119,.10);
}

/* ---- TRUST STRIP ---- */
.ps-trust-strip {
    background: var(--ps-navy2);
    padding: 2.5rem 0;
    border-top: 1px solid rgba(201,167,119,.15);
    border-bottom: 1px solid rgba(201,167,119,.15);
}
.ps-trust-item {
    text-align: center; padding: 0 1rem;
    border-right: 1px solid rgba(255,255,255,.08);
}
.ps-trust-item:last-child { border-right: none; }
.ps-trust-num {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2.4rem; font-weight: 700;
    color: var(--ps-gold); line-height: 1;
    margin-bottom: .2rem;
}
.ps-trust-lbl {
    font-size: .78rem; font-weight: 600;
    color: rgba(255,255,255,.55);
    text-transform: uppercase; letter-spacing: .08em;
}

/* ---- SECTION COMMONS ---- */
.ps-section { padding: 5.5rem 0; }
.ps-section-light { background: var(--ps-cream); }
.ps-section-dark  { background: var(--ps-navy); }
.ps-section-white { background: #fff; }
.ps-label {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: .73rem; font-weight: 800;
    letter-spacing: .16em; text-transform: uppercase;
    color: var(--ps-gold); margin-bottom: .85rem;
}
.ps-label::before { content:''; width:22px; height:1.5px; background:var(--ps-gold); }
.ps-heading {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(2rem, 3.5vw, 2.8rem);
    font-weight: 600; line-height: 1.2;
    color: var(--ps-navy); margin-bottom: 1rem;
}
.ps-heading-light { color: #fff; }
.ps-sub {
    font-size: .95rem; color: var(--ps-muted);
    max-width: 560px; line-height: 1.8;
}
.ps-sub-light { color: rgba(255,255,255,.6); }

/* ---- ABOUT SURGEON ---- */
.ps-surgeon-img-wrap {
    position: relative; border-radius: 20px; overflow: hidden;
}
.ps-surgeon-img { width: 100%; max-height: 480px; object-fit: cover; object-position: top; display: block; border-radius: 20px; }
.ps-surgeon-badge {
    position: absolute; bottom: -24px; right: 24px;
    background: var(--ps-gold); color: #0c1a2e;
    border-radius: 16px; padding: 1rem 1.25rem;
    text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,.18);
    min-width: 110px;
}
.ps-surgeon-badge-num { font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:700; line-height:1; }
.ps-surgeon-badge-lbl { font-size:.65rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
.ps-gold-line { width: 48px; height: 3px; background: var(--ps-gold); border-radius: 2px; margin: 1rem 0; }
.ps-surgeon-name { font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:700; color:var(--ps-navy); }
.ps-surgeon-spec { font-size:.85rem; color:var(--ps-gold); font-weight:700; letter-spacing:.06em; text-transform:uppercase; margin-bottom:.75rem; }
.ps-surgeon-bio { font-size:.95rem; color:var(--ps-muted); line-height:1.85; margin-bottom:1.5rem; }

/* ---- PROCEDURES ---- */
.ps-proc-card {
    background: #fff; border-radius: 18px;
    border: 1px solid #ede8e0;
    padding: 1.8rem 1.5rem;
    transition: transform .25s, box-shadow .25s, border-color .25s;
    height: 100%;
}
.ps-proc-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 50px rgba(12,26,46,.1);
    border-color: var(--ps-gold);
}
.ps-proc-icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: linear-gradient(135deg, rgba(201,167,119,.18), rgba(176,138,91,.08));
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: var(--ps-gold2);
    margin-bottom: 1rem;
}
.ps-proc-name { font-family:'Cormorant Garamond',serif; font-size:1.25rem; font-weight:600; color:var(--ps-navy); margin-bottom:.4rem; }
.ps-proc-desc { font-size:.82rem; color:var(--ps-muted); line-height:1.7; }

/* ---- BEFORE / AFTER ---- */
.ps-ba-card { border-radius: 18px; overflow: hidden; background: #fff; border: 1px solid #ede8e0; box-shadow: 0 4px 20px rgba(0,0,0,.05); }
.ps-ba-pair { display: grid; grid-template-columns: 1fr 1fr; }
.ps-ba-side { position: relative; }
.ps-ba-side img { width:100%; height:200px; object-fit:cover; display:block; }
.ps-ba-side-ph { width:100%; height:200px; background:#f4f4f4; display:flex; align-items:center; justify-content:center; color:#ccc; font-size:2rem; }
.ps-ba-side-label {
    position: absolute; bottom: 8px; left: 8px;
    font-size: .65rem; font-weight: 800; letter-spacing: .1em;
    text-transform: uppercase; background: rgba(12,26,46,.75);
    color: #fff; padding: 3px 8px; border-radius: 5px;
}
.ps-ba-info { padding: 1rem 1.2rem; }
.ps-ba-title { font-family:'Cormorant Garamond',serif; font-size:1.1rem; font-weight:600; color:var(--ps-navy); }
.ps-ba-proc { font-size:.76rem; color:var(--ps-gold); font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
.ps-ba-note { font-size:.78rem; color:var(--ps-muted); margin-top:.25rem; }

/* ---- WHY US ---- */
.ps-why-card {
    text-align: center; padding: 2rem 1.5rem;
    border-radius: 18px; border: 1px solid rgba(201,167,119,.2);
    background: rgba(255,255,255,.04);
    transition: background .25s, border-color .25s;
}
.ps-why-card:hover { background: rgba(201,167,119,.07); border-color: rgba(201,167,119,.4); }
.ps-why-icon {
    width: 60px; height: 60px; border-radius: 50%;
    background: rgba(201,167,119,.15);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; color: var(--ps-gold);
    margin: 0 auto 1rem;
}
.ps-why-title { font-family:'Cormorant Garamond',serif; font-size:1.2rem; font-weight:600; color:#fff; margin-bottom:.4rem; }
.ps-why-desc { font-size:.82rem; color:rgba(255,255,255,.55); line-height:1.7; }

/* ---- TESTIMONIALS ---- */
.ps-testi-card {
    background: var(--ps-cream);
    border-radius: 18px; border: 1px solid #ede8e0;
    padding: 1.8rem; position: relative; height: 100%;
}
.ps-testi-quote { font-size:3rem; color:var(--ps-gold); line-height:.8; font-family:'Cormorant Garamond',serif; margin-bottom:.5rem; }
.ps-testi-text { font-size:.92rem; color:#3b3b4f; line-height:1.85; margin-bottom:1.25rem; font-style:italic; }
.ps-testi-name { font-weight:700; font-size:.88rem; color:var(--ps-navy); }
.ps-testi-stars { color:var(--ps-gold); font-size:.8rem; }

/* ---- BLOG ---- */
.ps-blog-card { border-radius:18px; overflow:hidden; background:#fff; border:1px solid #ede8e0; transition:box-shadow .2s; height:100%; }
.ps-blog-card:hover { box-shadow:0 12px 40px rgba(0,0,0,.1); }
.ps-blog-img { width:100%; height:180px; object-fit:cover; display:block; }
.ps-blog-img-ph { width:100%; height:180px; background:linear-gradient(135deg,#f5f0e8,#ede8dc); display:flex; align-items:center; justify-content:center; font-size:2.5rem; color:#ccc; }
.ps-blog-body { padding:1.2rem 1.4rem; }
.ps-blog-cat { font-size:.68rem; font-weight:800; letter-spacing:.1em; text-transform:uppercase; color:var(--ps-gold); margin-bottom:.4rem; }
.ps-blog-title { font-family:'Cormorant Garamond',serif; font-size:1.18rem; font-weight:600; color:var(--ps-navy); margin-bottom:.5rem; line-height:1.3; }
.ps-blog-exc { font-size:.82rem; color:var(--ps-muted); line-height:1.7; }
.ps-blog-link { color:var(--ps-gold); font-size:.82rem; font-weight:700; text-decoration:none; }
.ps-blog-link:hover { color:var(--ps-gold2); }

/* ---- CTA BOTTOM ---- */
.ps-cta-bottom {
    background: var(--ps-navy);
    padding: 5rem 0;
    text-align: center;
    border-top: 1px solid rgba(201,167,119,.15);
}
.ps-cta-bottom h2 {
    font-family:'Cormorant Garamond',serif;
    font-size:clamp(2rem,4vw,3rem);
    font-weight:600; color:#fff; margin-bottom:.75rem;
}
.ps-cta-bottom p { color:rgba(255,255,255,.6); font-size:.95rem; margin-bottom:2rem; }

/* ---- RESPONSIVE ---- */
@media (max-width:767px){
    .ps-hero { min-height:80vh; }
    .ps-hero-content { padding:100px 0 60px; }
    .ps-trust-item { border-right:none; border-bottom:1px solid rgba(255,255,255,.08); padding:1rem 0; }
    .ps-surgeon-badge { right:8px; bottom:-16px; padding:.6rem .9rem; }
    .ps-ba-pair { grid-template-columns:1fr; }
    .ps-ba-side img, .ps-ba-side-ph { height:150px; }
}
</style>

<!-- ========== HERO ========== -->
<section class="ps-hero">
    <?php
    $heroImg = '';
    if (!empty($heroSlides)) {
        $heroImg = $heroSlides[0]['background_image'] ?? '';
    }
    ?>
    <?php if ($heroImg): ?>
    <div class="ps-hero-bg" style="background-image:url('<?= e($heroImg) ?>');"></div>
    <?php endif; ?>
    <div class="ps-hero-overlay"></div>
    <div class="ps-hero-deco"></div>
    <div class="container ps-hero-content">
        <div class="row">
            <div class="col-lg-7">
                <div class="ps-eyebrow">Precision &middot; Beauty &middot; Confidence</div>
                <h1 class="ps-font-serif">
                    <?= e($ps_siteName) ?><br>
                    <em>Aesthetic Excellence</em>
                </h1>
                <p class="ps-hero-sub">Board-certified plastic surgery with personalized care. Transforming lives through artistry, science, and compassionate expertise.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="/appointment.php" class="ps-btn-gold"><i class="fas fa-calendar-check"></i> Book Free Consultation</a>
                    <a href="#before-after" class="ps-btn-outline"><i class="fas fa-images"></i> View Our Work</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== TRUST STRIP ========== -->
<section class="ps-trust-strip">
    <div class="container">
        <div class="row g-0">
            <?php
            $trustStats = [
                ['num'=>'500+',  'lbl'=>'Successful Surgeries'],
                ['num'=>'15+',   'lbl'=>'Years Experience'],
                ['num'=>'1000+', 'lbl'=>'Happy Patients'],
                ['num'=>'98%',   'lbl'=>'Patient Satisfaction'],
            ];
            foreach ($trustStats as $stat):
            ?>
            <div class="col-6 col-md-3">
                <div class="ps-trust-item">
                    <div class="ps-trust-num"><?= $stat['num'] ?></div>
                    <div class="ps-trust-lbl"><?= $stat['lbl'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ========== ABOUT THE SURGEON ========== -->
<?php if ($ps_surgeon): ?>
<section class="ps-section ps-section-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="ps-surgeon-img-wrap">
                    <?php if (!empty($ps_surgeon['photo'])): ?>
                    <img src="<?= e($ps_surgeon['photo']) ?>" alt="<?= e($ps_surgeon['name']) ?>" class="ps-surgeon-img">
                    <?php else: ?>
                    <div class="ps-surgeon-img" style="background:linear-gradient(135deg,#f5f0e8,#ede8dc);display:flex;align-items:center;justify-content:center;min-height:400px;"><i class="fas fa-user-md" style="font-size:5rem;color:#ccc;"></i></div>
                    <?php endif; ?>
                    <div class="ps-surgeon-badge">
                        <div class="ps-surgeon-badge-num">15+</div>
                        <div class="ps-surgeon-badge-lbl">Years<br>Expertise</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7" style="padding-top:2rem;">
                <div class="ps-label">Meet Your Surgeon</div>
                <div class="ps-surgeon-name"><?= e($ps_surgeon['name']) ?></div>
                <div class="ps-surgeon-spec"><?= e($ps_surgeon['department_name'] ?? 'Plastic & Aesthetic Surgery') ?></div>
                <div class="ps-gold-line"></div>
                <?php if (!empty($ps_surgeon['bio'])): ?>
                <p class="ps-surgeon-bio"><?= nl2br(e($ps_surgeon['bio'])) ?></p>
                <?php else: ?>
                <p class="ps-surgeon-bio">With over 15 years of specialized experience in plastic and reconstructive surgery, dedicated to delivering natural, elegant results through a personalized approach tailored to each patient's unique goals and anatomy.</p>
                <?php endif; ?>
                <div class="d-flex flex-wrap gap-3 mb-3">
                    <?php foreach (['Board Certified','FIAPS Member','1000+ Surgeries'] as $badge): ?>
                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:.8rem;font-weight:700;color:var(--ps-navy);background:#f5f0e8;border-radius:8px;padding:.35rem .85rem;"><i class="fas fa-check" style="color:var(--ps-gold);font-size:.65rem;"></i><?= $badge ?></span>
                    <?php endforeach; ?>
                </div>
                <a href="/appointment.php<?= !empty($ps_surgeon['doctor_id']) ? '?doctor='.$ps_surgeon['doctor_id'] : '' ?>" class="ps-btn-gold"><i class="fas fa-calendar-check"></i>Book Appointment with <?= e($ps_surgeon['name']) ?></a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== PROCEDURES ========== -->
<?php if (!empty($departments)): ?>
<section class="ps-section ps-section-light">
    <div class="container">
        <div class="text-center mb-5">
            <div class="ps-label" style="justify-content:center;">What We Offer</div>
            <h2 class="ps-heading">Our Procedures</h2>
            <p class="ps-sub mx-auto">Comprehensive aesthetic and reconstructive solutions tailored to your individual goals.</p>
        </div>
        <div class="row g-4">
            <?php
            $deptIcons = ['fas fa-star','fas fa-leaf','fas fa-spa','fas fa-magic','fas fa-heartbeat','fas fa-eye','fas fa-hand-sparkles','fas fa-shield-alt'];
            foreach ($departments as $i => $dept):
            ?>
            <div class="col-md-4 col-sm-6">
                <div class="ps-proc-card">
                    <div class="ps-proc-icon">
                        <i class="<?= e($dept['icon'] ?? ($deptIcons[$i % count($deptIcons)])) ?>"></i>
                    </div>
                    <div class="ps-proc-name"><?= e($dept['name']) ?></div>
                    <?php if (!empty($dept['description'])): ?>
                    <p class="ps-proc-desc"><?= e(mb_substr($dept['description'],0,100)) . (mb_strlen($dept['description'])>100?'…':'') ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== BEFORE / AFTER ========== -->
<section class="ps-section ps-section-white" id="before-after">
    <div class="container">
        <div class="text-center mb-5">
            <div class="ps-label" style="justify-content:center;">Real Results</div>
            <h2 class="ps-heading">Before &amp; After Gallery</h2>
            <p class="ps-sub mx-auto">Authentic patient transformations showcasing the art and precision of our procedures.</p>
        </div>
        <?php if (!empty($beforeAfterItems)): ?>
        <div class="row g-4">
            <?php foreach ($beforeAfterItems as $item): ?>
            <div class="col-md-4 col-sm-6">
                <div class="ps-ba-card">
                    <div class="ps-ba-pair">
                        <div class="ps-ba-side">
                            <?php if (!empty($item['before_image'])): ?>
                            <img src="<?= e($item['before_image']) ?>" alt="Before">
                            <?php else: ?><div class="ps-ba-side-ph"><i class="fas fa-image"></i></div><?php endif; ?>
                            <span class="ps-ba-side-label">Before</span>
                        </div>
                        <div class="ps-ba-side">
                            <?php if (!empty($item['after_image'])): ?>
                            <img src="<?= e($item['after_image']) ?>" alt="After">
                            <?php else: ?><div class="ps-ba-side-ph"><i class="fas fa-image"></i></div><?php endif; ?>
                            <span class="ps-ba-side-label">After</span>
                        </div>
                    </div>
                    <div class="ps-ba-info">
                        <div class="ps-ba-title"><?= e($item['title']) ?></div>
                        <?php if (!empty($item['procedure_name'])): ?><div class="ps-ba-proc"><?= e($item['procedure_name']) ?></div><?php endif; ?>
                        <?php if (!empty($item['patient_note'])): ?><div class="ps-ba-note"><i class="fas fa-comment-dots me-1"></i><?= e($item['patient_note']) ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5" style="color:var(--ps-muted);">
            <i class="fas fa-images fa-3x mb-3" style="opacity:.3;color:var(--ps-gold);"></i>
            <p>Gallery coming soon. <a href="/admin/before-after.php" style="color:var(--ps-gold);">Add entries in the admin panel.</a></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========== WHY CHOOSE US ========== -->
<section class="ps-section ps-section-dark">
    <div class="container">
        <div class="text-center mb-5">
            <div class="ps-label" style="justify-content:center;color:var(--ps-gold);">Our Commitment</div>
            <h2 class="ps-heading ps-heading-light">Why Choose Us</h2>
            <p class="ps-sub ps-sub-light mx-auto">Your safety, comfort, and satisfaction are at the heart of everything we do.</p>
        </div>
        <div class="row g-4">
            <?php
            $whyCards = [
                ['icon'=>'fas fa-certificate','title'=>'Board-Certified Surgeons','desc'=>'Our surgeons hold the highest certifications and train continuously at leading international institutions.'],
                ['icon'=>'fas fa-microscope','title'=>'State-of-the-Art Technology','desc'=>'We use the latest surgical techniques and cutting-edge equipment for precision and optimal outcomes.'],
                ['icon'=>'fas fa-heart','title'=>'Personalized Care Plans','desc'=>'Every treatment is custom-designed around your unique anatomy, goals, and aesthetic preferences.'],
                ['icon'=>'fas fa-shield-alt','title'=>'Safety First, Always','desc'=>'Accredited facility, rigorous protocols, and comprehensive pre- and post-operative care for your peace of mind.'],
            ];
            foreach ($whyCards as $card):
            ?>
            <div class="col-md-6 col-lg-3">
                <div class="ps-why-card">
                    <div class="ps-why-icon"><i class="<?= $card['icon'] ?>"></i></div>
                    <div class="ps-why-title"><?= $card['title'] ?></div>
                    <p class="ps-why-desc"><?= $card['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ========== TESTIMONIALS ========== -->
<?php if (!empty($testimonials)): ?>
<section class="ps-section ps-section-white">
    <div class="container">
        <div class="text-center mb-5">
            <div class="ps-label" style="justify-content:center;">Patient Stories</div>
            <h2 class="ps-heading">What Our Patients Say</h2>
        </div>
        <div class="row g-4">
            <?php foreach (array_slice($testimonials,0,3) as $t): ?>
            <div class="col-md-4">
                <div class="ps-testi-card">
                    <div class="ps-testi-quote">&ldquo;</div>
                    <p class="ps-testi-text"><?= e($t['content'] ?? $t['message'] ?? '') ?></p>
                    <div class="ps-testi-stars">
                        <?php for ($s=0;$s<5;$s++): ?><i class="fas fa-star"></i><?php endfor; ?>
                    </div>
                    <div class="ps-testi-name mt-1"><?= e($t['patient_name'] ?? $t['name'] ?? 'Anonymous') ?></div>
                    <?php if (!empty($t['procedure'])): ?><div style="font-size:.76rem;color:var(--ps-gold);"><?= e($t['procedure']) ?></div><?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== LATEST ARTICLES ========== -->
<?php if (!empty($latestPosts)): ?>
<section class="ps-section ps-section-light">
    <div class="container">
        <div class="text-center mb-5">
            <div class="ps-label" style="justify-content:center;">Knowledge Hub</div>
            <h2 class="ps-heading">Latest Articles</h2>
            <p class="ps-sub mx-auto">Expert insights on procedures, recovery, and aesthetic health.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($latestPosts as $post): ?>
            <div class="col-md-4">
                <div class="ps-blog-card">
                    <?php if (!empty($post['featured_image'])): ?>
                    <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" class="ps-blog-img">
                    <?php else: ?>
                    <div class="ps-blog-img-ph"><i class="fas fa-feather-alt"></i></div>
                    <?php endif; ?>
                    <div class="ps-blog-body">
                        <div class="ps-blog-cat"><?= e($post['category'] ?? 'Insights') ?></div>
                        <div class="ps-blog-title"><?= e($post['title']) ?></div>
                        <?php if (!empty($post['excerpt'])): ?>
                        <p class="ps-blog-exc"><?= e(mb_substr($post['excerpt'],0,100)) ?>…</p>
                        <?php endif; ?>
                        <a href="/blog/<?= e($post['slug'] ?? '#') ?>" class="ps-blog-link">Read More <i class="fas fa-arrow-right ms-1" style="font-size:.7rem;"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== CTA BOTTOM ========== -->
<section class="ps-cta-bottom">
    <div class="container">
        <div class="ps-label" style="justify-content:center;margin-bottom:1rem;">Take the First Step</div>
        <h2>Ready for Your Transformation?</h2>
        <p>Schedule a complimentary consultation and let us design the best plan for your goals.</p>
        <?php if ($ps_phone): ?>
        <div style="font-family:'Cormorant Garamond',serif;font-size:1.5rem;color:var(--ps-gold);margin-bottom:1.5rem;"><i class="fas fa-phone-alt me-2"></i><?= e($ps_phone) ?></div>
        <?php endif; ?>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="/appointment.php" class="ps-btn-gold"><i class="fas fa-calendar-check"></i>Book Appointment</a>
            <?php if ($ps_phone): ?>
            <a href="tel:<?= preg_replace('/[^0-9+]/','',$ps_phone) ?>" class="ps-btn-outline"><i class="fas fa-phone-alt"></i>Call Us Now</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
