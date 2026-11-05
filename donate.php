<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/payments.php';
$pageTitle = 'Donate';
$pdo = db();
$s = fsettings();

$error = '';
$donation = null;
$razorOrder = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['step'] ?? '') === 'create') {
    $name = trim($_POST['donor_name'] ?? '');
    $email = trim($_POST['donor_email'] ?? '');
    $phone = trim($_POST['donor_phone'] ?? '');
    $amount = (float) ($_POST['amount'] ?? 0);
    $method = $_POST['method'] ?? '';
    $campaign = trim($_POST['campaign'] ?? '');

    if ($name === '' || $phone === '') {
        $error = 'Please share your name and phone number.';
    } elseif ($amount < 50) {
        $error = 'Minimum donation amount is ₹50.';
    } elseif (!in_array($method, ['razorpay', 'upi'], true)) {
        $error = 'Please choose a payment method.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO donations (donor_name, donor_email, donor_phone, amount, campaign, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$name, $email ?: null, $phone, $amount, $campaign ?: null, $method]);
        $donationId = (int) $pdo->lastInsertId();

        if ($method === 'razorpay') {
            $keyId = $s['razorpay_key_id'] ?? '';
            $keySecret = $s['razorpay_key_secret'] ?? '';
            if ($keyId === '' || $keySecret === '') {
                $error = 'Online payments are not configured yet. Please use the offline UPI option or contact us directly.';
            } else {
                $order = razorpay_create_order($amount, $keyId, $keySecret, 'SF' . $donationId . '-' . time());
                if (!$order || empty($order['id'])) {
                    $error = 'Could not start the online payment right now. Please try the offline UPI option.';
                } else {
                    $pdo->prepare("UPDATE donations SET razorpay_order_id = ? WHERE id = ?")->execute([$order['id'], $donationId]);
                    $donation = ['id' => $donationId, 'name' => $name, 'email' => $email, 'phone' => $phone, 'amount' => $amount, 'method' => $method];
                    $razorOrder = $order;
                }
            }
        } else {
            $donation = ['id' => $donationId, 'name' => $name, 'email' => $email, 'phone' => $phone, 'amount' => $amount, 'method' => $method];
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <p class="eyebrow">Support the mission</p>
  <h1>Make a <em>Donation</em></h1>
  <p>Every contribution helps fund art activities, women's skill-development workshops, and student-community programs.</p>
</section>

<section class="section-pad">
  <?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>

  <?php if ($donation && $donation['method'] === 'upi'): ?>
    <div class="donate-wrap">
      <div class="donate-side upi-box">
        <h3>Pay via UPI</h3>
        <p>UPI ID: <strong><?= e($s['upi_id'] ?? 'not configured') ?></strong></p>
        <?php if (!empty($s['upi_qr_image'])): ?>
          <img src="<?= e($s['upi_qr_image']) ?>" alt="UPI QR code" style="max-width:220px;border-radius:12px;margin:1rem 0">
        <?php endif; ?>
        <p>Amount: <strong>₹<?= e(number_format($donation['amount'], 2)) ?></strong></p>
        <p>After paying, upload a screenshot of the payment below. Our team will verify it and send you a receipt.</p>
      </div>
      <div class="donate-side">
        <form class="site-form" method="post" action="donate-upi-proof.php" enctype="multipart/form-data">
          <input type="hidden" name="donation_id" value="<?= (int) $donation['id'] ?>">
          <div>
            <label for="proof">Payment Screenshot</label>
            <input id="proof" name="proof" type="file" accept="image/*,.pdf" required>
          </div>
          <button class="button" type="submit">Submit Payment Proof <b>→</b></button>
        </form>
      </div>
    </div>

  <?php elseif ($donation && $donation['method'] === 'razorpay' && $razorOrder): ?>
    <div class="flash ok">Click below to complete your secure payment.</div>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <button id="rzp-pay-btn" class="button">Pay ₹<?= e(number_format($donation['amount'], 2)) ?> Now <b>→</b></button>
    <script>
      document.getElementById('rzp-pay-btn').addEventListener('click', function () {
        var options = {
          key: <?= json_encode($s['razorpay_key_id']) ?>,
          amount: <?= (int) round($donation['amount'] * 100) ?>,
          currency: 'INR',
          order_id: <?= json_encode($razorOrder['id']) ?>,
          name: 'Sandesh Foundation',
          description: 'Donation',
          handler: function (resp) {
            var f = document.createElement('form');
            f.method = 'POST';
            f.action = 'donate-verify.php';
            var fields = {
              razorpay_payment_id: resp.razorpay_payment_id,
              razorpay_order_id: resp.razorpay_order_id,
              razorpay_signature: resp.razorpay_signature,
              donation_id: <?= (int) $donation['id'] ?>
            };
            for (var k in fields) {
              var i = document.createElement('input');
              i.type = 'hidden'; i.name = k; i.value = fields[k];
              f.appendChild(i);
            }
            document.body.appendChild(f);
            f.submit();
          },
          prefill: {
            name: <?= json_encode($donation['name']) ?>,
            email: <?= json_encode($donation['email']) ?>,
            contact: <?= json_encode($donation['phone']) ?>
          },
          theme: { color: '#ef5f50' }
        };
        var rzp = new Razorpay(options);
        rzp.open();
      });
    </script>

  <?php else: ?>
    <form class="site-form donate-form" method="post" id="donate-form">
      <input type="hidden" name="step" value="create">
      <div class="amount-grid">
        <?php foreach ([500, 1000, 2500, 5000, 10000] as $amt): ?>
          <label class="amount-chip">
            <input type="radio" name="amount_preset" value="<?= $amt ?>" onclick="document.getElementById('amount').value=<?= $amt ?>">
            ₹<?= number_format($amt) ?>
          </label>
        <?php endforeach; ?>
      </div>
      <div>
        <label for="amount">Amount (₹)</label>
        <input id="amount" name="amount" type="number" min="50" step="1" required value="<?= e($_POST['amount'] ?? '1000') ?>">
      </div>
      <div>
        <label for="campaign">Campaign (optional)</label>
        <input id="campaign" name="campaign" placeholder="e.g. Women Skill Development" value="<?= e($_POST['campaign'] ?? '') ?>">
      </div>
      <div>
        <label for="donor_name">Full Name</label>
        <input id="donor_name" name="donor_name" required value="<?= e($_POST['donor_name'] ?? '') ?>">
      </div>
      <div>
        <label for="donor_phone">Phone</label>
        <input id="donor_phone" name="donor_phone" required value="<?= e($_POST['donor_phone'] ?? '') ?>">
      </div>
      <div>
        <label for="donor_email">Email (optional, for receipt)</label>
        <input id="donor_email" name="donor_email" type="email" value="<?= e($_POST['donor_email'] ?? '') ?>">
      </div>
      <div class="method-tabs">
        <label><input type="radio" name="method" value="razorpay" checked> Pay Online (Razorpay)</label>
        <label><input type="radio" name="method" value="upi"> Offline UPI Transfer</label>
      </div>
      <button class="button" type="submit">Continue <b>→</b></button>
    </form>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
