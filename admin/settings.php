<?php
$pageTitle = 'Site Settings';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = db();

$s = $pdo->query("SELECT * FROM foundation_settings WHERE id = 1")->fetch() ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['about_text', 'mission_text', 'vision_text', 'donation_policy_text', 'upi_id', 'razorpay_key_id', 'razorpay_key_secret'];
    $vals = [];
    foreach ($fields as $f) {
        $v = trim($_POST[$f] ?? '');
        $vals[] = $v === '' ? null : $v;
    }
    $fields[] = 'upi_qr_image';
    $vals[] = handle_upload('upi_qr_image', $s['upi_qr_image'] ?? null, 'uploads/settings');
    $sets = implode(', ', array_map(fn($f) => "`$f` = ?", $fields));
    $pdo->prepare("UPDATE foundation_settings SET $sets WHERE id = 1")->execute($vals);
    header('Location: settings.php?saved=1');
    exit;
}
?>
<?php if (isset($_GET['saved'])): ?><div class="flash ok">Settings saved.</div><?php endif; ?>

<form class="admin-form settings-section" method="post" enctype="multipart/form-data">
  <h2>About &amp; Mission</h2>
  <div>
    <label for="about_text">About Text</label>
    <textarea id="about_text" name="about_text" rows="4"><?= e($s['about_text'] ?? '') ?></textarea>
  </div>
  <div>
    <label for="mission_text">Mission</label>
    <textarea id="mission_text" name="mission_text" rows="3"><?= e($s['mission_text'] ?? '') ?></textarea>
  </div>
  <div>
    <label for="vision_text">Vision</label>
    <textarea id="vision_text" name="vision_text" rows="3"><?= e($s['vision_text'] ?? '') ?></textarea>
  </div>

  <h2>Donation Policy</h2>
  <div>
    <label for="donation_policy_text">Donation Policy &amp; Legal Text</label>
    <textarea id="donation_policy_text" name="donation_policy_text" rows="6"><?= e($s['donation_policy_text'] ?? '') ?></textarea>
  </div>

  <h2>Offline UPI Payment</h2>
  <div>
    <label for="upi_id">UPI ID</label>
    <input id="upi_id" name="upi_id" value="<?= e($s['upi_id'] ?? '') ?>" placeholder="sandeshfoundation@upi">
  </div>
  <div>
    <label for="upi_qr_image">UPI QR Code Image</label>
    <?php if (!empty($s['upi_qr_image'])): ?><div style="margin:.3rem 0"><img src="<?= e(img_url($s['upi_qr_image'])) ?>" alt="" style="max-width:140px;border-radius:6px;display:block"></div><?php endif; ?>
    <input id="upi_qr_image" name="upi_qr_image" type="file" accept="image/*">
  </div>

  <h2>Razorpay (Online Payments)</h2>
  <p class="muted">Leave blank to keep online payments disabled — donors will only see the offline UPI option.</p>
  <div>
    <label for="razorpay_key_id">Razorpay Key ID</label>
    <input id="razorpay_key_id" name="razorpay_key_id" value="<?= e($s['razorpay_key_id'] ?? '') ?>">
  </div>
  <div>
    <label for="razorpay_key_secret">Razorpay Key Secret</label>
    <input id="razorpay_key_secret" name="razorpay_key_secret" type="password" value="<?= e($s['razorpay_key_secret'] ?? '') ?>">
  </div>

  <button class="btn primary" type="submit">Save Settings</button>
</form>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
