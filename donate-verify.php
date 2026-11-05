<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/payments.php';

$pdo = db();
$donationId = (int) ($_POST['donation_id'] ?? 0);
$paymentId = $_POST['razorpay_payment_id'] ?? '';
$orderId = $_POST['razorpay_order_id'] ?? '';
$signature = $_POST['razorpay_signature'] ?? '';

if (!$donationId || !$paymentId || !$orderId || !$signature) {
    http_response_code(400);
    echo 'Missing payment details.';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM donations WHERE id = ?");
$stmt->execute([$donationId]);
$donation = $stmt->fetch();

if (!$donation || $donation['razorpay_order_id'] !== $orderId) {
    http_response_code(404);
    echo 'Donation record not found.';
    exit;
}

$s = fsettings();
$keySecret = $s['razorpay_key_secret'] ?? '';

if ($keySecret !== '' && razorpay_verify_signature($orderId, $paymentId, $signature, $keySecret)) {
    $receiptNo = generate_receipt_no($donationId);
    $pdo->prepare("UPDATE donations SET payment_status = 'paid', razorpay_payment_id = ?, receipt_no = ? WHERE id = ?")
        ->execute([$paymentId, $receiptNo, $donationId]);
    header('Location: donation-success.php?id=' . $donationId);
    exit;
} else {
    $pdo->prepare("UPDATE donations SET payment_status = 'failed' WHERE id = ?")->execute([$donationId]);
    header('Location: donation-success.php?id=' . $donationId . '&failed=1');
    exit;
}
