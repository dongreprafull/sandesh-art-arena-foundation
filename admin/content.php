<?php
$tables = [
    'foundation_activities' => [
        'label' => 'Activities',
        'fields' => [
            'category' => ['label' => 'Category', 'type' => 'select', 'required' => true, 'options' => [
                'activity' => 'General Activity', 'women_skill' => 'Women Skill Development', 'student_community' => 'Student & Community',
            ]],
            'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
            'description' => ['label' => 'Description', 'type' => 'textarea'],
            'image' => ['label' => 'Image URL', 'type' => 'text'],
            'sort_order' => ['label' => 'Sort Order', 'type' => 'number'],
        ],
    ],
    'foundation_events' => [
        'label' => 'Events',
        'fields' => [
            'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
            'description' => ['label' => 'Description', 'type' => 'textarea'],
            'event_date' => ['label' => 'Event Date', 'type' => 'date'],
            'location' => ['label' => 'Location', 'type' => 'text'],
            'image' => ['label' => 'Image URL', 'type' => 'text'],
            'sort_order' => ['label' => 'Sort Order', 'type' => 'number'],
        ],
    ],
    'foundation_gallery' => [
        'label' => 'Gallery',
        'fields' => [
            'title' => ['label' => 'Title', 'type' => 'text'],
            'item_type' => ['label' => 'Type', 'type' => 'select', 'required' => true, 'options' => ['photo' => 'Photo', 'video' => 'Video']],
            'url' => ['label' => 'Media URL', 'type' => 'text', 'required' => true],
            'album' => ['label' => 'Album', 'type' => 'text'],
            'sort_order' => ['label' => 'Sort Order', 'type' => 'number'],
        ],
    ],
];

$table = $_GET['table'] ?? '';
if (!isset($tables[$table])) {
    $table = 'foundation_activities';
}
$config = $tables[$table];
$pageTitle = $config['label'];
require_once __DIR__ . '/includes/admin_header.php';
$pdo = db();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    $pdo->prepare("DELETE FROM `$table` WHERE id = ?")->execute([$id]);
    header('Location: content.php?table=' . urlencode($table));
    exit;
}

// Handle add/edit save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    $id = (int) ($_POST['id'] ?? 0);
    $cols = [];
    $vals = [];
    foreach ($config['fields'] as $key => $field) {
        $val = trim($_POST[$key] ?? '');
        if ($field['type'] === 'number') $val = $val === '' ? 0 : (int) $val;
        $cols[] = $key;
        $vals[] = $val === '' ? null : $val;
    }
    if ($id) {
        $sets = implode(', ', array_map(fn($c) => "`$c` = ?", $cols));
        $pdo->prepare("UPDATE `$table` SET $sets WHERE id = ?")->execute([...$vals, $id]);
    } else {
        $placeholders = implode(', ', array_fill(0, count($cols), '?'));
        $colList = implode(', ', array_map(fn($c) => "`$c`", $cols));
        $pdo->prepare("INSERT INTO `$table` ($colList) VALUES ($placeholders)")->execute($vals);
    }
    header('Location: content.php?table=' . urlencode($table));
    exit;
}

$action = $_GET['action'] ?? 'list';
$editRow = null;
if (in_array($action, ['add', 'edit'], true)) {
    if ($action === 'edit') {
        $id = (int) ($_GET['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
        $stmt->execute([$id]);
        $editRow = $stmt->fetch();
    }
    ?>
    <h2><?= $action === 'edit' ? 'Edit' : 'Add' ?> <?= e($config['label']) ?></h2>
    <form class="admin-form" method="post">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) ($editRow['id'] ?? 0) ?>">
      <?php foreach ($config['fields'] as $key => $field): ?>
        <div>
          <label for="<?= e($key) ?>"><?= e($field['label']) ?></label>
          <?php $val = $editRow[$key] ?? ''; ?>
          <?php if ($field['type'] === 'textarea'): ?>
            <textarea id="<?= e($key) ?>" name="<?= e($key) ?>" <?= !empty($field['required']) ? 'required' : '' ?>><?= e($val) ?></textarea>
          <?php elseif ($field['type'] === 'select'): ?>
            <select id="<?= e($key) ?>" name="<?= e($key) ?>">
              <?php foreach ($field['options'] as $optVal => $optLabel): ?>
                <option value="<?= e($optVal) ?>" <?= $val === $optVal ? 'selected' : '' ?>><?= e($optLabel) ?></option>
              <?php endforeach; ?>
            </select>
          <?php else: ?>
            <input id="<?= e($key) ?>" name="<?= e($key) ?>" type="<?= $field['type'] === 'number' ? 'number' : ($field['type'] === 'date' ? 'date' : 'text') ?>"
              <?= !empty($field['required']) ? 'required' : '' ?> value="<?= e((string) $val) ?>">
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      <button class="btn primary" type="submit">Save</button>
      <a class="btn ghost" href="content.php?table=<?= urlencode($table) ?>">Cancel</a>
    </form>
    <?php
} else {
    $rows = $pdo->query("SELECT * FROM `$table` ORDER BY sort_order DESC, id DESC")->fetchAll();
    ?>
    <div class="admin-topbar-actions" style="margin-bottom:1rem">
      <a class="btn primary" href="content.php?table=<?= urlencode($table) ?>&action=add">+ Add <?= e($config['label']) ?></a>
    </div>
    <table class="admin-table">
      <thead>
        <tr>
          <?php foreach ($config['fields'] as $key => $field): ?>
            <?php if ($field['type'] !== 'textarea'): ?><th><?= e($field['label']) ?></th><?php endif; ?>
          <?php endforeach; ?>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <?php foreach ($config['fields'] as $key => $field): ?>
              <?php if ($field['type'] !== 'textarea'): ?>
                <td><?= e((string) ($row[$key] ?? '')) ?></td>
              <?php endif; ?>
            <?php endforeach; ?>
            <td>
              <a class="btn ghost" href="content.php?table=<?= urlencode($table) ?>&action=edit&id=<?= (int) $row['id'] ?>">Edit</a>
              <form method="post" style="display:inline" onsubmit="return confirm('Delete this item?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                <button class="btn danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="10">No records yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
    <?php
}
require_once __DIR__ . '/includes/admin_footer.php';
