<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

$departments = getDepartments($pdo);
$doctors = getDoctors($pdo);
$testimonials = getTestimonials($pdo);
$latestPosts = getPosts($pdo, true, 3);
$emergencyPhone = $settings['emergency_phone'] ?? '';
$heroSlides = getHeroSlides($pdo, true);
$infoStripData = getHomeSection($pdo, 'info_strip');
$aboutData = getHomeSection($pdo, 'about_section');
$locationData = getHomeSection($pdo, 'location_section');
$ctaCheckupData = getHomeSection($pdo, 'cta_checkup');
$appointmentData = getHomeSection($pdo, 'appointment_section');
$processData = getHomeSection($pdo, 'process_section');
$statsData = getHomeSection($pdo, 'stats_section');
$ctaReadyData = getHomeSection($pdo, 'cta_ready');
$videosData = getHomeSection($pdo, 'our_videos') ?: ['title' => 'Our Latest Videos', 'subtitle' => 'Watch health tips, facility tours and expert talks', 'videos' => []];
$secVis = getHomeSection($pdo, 'section_visibility');
if (empty($secVis)) $secVis = [];
$isVisible = function($key) use ($secVis) { return !isset($secVis[$key]) || !empty($secVis[$key]); };
?>

<?php if ($isVisible('hero_slider')): ?>
<?php if (!empty($heroSlides)): ?>
<section class="hero-slider">
    <div id="heroSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
        <div class="carousel-indicators">
            <?php foreach ($heroSlides as $i => $slide): ?>
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active"' : '' ?>></button>
            <?php endforeach; ?>
        </div>

        <div class="carousel-inner">
            <?php foreach ($heroSlides as $i => $slide):
                $bgStyle = !empty($slide['background_image'])
                    ? "background-image:url('" . e($slide['background_image']) . "');"
                    : "background:linear-gradient(110deg, #0f2b5c 0%, #0D6EFD 50%, #20C997 100%);";
                $overlay = e($slide['overlay_color'] ?? 'rgba(15,33,55,0.7)');
                $anim = e($slide['text_animation'] ?? 'fadeIn');
            ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" data-animation="<?= $anim ?>" data-transition="<?= e($slide['transition_effect'] ?? 'slide') ?>">
                <div class="slide-bg" style="<?= $bgStyle ?>"></div>
                <div class="slide-overlay" style="background:<?= $overlay ?>;"></div>

                <div class="slide-floating-shapes">
                    <div class="floating-shape"></div>
                    <div class="floating-shape"></div>
                    <div class="floating-shape"></div>
                </div>

                <div class="slide-content">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <?php if (!empty($slide['subtitle'])): ?>
                                <div class="slide-subtitle"><i class="fas fa-plus-circle me-2"></i><?= e($slide['subtitle']) ?></div>
                                <?php endif; ?>
                                <h1 class="slide-title"><?= e($slide['title']) ?></h1>
                                <?php if (!empty($slide['description'])): ?>
                                <p class="slide-description"><?= e($slide['description']) ?></p>
                                <?php endif; ?>
                                <div class="slide-buttons">
                                    <a href="<?= e($slide['button_link'] ?? '#') ?>" class="slide-btn slide-btn-primary">
                                        <i class="fas fa-calendar-check"></i>
                                        <?= e($slide['button_text'] ?? 'Learn More') ?>
                                    </a>
                                    <a href="/public/departments.php" class="slide-btn slide-btn-outline">
                                        <i class="fas fa-stethoscope"></i>
                                        Our Services
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="slide-pattern"></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="slide-medical-icons d-none d-lg-flex">
            <div class="med-icon"><i class="fas fa-heartbeat"></i></div>
            <div class="med-icon"><i class="fas fa-lungs"></i></div>
            <div class="med-icon"><i class="fas fa-brain"></i></div>
            <div class="med-icon"><i class="fas fa-tooth"></i></div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>
<?php else: ?>
<section class="hero-section">
    <div class="container position-relative" style="z-index:2;">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="hero-badge"><i class="fas fa-plus-circle me-1"></i> Welcome to JMedi</span>
                <h1 class="mb-4">We Take Care Of<br>Your <span style="color: var(--secondary);">Healthy</span> Life</h1>
                <p class="hero-subtitle mb-4">Providing comprehensive healthcare solutions with world-class medical professionals.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="/public/appointment.php" class="hero-btn hero-btn-light"><i class="fas fa-calendar-check me-2"></i>Make Appointment</a>
                    <a href="/public/departments.php" class="hero-btn hero-btn-outline"><i class="fas fa-stethoscope me-2"></i>Our Services</a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="hero-icon-grid">
                    <div class="hero-floating-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="hero-floating-icon"><i class="fas fa-lungs"></i></div>
                    <div class="hero-floating-icon"><i class="fas fa-brain"></i></div>
                    <div class="hero-floating-icon"><i class="fas fa-tooth"></i></div>
                    <div class="hero-floating-icon"><i class="fas fa-bone"></i></div>
                    <div class="hero-floating-icon"><i class="fas fa-eye"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>

<?php if ($isVisible('info_strip')): ?>
<?php
$stripItems = $infoStripData['items'] ?? [
    ['title' => 'Request Appointment', 'subtitle' => 'Book a visit online', 'icon' => 'fa-calendar-check', 'link' => '/public/appointment.php', 'color' => '#0D6EFD'],
    ['title' => 'Find Doctors', 'subtitle' => 'Expert specialists', 'icon' => 'fa-user-md', 'link' => '/public/doctors.php', 'color' => '#20C997'],
    ['title' => 'Find Locations', 'subtitle' => 'Visit our hospital', 'icon' => 'fa-map-marker-alt', 'link' => '/public/contact.php', 'color' => '#6f42c1'],
    ['title' => 'Emergency', 'subtitle' => $emergencyPhone, 'icon' => 'fa-ambulance', 'link' => '', 'color' => '#dc3545']
];
$colSize = count($stripItems) > 0 ? intval(12 / count($stripItems)) : 3;
if ($colSize < 2) $colSize = 3;
?>
<div class="container">
    <div class="info-strip">
        <div class="row g-0">
            <?php foreach ($stripItems as $stripItem): ?>
            <div class="col-lg-<?= $colSize ?> col-md-6">
                <?php if (!empty($stripItem['link'])): ?>
                <a href="<?= e($stripItem['link']) ?>" class="info-strip-item text-decoration-none">
                <?php else: ?>
                <div class="info-strip-item">
                <?php endif; ?>
                    <div class="strip-icon" style="background:<?= e($stripItem['color'] ?? '#0D6EFD') ?>;">
                        <i class="fas <?= e($stripItem['icon'] ?? 'fa-circle') ?>"></i>
                    </div>
                    <div>
                        <h6><?= e($stripItem['title']) ?></h6>
                        <p><?= e($stripItem['subtitle']) ?></p>
                    </div>
                <?php if (!empty($stripItem['link'])): ?>
                </a>
                <?php else: ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($isVisible('about_section')): ?>
<?php
$abtSubtitle = $aboutData['subtitle'] ?? 'About Us';
$abtTitle = $aboutData['title'] ?? 'Welcome To JMedi Central Hospital';
$abtDesc = $aboutData['description'] ?? 'At JMedi, we are committed to delivering the highest standard of medical care. Our state-of-the-art facilities combined with experienced medical professionals ensure you receive comprehensive healthcare tailored to your needs.';
$abtImage = $aboutData['image'] ?? '';
$abtYears = $aboutData['experience_years'] ?? '25+';
$abtYearsLabel = $aboutData['experience_label'] ?? 'Years of Experience';
$abtFeatures = $aboutData['features'] ?? ['Advanced Medical Technology', 'Certified & Experienced Doctors', '24/7 Emergency Support', 'Comprehensive Health Packages'];
$abtBtnText = $aboutData['button_text'] ?? 'Discover More';
$abtBtnLink = $aboutData['button_link'] ?? '/public/departments.php';
?>
<section class="about-section section-padding">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 fade-in-left">
                <div class="about-img-wrapper">
                    <?php if (!empty($abtImage)): ?>
                    <div class="rounded-3 overflow-hidden">
                        <img src="<?= e($abtImage) ?>" alt="<?= e($abtTitle) ?>" style="width:100%;height:auto;display:block;">
                    </div>
                    <?php else: ?>
                    <div class="rounded-3 overflow-hidden" style="background:linear-gradient(135deg,#e8f4fd,#d4f5e9);padding:3rem;text-align:center;">
                        <i class="fas fa-hospital" style="font-size:10rem;color:var(--primary);opacity:0.2;"></i>
                    </div>
                    <?php endif; ?>
                    <div class="about-experience-badge">
                        <div class="number"><?= e($abtYears) ?></div>
                        <small><?= nl2br(e($abtYearsLabel)) ?></small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 fade-in-right">
                <div class="section-title text-start mb-4">
                    <span class="subtitle" style="padding-left:0;"><?= e($abtSubtitle) ?></span>
                    <h2><?= e($abtTitle) ?></h2>
                </div>
                <p class="mb-4" style="line-height:1.8;"><?= e($abtDesc) ?></p>
                <?php if (!empty($abtFeatures)): ?>
                <ul class="about-feature-list mb-4">
                    <?php foreach ($abtFeatures as $feature): ?>
                    <li><i class="fas fa-check"></i> <?= e($feature) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <a href="<?= e($abtBtnLink) ?>" class="btn btn-primary px-4 py-2" style="border-radius:30px;">
                    <i class="fas fa-arrow-right me-2"></i><?= e($abtBtnText) ?>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isVisible('departments')): ?>
<section class="section-padding" style="background:var(--light-bg);">
    <div class="container">
        <div class="section-title fade-in">
            <span class="subtitle">Departments</span>
            <h2>Our Medical Services</h2>
            <p>We provide specialized medical services across a wide range of departments</p>
        </div>
        <div class="row g-4">
            <?php foreach (array_slice($departments, 0, 6) as $i => $dept): ?>
            <div class="col-lg-4 col-md-6 fade-in delay-<?= ($i % 3) + 1 ?>">
                <div class="dept-card">
                    <div class="icon-box">
                        <i class="fas <?= e($dept['icon'] ?? 'fa-heartbeat') ?>"></i>
                    </div>
                    <h5><?= e($dept['name']) ?></h5>
                    <p><?= e(truncateText($dept['description'] ?? '', 100)) ?></p>
                    <a href="/public/departments.php?slug=<?= e($dept['slug']) ?>" class="btn btn-sm btn-outline-primary">
                        Learn More <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isVisible('doctors')): ?>
<section class="section-padding">
    <div class="container">
        <div class="section-title fade-in">
            <span class="subtitle">Meet Our Team</span>
            <h2>Specialist Doctors</h2>
            <p>Experienced and dedicated medical professionals committed to your health</p>
        </div>
        <div class="row g-4">
            <?php foreach (array_slice($doctors, 0, 4) as $i => $doc): ?>
            <div class="col-lg-3 col-md-6 scale-in delay-<?= $i + 1 ?>">
                <div class="card doctor-card">
                    <div class="doctor-img">
                        <?php if ($doc['photo']): ?>
                            <img src="<?= e($doc['photo']) ?>" alt="<?= e($doc['name']) ?>">
                        <?php else: ?>
                            <i class="fas fa-user-md placeholder-icon"></i>
                        <?php endif; ?>
                        <div class="doctor-overlay">
                            <a href="/public/doctor-profile.php?id=<?= $doc['doctor_id'] ?>" class="social-icon"><i class="fas fa-link"></i></a>
                            <?php if (!empty($doc['email'])): ?>
                            <a href="mailto:<?= e($doc['email']) ?>" class="social-icon"><i class="fas fa-envelope"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($doc['phone'])): ?>
                            <a href="tel:<?= e($doc['phone']) ?>" class="social-icon"><i class="fas fa-phone"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5><a href="/public/doctor-profile.php?id=<?= $doc['doctor_id'] ?>" class="text-decoration-none text-dark"><?= e($doc['name']) ?></a></h5>
                        <span class="dept-badge"><?= e($doc['department_name'] ?? '') ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5 fade-in">
            <a href="/public/doctors.php" class="btn btn-primary px-5 py-2" style="border-radius:30px;">
                View All Doctors <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$activeVideos = array_values(array_filter($videosData['videos'] ?? [], fn($v) => !empty($v['active'])));
?>

<?php if ($isVisible('cta_checkup')): ?>
<?php
$ctaHeading = $ctaCheckupData['heading'] ?? 'Need a Doctor for Check-up?';
$ctaSubtitle = $ctaCheckupData['subtitle'] ?? 'Just make an appointment and you\'re done!';
$ctaBtnText = $ctaCheckupData['button_text'] ?? 'Make Appointment';
$ctaBtnLink = $ctaCheckupData['button_link'] ?? '/public/appointment.php';
?>
<section class="cta-section">
    <div class="container position-relative" style="z-index:2;">
        <div class="row align-items-center">
            <div class="col-lg-8 text-lg-start">
                <h2 style="font-size:2.2rem;"><?= e($ctaHeading) ?></h2>
                <p class="mb-0"><?= e($ctaSubtitle) ?></p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="<?= e($ctaBtnLink) ?>" class="btn btn-light btn-lg px-5" style="border-radius:30px;font-weight:700;color:var(--primary);">
                    <i class="fas fa-calendar-check me-2"></i><?= e($ctaBtnText) ?>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isVisible('appointment')): ?>
<?php
$apptBadge = $appointmentData['badge_text'] ?? 'Appointment';
$apptHeading = $appointmentData['heading'] ?? "Book Your\nAppointment";
$apptDesc = $appointmentData['description'] ?? 'Schedule your visit with our specialists. Fill out the form and we\'ll confirm your appointment within 24 hours.';
?>
<section class="appointment-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0 fade-in-left">
                <span class="hero-badge"><i class="fas fa-calendar-alt me-1"></i> <?= e($apptBadge) ?></span>
                <h2 class="fw-bold mb-3" style="color:white;font-size:2.2rem;"><?= nl2br(e($apptHeading)) ?></h2>
                <p class="opacity-75 mb-4" style="line-height:1.8;"><?= e($apptDesc) ?></p>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:50px;height:50px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-phone-alt fs-5"></i>
                    </div>
                    <div>
                        <small class="d-block opacity-75">Call us directly</small>
                        <span class="fs-5 fw-bold"><?= e($settings['phone'] ?? '') ?></span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div style="width:50px;height:50px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-clock fs-5"></i>
                    </div>
                    <div>
                        <small class="d-block opacity-75">Working Hours</small>
                        <span class="fw-bold">Mon-Sat: 8:00 AM - 7:00 PM</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 fade-in-right">
                <form action="/public/appointment.php" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCSRFToken()) ?>">
                    <input type="hidden" name="book_appointment" value="1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="patient_name" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        <div class="col-md-6">
                            <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required>
                        </div>
                        <div class="col-md-6">
                            <select name="department_id" class="form-select" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['department_id'] ?>"><?= e($dept['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="appointment_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <input type="time" name="appointment_time" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <textarea name="message" class="form-control" rows="3" placeholder="Your Message (optional)"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-appointment">
                                <i class="fas fa-paper-plane me-2"></i>Submit Appointment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isVisible('process')): ?>
<?php
$procSubtitle = $processData['subtitle'] ?? 'Working Process';
$procHeading = $processData['heading'] ?? 'How It Helps You Stay Healthy';
$procSteps = $processData['steps'] ?? [
    ['icon' => 'fa-calendar-check', 'title' => 'Book Appointment', 'description' => 'Schedule your visit online easily with our booking system'],
    ['icon' => 'fa-stethoscope', 'title' => 'Consultation', 'description' => 'Meet with experienced doctors for thorough evaluation'],
    ['icon' => 'fa-prescription', 'title' => 'Treatment Plan', 'description' => 'Receive personalized treatment plans tailored for you'],
    ['icon' => 'fa-smile-beam', 'title' => 'Get Healthy', 'description' => 'Recover and enjoy a healthy, happy life with ongoing care'],
];
?>
<section class="process-section section-padding">
    <div class="container">
        <div class="section-title fade-in">
            <span class="subtitle"><?= e($procSubtitle) ?></span>
            <h2><?= e($procHeading) ?></h2>
        </div>
        <div class="row g-4">
            <?php foreach ($procSteps as $stepIdx => $step): ?>
            <div class="col-lg-3 col-md-6 fade-in delay-<?= $stepIdx + 1 ?>">
                <div class="process-card">
                    <div class="process-icon">
                        <i class="fas <?= e($step['icon'] ?? 'fa-circle') ?>"></i>
                        <span class="process-number"><?= str_pad($stepIdx + 1, 2, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <h5><?= e($step['title']) ?></h5>
                    <p><?= e($step['description']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isVisible('stats')): ?>
<?php
$statItems = $statsData['items'] ?? [
    ['icon' => 'fa-hospital', 'number' => '25', 'suffix' => '+', 'label' => 'Years of Experience'],
    ['icon' => 'fa-user-md', 'number' => (string)getCount($pdo, 'doctors'), 'suffix' => '', 'label' => 'Medical Specialists'],
    ['icon' => 'fa-procedures', 'number' => '13', 'suffix' => '', 'label' => 'Modern Rooms'],
    ['icon' => 'fa-smile', 'number' => '1500', 'suffix' => '+', 'label' => 'Happy Patients'],
];
?>
<section class="stats-section">
    <div class="container position-relative" style="z-index:2;">
        <div class="row">
            <?php foreach ($statItems as $statIdx => $stat):
                $statNum = $stat['number'] ?? '0';
                if ($statNum === 'auto') $statNum = (string)getCount($pdo, 'doctors');
            ?>
            <div class="col-lg-3 col-md-6">
                <div class="stat-item fade-in delay-<?= $statIdx + 1 ?>">
                    <div class="stat-icon"><i class="fas <?= e($stat['icon'] ?? 'fa-circle') ?>"></i></div>
                    <div class="stat-number"><span class="counter-number" data-target="<?= e($statNum) ?>">0</span><?= e($stat['suffix'] ?? '') ?></div>
                    <div class="stat-label"><?= e($stat['label'] ?? '') ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isVisible('testimonials') && $testimonials): ?>
<section class="section-padding" style="background:var(--light-bg);">
    <div class="container">
        <div class="section-title fade-in">
            <span class="subtitle">Testimonials</span>
            <h2>What Our Patients Say</h2>
            <p>Real stories from real patients about their experience at JMedi</p>
        </div>
        <div class="swiper testimonial-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($testimonials as $t): ?>
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="stars">
                            <?php for ($i = 0; $i < ($t['rating'] ?? 5); $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <blockquote>"<?= e($t['content']) ?>"</blockquote>
                        <div class="patient-info">
                            <div class="patient-avatar"><?= strtoupper(substr($t['patient_name'], 0, 1)) ?></div>
                            <div>
                                <p class="patient-name"><?= e($t['patient_name']) ?></p>
                                <span class="patient-title">Patient</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isVisible('our_videos') && !empty($activeVideos)): ?>
<section class="section-padding" style="background:var(--light-bg);">
    <div class="container">
        <div class="section-title fade-in">
            <span class="subtitle"><i class="fab fa-youtube me-1" style="color:#ff0000;"></i>Watch &amp; Learn</span>
            <h2><?= e($videosData['title'] ?? 'Our Latest Videos') ?></h2>
            <?php if (!empty($videosData['subtitle'])): ?>
            <p><?= e($videosData['subtitle']) ?></p>
            <?php endif; ?>
        </div>
        <div class="swiper video-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($activeVideos as $vid): ?>
                <div class="swiper-slide">
                    <div class="testimonial-card video-card-t" onclick="openVideoModal('<?= e($vid['id']) ?>', '<?= e(addslashes($vid['title'])) ?>')">
                        <div class="vid-thumb-wrap">
                            <img src="https://img.youtube.com/vi/<?= e($vid['id']) ?>/maxresdefault.jpg"
                                 onerror="this.src='https://img.youtube.com/vi/<?= e($vid['id']) ?>/hqdefault.jpg'"
                                 alt="<?= e($vid['title']) ?>">
                            <div class="vid-play-btn">
                                <i class="fab fa-youtube"></i>
                            </div>
                        </div>
                        <div class="vid-card-body">
                            <h6 class="vid-card-title"><?= e($vid['title']) ?></h6>
                            <span class="vid-watch-tag"><i class="fas fa-play-circle me-1"></i>Watch Video</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination vid-swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- Video Popup Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
            <div class="modal-header border-0 px-4 pt-3 pb-0">
                <h6 class="modal-title fw-bold" id="videoModalTitle"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 pt-2">
                <div class="ratio ratio-16x9">
                    <iframe id="videoModalIframe" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.video-card-t {
    cursor: pointer;
    padding: 0;
    overflow: hidden;
    transition: transform 0.28s, box-shadow 0.28s;
    user-select: none;
}
.video-card-t::before { display: none; }
.video-card-t:hover { transform: translateY(-6px); box-shadow: 0 14px 40px rgba(0,0,0,0.14); }
.vid-thumb-wrap {
    position: relative;
    overflow: hidden;
    aspect-ratio: 16/9;
    background: #0d0d1a;
}
.vid-thumb-wrap img { width:100%; height:100%; object-fit:cover; display:block; transition: transform 0.4s; }
.video-card-t:hover .vid-thumb-wrap img { transform: scale(1.05); }
.vid-play-btn {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(0,0,0,0.25);
    transition: background 0.3s;
}
.video-card-t:hover .vid-play-btn { background: rgba(0,0,0,0.45); }
.vid-play-btn i {
    font-size: 3.5rem;
    color: #fff;
    filter: drop-shadow(0 2px 8px rgba(0,0,0,0.5));
    transition: transform 0.25s, color 0.25s;
}
.video-card-t:hover .vid-play-btn i { transform: scale(1.12); color: #ff2222; }
.vid-card-body { padding: 1.2rem 1.5rem 1.5rem; }
.vid-card-title {
    font-weight: 700; font-size: 1rem;
    color: var(--text-heading);
    margin-bottom: 0.6rem; line-height: 1.45;
}
.vid-watch-tag {
    font-size: 0.8rem; font-weight: 600;
    color: var(--primary);
    letter-spacing: 0.3px;
}
.video-swiper { padding-bottom: 2.5rem !important; }
.vid-swiper-pagination .swiper-pagination-bullet-active { background: var(--primary); }
</style>

<script>
function openVideoModal(videoId, title) {
    document.getElementById('videoModalTitle').textContent = title;
    document.getElementById('videoModalIframe').src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0';
    var modal = new bootstrap.Modal(document.getElementById('videoModal'));
    modal.show();
}
document.getElementById('videoModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('videoModalIframe').src = '';
});
new Swiper('.video-swiper', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: false,
    autoplay: { delay: 5000, disableOnInteraction: false },
    pagination: { el: '.vid-swiper-pagination', clickable: true },
    breakpoints: {
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 }
    }
});
</script>
<?php endif; ?>

<?php if ($isVisible('blog') && $latestPosts): ?>
<section class="section-padding">
    <div class="container">
        <div class="section-title fade-in">
            <span class="subtitle">Articles</span>
            <h2>Our Latest News</h2>
            <p>Stay informed with the latest medical news and health tips</p>
        </div>
        <div class="row g-4">
            <?php foreach ($latestPosts as $i => $post): ?>
            <div class="col-lg-4 col-md-6 fade-in delay-<?= $i + 1 ?>">
                <div class="card blog-card">
                    <div class="blog-img">
                        <?php if ($post['featured_image']): ?>
                            <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>">
                        <?php else: ?>
                            <i class="fas fa-newspaper placeholder-icon"></i>
                        <?php endif; ?>
                        <span class="blog-date-badge"><i class="far fa-calendar-alt me-1"></i><?= formatDate($post['created_at']) ?></span>
                    </div>
                    <div class="card-body">
                        <div class="blog-meta">
                            <i class="fas fa-user"></i> <?= e($post['author'] ?? 'Admin') ?>
                        </div>
                        <h5><?= e($post['title']) ?></h5>
                        <p class="text-muted small"><?= e(truncateText($post['content'] ?? '', 110)) ?></p>
                        <a href="/public/blog.php?slug=<?= e($post['slug']) ?>" class="read-more">
                            Read More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isVisible('location_section')): ?>
<?php
$locTitle = $locationData['title'] ?? 'Our Location';
$locSubtitle = $locationData['subtitle'] ?? 'Find Us';
$locDesc = $locationData['description'] ?? '';
$locAddress = $locationData['address'] ?? ($settings['address'] ?? '');
$locPhone = $locationData['phone'] ?? ($settings['phone'] ?? '');
$locEmail = $locationData['email'] ?? ($settings['email'] ?? '');
$locHours = $locationData['hours'] ?? 'Mon - Sat: 8:00 AM - 7:00 PM';
$locMapEmbed = $locationData['map_embed'] ?? '';
?>
<section class="location-section">
    <div class="container-fluid px-0">
        <div class="row g-0">
            <div class="col-lg-5">
                <div class="location-info-panel">
                    <div class="section-title text-start mb-4">
                        <span class="subtitle" style="padding-left:0;"><?= e($locSubtitle) ?></span>
                        <h2 style="color:#fff;"><?= e($locTitle) ?></h2>
                    </div>
                    <?php if ($locDesc): ?>
                    <p class="location-desc"><?= e($locDesc) ?></p>
                    <?php endif; ?>
                    <div class="location-details">
                        <?php if ($locAddress): ?>
                        <div class="location-item">
                            <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h6>Address</h6>
                                <p><?= e($locAddress) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($locPhone): ?>
                        <div class="location-item">
                            <div class="location-icon"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <h6>Phone</h6>
                                <p><?= e($locPhone) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($locEmail): ?>
                        <div class="location-item">
                            <div class="location-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <h6>Email</h6>
                                <p><?= e($locEmail) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($locHours): ?>
                        <div class="location-item">
                            <div class="location-icon"><i class="fas fa-clock"></i></div>
                            <div>
                                <h6>Working Hours</h6>
                                <p><?= e($locHours) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <a href="/public/contact.php" class="btn btn-light px-4 py-2 mt-3" style="border-radius:30px;font-weight:600;">
                        <i class="fas fa-directions me-2"></i>Get Directions
                    </a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="location-map-wrapper">
                    <?php if ($locMapEmbed): ?>
                    <iframe src="<?= e($locMapEmbed) ?>" width="100%" height="100%" style="border:0;min-height:500px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <?php else: ?>
                    <div class="map-placeholder">
                        <i class="fas fa-map-marked-alt"></i>
                        <p>Map location not configured yet.<br>Admin can add a Google Maps embed URL.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($isVisible('cta_ready')): ?>
<?php
$rdyHeading = $ctaReadyData['heading'] ?? 'Ready to Get Started?';
$rdyDesc = $ctaReadyData['description'] ?? 'Our team of experienced doctors is ready to help you. Book your appointment today and take the first step towards better health.';
$rdyBtn1Text = $ctaReadyData['button1_text'] ?? 'Book Appointment';
$rdyBtn1Link = $ctaReadyData['button1_link'] ?? '/public/appointment.php';
$rdyBtn2Text = $ctaReadyData['button2_text'] ?? 'Contact Us';
$rdyBtn2Link = $ctaReadyData['button2_link'] ?? '/public/contact.php';
?>
<section class="cta-section" style="background:linear-gradient(110deg,var(--secondary-dark),var(--secondary));">
    <div class="container position-relative" style="z-index:2;">
        <i class="fas fa-heartbeat mb-3" style="font-size:3rem;opacity:0.3;"></i>
        <h2><?= e($rdyHeading) ?></h2>
        <p class="mb-4 mx-auto" style="max-width:600px;"><?= e($rdyDesc) ?></p>
        <a href="<?= e($rdyBtn1Link) ?>" class="btn btn-light btn-lg px-5 me-2" style="border-radius:30px;font-weight:700;color:var(--secondary);">
            <i class="fas fa-calendar-check me-2"></i><?= e($rdyBtn1Text) ?>
        </a>
        <a href="<?= e($rdyBtn2Link) ?>" class="btn btn-outline-light btn-lg px-5" style="border-radius:30px;">
            <i class="fas fa-phone me-2"></i><?= e($rdyBtn2Text) ?>
        </a>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
