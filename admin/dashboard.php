<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = db();

$totalRaised = (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM donations WHERE payment_status = 'paid'")->fetchColumn();
$pendingDonations = (int) $pdo->query("SELECT COUNT(*) FROM donations WHERE payment_status = 'pending'")->fetchColumn();
$totalDonors = (int) $pdo->query("SELECT COUNT(DISTINCT donor_phone) FROM donations WHERE payment_status = 'paid'")->fetchColumn();
$volunteerCount = (int) $pdo->query("SELECT COUNT(*) FROM volunteer_registrations")->fetchColumn();
$activityCount = (int) $pdo->query("SELECT COUNT(*) FROM foundation_activities")->fetchColumn();
$eventCount = (int) $pdo->query("SELECT COUNT(*) FROM foundation_events")->fetchColumn();
?>
<div class="admin-cards">
  <div class="admin-card">
    <p class="admin-card-label">Total Raised (Verified)</p>
    <p class="admin-card-value">₹<?= number_format($totalRaised, 2) ?></p>
  </div>
  <div class="admin-card">
    <p class="admin-card-label">Pending Verification</p>
    <p class="admin-card-value"><?= $pendingDonations ?></p>
  </div>
  <div class="admin-card">
    <p class="admin-card-label">Unique Donors</p>
    <p class="admin-card-value"><?= $totalDonors ?></p>
  </div>
  <div class="admin-card">
    <p class="admin-card-label">Volunteers Registered</p>
    <p class="admin-card-value"><?= $volunteerCount ?></p>
  </div>
  <div class="admin-card">
    <p class="admin-card-label">Activities Published</p>
    <p class="admin-card-value"><?= $activityCount ?></p>
  </div>
  <div class="admin-card">
    <p class="admin-card-label">Upcoming Events</p>
    <p class="admin-card-value"><?= $eventCount ?></p>
  </div>
</div>

<h2 style="margin-top:2rem">Quick Actions</h2>
<div class="admin-cards">
  <a class="btn primary" href="donations.php?status=pending">Review Pending Donations</a>
  <a class="btn ghost" href="content.php?table=foundation_activities&action=add">Add Activity</a>
  <a class="btn ghost" href="content.php?table=foundation_events&action=add">Add Event</a>
  <a class="btn ghost" href="settings.php">Edit Site Settings</a>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
