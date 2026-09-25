<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';
$sessionUser = require_authenticated_user();
$user = $userRepository->findById($sessionUser['id']);
if ($user === null) { sign_out_session(); header('Location: /', true, 303); exit; }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="AccountFoundry authenticated account dashboard.">
<meta name="color-scheme" content="light dark">
<title>Dashboard — AccountFoundry</title>
<link rel="stylesheet" href="/main.css">
</head>
<body>
<header class="dashboard-header">
<a class="brand" href="/dashboard.php"><span class="brand-mark">AF</span><span>AccountFoundry</span></a>
<form method="post" action="/logout.php"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><button class="secondary-button" type="submit">Sign out</button></form>
</header>
<main class="dashboard-shell">
<p class="eyebrow">Authenticated account</p>
<h1>Welcome, <?= e($user['username']) ?>.</h1>
<p class="dashboard-lead">Your account is stored using the <strong><?= e($userRepository->driver()) ?></strong> PDO driver.</p>
<section class="account-grid">
<article><span>Username</span><strong><?= e($user['username']) ?></strong></article>
<article><span>Email</span><strong><?= e($user['email']) ?></strong></article>
<article><span>Phone</span><strong><?= e($user['phone'] ?? 'Not provided') ?></strong></article>
<article><span>Created</span><strong><?= e($user['created_at']) ?></strong></article>
</section>
</main>
</body>
</html>