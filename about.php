<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'About & Mission';
$s = fsettings();
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <p class="eyebrow">About</p>
  <h1>About the <em>Foundation</em></h1>
</section>
<section class="section-pad">
  <div class="about-grid">
    <div>
      <p><?= nl2br(e($s['about_text'] ?? '')) ?></p>
    </div>
    <img src="https://artarenastudio.com/assets/three-sectors-artwork-v2.png" alt="Sandesh Foundation">
  </div>
</section>
<section class="section-pad" style="padding-top:0">
  <div class="grid-cards">
    <div class="tile">
      <div class="tile-body">
        <span class="tag">Mission</span>
        <p style="font-size:1.05rem"><?= nl2br(e($s['mission_text'] ?? '')) ?></p>
      </div>
    </div>
    <div class="tile">
      <div class="tile-body">
        <span class="tag">Vision</span>
        <p style="font-size:1.05rem"><?= nl2br(e($s['vision_text'] ?? '')) ?></p>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
