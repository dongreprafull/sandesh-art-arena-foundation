<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Photo & Video Gallery';
$items = db()->query("SELECT * FROM foundation_gallery ORDER BY sort_order DESC")->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <p class="eyebrow">Gallery</p>
  <h1>Photo & <em>Video</em></h1>
</section>
<section class="section-pad">
  <?php if ($items): ?>
  <div class="gallery-grid">
    <?php foreach ($items as $g): ?>
      <figure>
        <?php if ($g['item_type'] === 'video'): ?>
          <video src="<?= e($g['url']) ?>" controls></video>
        <?php else: ?>
          <img src="<?= e($g['url']) ?>" alt="<?= e($g['title'] ?? '') ?>" loading="lazy">
        <?php endif; ?>
        <?php if ($g['title']): ?><figcaption><?= e($g['title']) ?></figcaption><?php endif; ?>
      </figure>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p>Photos and videos will appear here once added from the admin panel.</p>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
