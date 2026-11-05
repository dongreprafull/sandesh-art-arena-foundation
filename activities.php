<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Social Activities & Projects';
$stmt = db()->prepare("SELECT * FROM foundation_activities WHERE category = 'activity' ORDER BY sort_order");
$stmt->execute();
$items = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <p class="eyebrow">Programmes</p>
  <h1>Social Activities & <em>Projects</em></h1>
  <div class="category-tabs">
    <a href="/activities.php" class="active">Activities & Projects</a>
    <a href="/women-skill.php">Women Skill Development</a>
    <a href="/community.php">Student & Community</a>
  </div>
</section>
<section class="section-pad">
  <?php if ($items): ?>
  <div class="grid-cards">
    <?php foreach ($items as $a): ?>
      <div class="tile">
        <?php if ($a['image']): ?><img src="<?= e(img_url($a['image'])) ?>" alt="<?= e($a['title']) ?>"><?php endif; ?>
        <div class="tile-body">
          <h3><?= e($a['title']) ?></h3>
          <p><?= nl2br(e($a['description'] ?? '')) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p>Activities will appear here once added from the admin panel.</p>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
