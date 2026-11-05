<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Home';
$pdo = db();
$s = fsettings();
$activities = $pdo->query("SELECT * FROM foundation_activities ORDER BY sort_order LIMIT 3")->fetchAll();
$events = $pdo->query("SELECT * FROM foundation_events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 2")->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <p class="eyebrow">Sandesh Art Arena · Foundation</p>
  <h1>Art that <em>builds community.</em></h1>
  <p><?= e(mb_strimwidth($s['about_text'] ?? '', 0, 180, '…')) ?></p>
  <p style="margin-top:1.5rem">
    <a class="button" href="/donate.php">Donate Now <b>→</b></a>
  </p>
</section>

<section class="section-pad">
  <h2 style="margin-bottom:1rem">Our Work</h2>
  <?php if ($activities): ?>
  <div class="grid-cards">
    <?php foreach ($activities as $a): ?>
      <div class="tile">
        <img src="<?= e(img_url($a['image']) ?: 'https://artarenastudio.com/assets/three-sectors-artwork.png') ?>" alt="<?= e($a['title']) ?>">
        <div class="tile-body">
          <h3><?= e($a['title']) ?></h3>
          <p><?= e(mb_strimwidth($a['description'] ?? '', 0, 90, '…')) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <p style="margin-top:1.2rem"><a href="/activities.php">See all activities →</a></p>
</section>

<?php if ($events): ?>
<section class="section-pad">
  <h2 style="margin-bottom:1rem">Upcoming Events</h2>
  <div class="grid-cards">
    <?php foreach ($events as $ev): ?>
      <div class="event-card">
        <img src="<?= e(img_url($ev['image']) ?: 'https://artarenastudio.com/assets/three-sectors-artwork-v2.png') ?>" alt="<?= e($ev['title']) ?>">
        <div class="event-body">
          <span class="event-date-badge"><?= e(date('d M Y', strtotime($ev['event_date']))) ?></span>
          <h3><?= e($ev['title']) ?></h3>
          <p><?= e(mb_strimwidth($ev['description'] ?? '', 0, 90, '…')) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <p style="margin-top:1.2rem"><a href="/events.php">All events & workshops →</a></p>
</section>
<?php endif; ?>

<section class="connect">
  <p class="eyebrow">Get Involved</p>
  <h2>Help us do <em>more.</em></h2>
  <p>Donate to support our programmes, or volunteer your time.</p>
  <p style="display:flex;gap:1rem;flex-wrap:wrap">
    <a class="button" href="/donate.php">Donate <b>→</b></a>
    <a class="button" href="/volunteer.php">Volunteer <b>→</b></a>
  </p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
