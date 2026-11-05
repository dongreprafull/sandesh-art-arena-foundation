<?php
/** One-time: create Foundation tables + seed starter content. Delete after running. */
require __DIR__ . '/config.php';
header('Content-Type: text/plain');
$pdo = db();

try {
    $sql = file_get_contents(__DIR__ . '/schema_foundation.sql');
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        $pdo->exec($stmt);
    }
    echo "Tables created/verified.\n";

    $pdo->exec("INSERT IGNORE INTO foundation_settings (id, about_text, mission_text, vision_text, donation_policy_text, upi_id) VALUES (
      1,
      'Sandesh Foundation works on art-led community development — school art programmes, women\'s skill development, and student & community initiatives across Pune. Edit this from Admin > Settings.',
      'To make art-based learning and livelihood skills accessible to every community we work in.',
      'A society where creative skills open doors to dignity, income and confidence for women and students alike.',
      'Donations are used solely for the programmes described on this site. Receipts are issued for every donation. For 80G or other tax-related documentation, contact us directly. Refunds for failed online payments are processed within 7 working days.',
      'sandeshfoundation@upi'
    )");
    echo "Settings row seeded (edit UPI ID / Razorpay keys from Admin > Settings).\n";

    $img1 = 'https://artarenastudio.com/assets/three-sectors-artwork.png';
    $img2 = 'https://artarenastudio.com/assets/three-sectors-artwork-v2.png';
    $img3 = 'https://artarenastudio.com/assets/three-sectors-artwork-v3.png';

    $count = $pdo->query("SELECT COUNT(*) c FROM foundation_activities")->fetch()['c'];
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO foundation_activities (category, title, description, image, sort_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['activity', 'School Art Programme', 'Weekly art sessions run with partner schools across Pune, reaching 300+ students.', $img1, 1]);
        $stmt->execute(['activity', 'Community Mural Drive', 'Volunteers and local artists repaint public walls with community-themed murals.', $img2, 2]);
        $stmt->execute(['women_skill', 'Textile & Craft Training', 'A 6-week hands-on course teaching block printing and craft skills to local women, with market linkage support.', $img3, 1]);
        $stmt->execute(['women_skill', 'Handmade Products Collective', 'A collective of trained women artisans selling handmade goods through local markets and online.', $img1, 2]);
        $stmt->execute(['student_community', 'Scholarship Support', 'Need-based scholarship support for students pursuing art and design education.', $img2, 1]);
        $stmt->execute(['student_community', 'Weekend Community Library', 'A free weekend reading and activity space for children in underserved neighbourhoods.', $img3, 2]);
        echo "foundation_activities: 6 rows added.\n";
    } else {
        echo "foundation_activities: already has data, skipped.\n";
    }

    $count = $pdo->query("SELECT COUNT(*) c FROM foundation_events")->fetch()['c'];
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO foundation_events (title, description, event_date, location, image, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Annual Art Workshop for Kids', 'A full-day free workshop open to children from partner schools and the local community.', date('Y-m-d', strtotime('+21 days')), 'Sandesh Art Arena Studio, Pune', $img1, 1]);
        $stmt->execute(['Women Artisans Market Day', 'A pop-up market where trained women artisans sell their handmade work directly to buyers.', date('Y-m-d', strtotime('+45 days')), 'Pune', $img2, 2]);
        echo "foundation_events: 2 rows added.\n";
    } else {
        echo "foundation_events: already has data, skipped.\n";
    }

    $count = $pdo->query("SELECT COUNT(*) c FROM foundation_gallery")->fetch()['c'];
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO foundation_gallery (title, item_type, url, album, sort_order) VALUES (?, 'photo', ?, ?, ?)");
        $stmt->execute(['School art session', $img1, 'Activities', 1]);
        $stmt->execute(['Craft training batch', $img2, 'Women Skill Development', 2]);
        $stmt->execute(['Community library in session', $img3, 'Community', 3]);
        $stmt->execute(['Annual workshop, last year', $img1, 'Events', 4]);
        echo "foundation_gallery: 4 rows added.\n";
    } else {
        echo "foundation_gallery: already has data, skipped.\n";
    }

    echo "\nSetup complete. Delete setup.php and schema_foundation.sql now.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Setup failed: " . $e->getMessage() . "\n";
}
