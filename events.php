<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Events & Workshops';
$events = db()->query("SELECT * FROM foundation_events ORDER BY event_date IS NULL, event_date")->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <p class="eyebrow">Get Involved</p>
  <h1>Events & <em>Workshops</em></h1>
</section>
<section class="section-pad">
  <?php if ($events): ?>
  <div class="grid-cards">
    <?php foreach ($events as $ev): ?>
      <div class="event-card">
        <?php if ($ev['image']): ?><img src="<?= e($ev['image']) ?>" alt="<?= e($ev['title']) ?>"><?php endif; ?>
        <div class="event-body">
          <?php if ($ev['event_date']): ?><span class="event-date-badge"><?= e(date('d M Y', strtotime($ev['event_date']))) ?></span><?php endif; ?>
          <h3><?= e($ev['title']) ?></h3>
          <p><?= nl2br(e($ev['description'] ?? '')) ?></p>
          <?php if ($ev['location']): ?><p class="location">📍 <?= e($ev['location']) ?></p><?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p>Events will appear here once added from the admin panel.</p>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
