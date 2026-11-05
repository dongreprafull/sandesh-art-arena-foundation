<?php
require_once __DIR__ . '/../../config.php';
require_admin();
$u = current_user();
$current = basename($_SERVER['PHP_SELF']);
$currentTable = $_GET['table'] ?? '';

function nav_is_active(string $href, string $current, string $table = ''): bool {
    if ($table !== '') {
        return $current === 'content.php' && ($_GET['table'] ?? '') === $table;
    }
    return $current === $href;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? e($pageTitle) . ' — Foundation Admin' : 'Foundation Admin' ?></title>
<link rel="stylesheet" href="https://artarenastudio.com/assets/style.css">
<link rel="stylesheet" href="https://artarenastudio.com/assets/artful-theme.css">
<link rel="stylesheet" href="/assets/foundation.css?v=<?= ASSET_VERSION ?>">
</head>
<body>
<div class="admin-topbar">
  <a class="brand" href="dashboard.php">
    <img src="https://artarenastudio.com/assets/sandesh-art-arena-logo.jpg" alt="Sandesh Art Arena">
    <i>Foundation Admin</i>
  </a>
  <div class="admin-topbar-actions">
    <a class="btn ghost" href="https://sandeshfoundation.artarenastudio.com/" target="_blank" rel="noopener">View Site ↗</a>
    <a class="btn danger" href="logout.php">Logout (<?= e($u['name'] ?? 'Admin') ?>)</a>
  </div>
</div>
<div class="admin-shell">
  <aside class="admin-side">
    <p class="admin-side-group">Overview</p>
    <a href="dashboard.php" class="<?= nav_is_active('dashboard.php', $current) ? 'active' : '' ?>">Dashboard</a>

    <p class="admin-side-group">Donations &amp; Volunteers</p>
    <a href="donations.php" class="<?= nav_is_active('donations.php', $current) ? 'active' : '' ?>">Donations</a>
    <a href="volunteers.php" class="<?= nav_is_active('volunteers.php', $current) ? 'active' : '' ?>">Volunteers</a>
    <a href="enquiries.php" class="<?= nav_is_active('enquiries.php', $current) ? 'active' : '' ?>">Enquiries</a>

    <p class="admin-side-group">Content</p>
    <a href="content.php?table=foundation_activities" class="<?= nav_is_active('', $current, 'foundation_activities') ? 'active' : '' ?>">Activities</a>
    <a href="content.php?table=foundation_events" class="<?= nav_is_active('', $current, 'foundation_events') ? 'active' : '' ?>">Events</a>
    <a href="content.php?table=foundation_gallery" class="<?= nav_is_active('', $current, 'foundation_gallery') ? 'active' : '' ?>">Gallery</a>

    <p class="admin-side-group">Settings</p>
    <a href="settings.php" class="<?= nav_is_active('settings.php', $current) ? 'active' : '' ?>">Site Settings</a>
  </aside>
  <div class="admin-wrap">
