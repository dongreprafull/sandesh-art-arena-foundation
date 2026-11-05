<?php
$pageTitle = 'Volunteers';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id && ($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare("DELETE FROM volunteer_registrations WHERE id = ?")->execute([$id]);
    }
    header('Location: volunteers.php');
    exit;
}

$volunteers = $pdo->query("SELECT * FROM volunteer_registrations ORDER BY created_at DESC")->fetchAll();
?>
<table class="admin-table">
  <thead><tr><th>Date</th><th>Name</th><th>Phone</th><th>Email</th><th>Message</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($volunteers as $v): ?>
    <tr>
      <td><?= e(date('d M Y', strtotime($v['created_at']))) ?></td>
      <td><?= e($v['name']) ?></td>
      <td><?= e($v['phone']) ?></td>
      <td><?= e($v['email'] ?? '—') ?></td>
      <td><?= e($v['message'] ?? '—') ?></td>
      <td>
        <form method="post" style="display:inline" onsubmit="return confirm('Remove this volunteer record?')">
          <input type="hidden" name="id" value="<?= (int) $v['id'] ?>">
          <input type="hidden" name="action" value="delete">
          <button class="btn danger" type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$volunteers): ?><tr><td colspan="6">No volunteer registrations yet.</td></tr><?php endif; ?>
  </tbody>
</table>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
