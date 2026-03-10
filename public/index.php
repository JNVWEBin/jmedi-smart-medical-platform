<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

$departments    = getDepartments($pdo);
$doctors        = getDoctors($pdo);
$testimonials   = getTestimonials($pdo);
$latestPosts    = getPosts($pdo, true, 3);
$emergencyPhone = $settings['emergency_phone'] ?? '';
$heroSlides     = getHeroSlides($pdo, true);
$infoStripData      = getHomeSection($pdo, 'info_strip');
$aboutData          = getHomeSection($pdo, 'about_section');
$wcuData            = getHomeSection($pdo, 'why_choose_us');
$locationData       = getHomeSection($pdo, 'location_section');
$ctaCheckupData     = getHomeSection($pdo, 'cta_checkup');
$appointmentData    = getHomeSection($pdo, 'appointment_section');
$processData        = getHomeSection($pdo, 'process_section');
$statsData          = getHomeSection($pdo, 'stats_section');
$ctaReadyData       = getHomeSection($pdo, 'cta_ready');
$videosData         = getHomeSection($pdo, 'our_videos') ?: ['title' => 'Our Latest Videos', 'subtitle' => 'Watch health tips, facility tours and expert talks', 'videos' => []];
$secVis = getHomeSection($pdo, 'section_visibility');
if (empty($secVis)) $secVis = [];
$isVisible = function($key) use ($secVis) { return !isset($secVis[$key]) || !empty($secVis[$key]); };

try {
    $beforeAfterItems = $pdo->query("SELECT * FROM before_after_gallery WHERE status=1 ORDER BY sort_order ASC, gallery_id ASC LIMIT 6")->fetchAll();
} catch (Exception $e) {
    $beforeAfterItems = [];
}

$siteTemplate = $settings['site_template'] ?? 'hospital';
$templateFile = __DIR__ . '/templates/home-' . $siteTemplate . '.php';
if (!file_exists($templateFile)) {
    $templateFile = __DIR__ . '/templates/home-hospital.php';
}

include $templateFile;
