<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Volunteer Registration';
$pdo = db();
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $phone === '') {
        $error = 'Name and phone are required.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO volunteer_registrations (name, phone, email, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email ?: null, $message ?: null]);
        $success = true;
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <p class="eyebrow">Get Involved</p>
  <h1>Volunteer <em>Registration</em></h1>
  <p>Tell us a bit about yourself — we'll reach out when there's a fit for an upcoming activity or event.</p>
</section>
<section class="section-pad">
  <?php if ($success): ?>
    <div class="flash ok">Thanks for signing up to volunteer — we'll be in touch.</div>
  <?php else: ?>
    <?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
    <form class="site-form" method="post">
      <div>
        <label for="name">Full Name</label>
        <input id="name" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
      </div>
      <div>
        <label for="phone">Phone</label>
        <input id="phone" name="phone" required value="<?= e($_POST['phone'] ?? '') ?>">
      </div>
      <div>
        <label for="email">Email (optional)</label>
        <input id="email" name="email" type="email" value="<?= e($_POST['email'] ?? '') ?>">
      </div>
      <div>
        <label for="message">How would you like to help? (optional)</label>
        <textarea id="message" name="message"></textarea>
      </div>
      <button class="button" type="submit">Register as Volunteer <b>→</b></button>
    </form>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
