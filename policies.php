<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Donation Policies & Legal Information';
$s = fsettings();
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <p class="eyebrow">Legal</p>
  <h1>Donation Policies & <em>Legal Information</em></h1>
</section>
<section class="section-pad">
  <div style="max-width:40rem">
    <p><?= nl2br(e($s['donation_policy_text'] ?? '')) ?></p>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
