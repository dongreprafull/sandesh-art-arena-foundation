<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Student & Community Initiatives';
$stmt = db()->prepare("SELECT * FROM foundation_activities WHERE category = 'student_community' ORDER BY sort_order");
$stmt->execute();
$items = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <p class="eyebrow">Programmes</p>
  <h1>Student & Community <em>Initiatives</em></h1>
  <div class="category-tabs">
    <a href="/activities.php">Activities & Projects</a>
    <a href="/women-skill.php">Women Skill Development</a>
    <a href="/community.php" class="active">Student & Community</a>
  </div>
</section>
<section class="section-pad">
  <?php if ($items): ?>
  <div class="grid-cards">
    <?php foreach ($items as $a): ?>
      <div class="tile">
        <?php if ($a['image']): ?><img src="<?= e($a['image']) ?>" alt="<?= e($a['title']) ?>"><?php endif; ?>
        <div class="tile-body">
          <h3><?= e($a['title']) ?></h3>
          <p><?= nl2br(e($a['description'] ?? '')) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p>Initiatives will appear here once added from the admin panel.</p>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
