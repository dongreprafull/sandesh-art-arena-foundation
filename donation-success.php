<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Thank You';
$pdo = db();

$id = (int) ($_GET['id'] ?? 0);
$pending = isset($_GET['pending']);
$failed = isset($_GET['failed']);

$stmt = $pdo->prepare("SELECT * FROM donations WHERE id = ?");
$stmt->execute([$id]);
$donation = $stmt->fetch();

include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <p class="eyebrow">Thank You</p>
  <h1><?= $failed ? 'Payment Not Completed' : 'Your Generosity Matters' ?></h1>
</section>
<section class="section-pad">
  <?php if (!$donation): ?>
    <p>We couldn't find that donation record.</p>
  <?php elseif ($failed): ?>
    <div class="flash err">The payment could not be verified. No amount has been charged to a verified transaction. Please try again or use the offline UPI option.</div>
    <p><a class="button" href="donate.php">Try Again →</a></p>
  <?php elseif ($pending || $donation['payment_status'] === 'pending'): ?>
    <div class="flash ok">Thank you, <?= e($donation['donor_name']) ?>! We've received your donation details and will verify your payment shortly. A confirmation receipt will follow once verified.</div>
    <div class="receipt-card">
      <div class="receipt-row"><span>Donor</span><strong><?= e($donation['donor_name']) ?></strong></div>
      <div class="receipt-row"><span>Amount</span><strong class="receipt-amount">₹<?= e(number_format((float)$donation['amount'], 2)) ?></strong></div>
      <div class="receipt-row"><span>Method</span><strong><?= $donation['payment_method'] === 'upi' ? 'Offline UPI Transfer' : 'Online (Razorpay)' ?></strong></div>
      <div class="receipt-row"><span>Status</span><strong>Pending Verification</strong></div>
    </div>
  <?php else: ?>
    <div class="flash ok">Thank you, <?= e($donation['donor_name']) ?>! Your donation has been received and verified.</div>
    <div class="receipt-card" id="receipt">
      <h3>Donation Receipt</h3>
      <div class="receipt-row"><span>Receipt No.</span><strong><?= e($donation['receipt_no'] ?? '—') ?></strong></div>
      <div class="receipt-row"><span>Date</span><strong><?= e(date('d M Y', strtotime($donation['created_at']))) ?></strong></div>
      <div class="receipt-row"><span>Donor</span><strong><?= e($donation['donor_name']) ?></strong></div>
      <?php if ($donation['campaign']): ?><div class="receipt-row"><span>Campaign</span><strong><?= e($donation['campaign']) ?></strong></div><?php endif; ?>
      <div class="receipt-row"><span>Payment Method</span><strong><?= $donation['payment_method'] === 'upi' ? 'UPI' : 'Razorpay' ?></strong></div>
      <div class="receipt-row"><span>Amount</span><strong class="receipt-amount">₹<?= e(number_format((float)$donation['amount'], 2)) ?></strong></div>
    </div>
    <p class="no-print"><button class="button" onclick="window.print()">Print Receipt 🖨</button></p>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
