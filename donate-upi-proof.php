<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$donationId = (int) ($_POST['donation_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM donations WHERE id = ? AND payment_method = 'upi'");
$stmt->execute([$donationId]);
$donation = $stmt->fetch();

if (!$donation) {
    http_response_code(404);
    echo 'Donation record not found.';
    exit;
}

if (empty($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
    header('Location: donate.php?err=upload');
    exit;
}

$allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
$origName = $_FILES['proof']['name'];
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt, true)) {
    header('Location: donate.php?err=filetype');
    exit;
}

$uploadDir = __DIR__ . '/uploads/donation_proofs';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$fileName = 'proof_' . $donationId . '_' . time() . '.' . $ext;
$destPath = $uploadDir . '/' . $fileName;

if (!move_uploaded_file($_FILES['proof']['tmp_name'], $destPath)) {
    header('Location: donate.php?err=savefail');
    exit;
}

$publicUrl = 'uploads/donation_proofs/' . $fileName;
$pdo->prepare("UPDATE donations SET upi_proof_url = ? WHERE id = ?")->execute([$publicUrl, $donationId]);

header('Location: donation-success.php?id=' . $donationId . '&pending=1');
exit;
