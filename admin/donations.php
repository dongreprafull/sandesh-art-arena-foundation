<?php
require_once __DIR__ . '/../includes/payments.php';
$pageTitle = 'Donations';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($id && $action === 'verify') {
        $receiptNo = generate_receipt_no($id);
        $pdo->prepare("UPDATE donations SET payment_status = 'paid', receipt_no = ? WHERE id = ? AND payment_status = 'pending'")
            ->execute([$receiptNo, $id]);
    } elseif ($id && $action === 'reject') {
        $pdo->prepare("UPDATE donations SET payment_status = 'failed' WHERE id = ? AND payment_status = 'pending'")->execute([$id]);
    }
    header('Location: donations.php' . (isset($_GET['status']) ? '?status=' . e($_GET['status']) : ''));
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT * FROM donations";
$params = [];
if (in_array($statusFilter, ['pending', 'paid', 'failed'], true)) {
    $sql .= " WHERE payment_status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY created_at DESC LIMIT 200";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$donations = $stmt->fetchAll();
?>
<div class="admin-topbar-actions" style="margin-bottom:1rem">
  <a class="btn <?= $statusFilter === '' ? 'primary' : 'ghost' ?>" href="donations.php">All</a>
  <a class="btn <?= $statusFilter === 'pending' ? 'primary' : 'ghost' ?>" href="donations.php?status=pending">Pending</a>
  <a class="btn <?= $statusFilter === 'paid' ? 'primary' : 'ghost' ?>" href="donations.php?status=paid">Paid</a>
  <a class="btn <?= $statusFilter === 'failed' ? 'primary' : 'ghost' ?>" href="donations.php?status=failed">Failed</a>
</div>
<table class="admin-table">
  <thead><tr><th>Date</th><th>Donor</th><th>Contact</th><th>Amount</th><th>Method</th><th>Proof</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($donations as $d): ?>
    <tr>
      <td><?= e(date('d M Y', strtotime($d['created_at']))) ?></td>
      <td><?= e($d['donor_name']) ?><?= $d['campaign'] ? '<br><small class="muted">' . e($d['campaign']) . '</small>' : '' ?></td>
      <td><?= e($d['donor_phone']) ?><?= $d['donor_email'] ? '<br><small class="muted">' . e($d['donor_email']) . '</small>' : '' ?></td>
      <td>₹<?= number_format((float) $d['amount'], 2) ?></td>
      <td><?= $d['payment_method'] === 'upi' ? 'UPI' : 'Razorpay' ?></td>
      <td><?= $d['upi_proof_url'] ? '<a href="../' . e($d['upi_proof_url']) . '" target="_blank">View</a>' : '—' ?></td>
      <td><span class="status-pill status-<?= e($d['payment_status']) ?>"><?= e(ucfirst($d['payment_status'])) ?></span></td>
      <td>
        <?php if ($d['payment_status'] === 'pending'): ?>
          <form method="post" style="display:inline">
            <input type="hidden" name="id" value="<?= (int) $d['id'] ?>">
            <input type="hidden" name="action" value="verify">
            <button class="btn primary" type="submit" onclick="return confirm('Mark this donation as verified/paid?')">Verify</button>
          </form>
          <form method="post" style="display:inline">
            <input type="hidden" name="id" value="<?= (int) $d['id'] ?>">
            <input type="hidden" name="action" value="reject">
            <button class="btn danger" type="submit" onclick="return confirm('Mark this donation as failed?')">Reject</button>
          </form>
        <?php else: ?>
          <?= $d['receipt_no'] ? e($d['receipt_no']) : '—' ?>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$donations): ?><tr><td colspan="8">No donations found.</td></tr><?php endif; ?>
  </tbody>
</table>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
