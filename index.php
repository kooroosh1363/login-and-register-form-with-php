<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

if (authenticated_user() !== null) redirect('/dashboard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = LoginValidator::normalize($_POST);
    $errors = LoginValidator::validate($data);

    if (!csrf_is_valid($_POST['_token'] ?? null)) {
        $errors['form'] = 'Your session expired. Please try again.';
    }

    if ($errors === []) {
        $result = $authService->attempt($data['identity'], $data['password'], time());
        if ($result['ok'] && is_array($result['user'])) {
            sign_in_session($result['user']);
            redirect('/dashboard.php');
        }
        $errors['form'] = $result['reason'] === 'locked'
            ? 'Too many sign-in attempts. Please wait a few minutes.'
            : 'The username/email or password is incorrect.';
    }

    flash('login_errors', $errors);
    flash('login_identity', $data['identity']);
    redirect('/');
}

$errors = pull_flash('login_errors', []);
$oldIdentity = pull_flash('login_identity', '');
$notice = pull_flash('notice');
$registered = pull_flash('registered');

function field_error(array $errors, string $field): ?string {
    return isset($errors[$field]) && is_string($errors[$field]) ? $errors[$field] : null;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="AccountFoundry secure PHP registration and login demo.">
<meta name="color-scheme" content="light dark">
<title>AccountFoundry — Sign in</title>
<link rel="stylesheet" href="/main.css">
</head>
<body>
<a class="skip-link" href="#login-form">Skip to sign in</a>
<main class="auth-shell">
<section class="story">
<a class="brand" href="/"><span class="brand-mark">AF</span><span>AccountFoundry</span></a>
<div class="story-copy">
<p class="eyebrow">Self-service accounts / secure PHP</p>
<h1>Create accounts safely. Sign in without shortcuts.</h1>
<p>A focused registration + login demo with hashed passwords, uniqueness constraints, CSRF protection, session hardening, and throttled authentication.</p>
</div>
<ul class="feature-list">
<li><strong>Unique identities</strong><span>Username and email enforced by the database.</span></li>
<li><strong>Hashed secrets</strong><span>Passwords never persist in plaintext.</span></li>
<li><strong>Portable storage</strong><span>SQLite locally, MySQL in production-style CI.</span></li>
</ul>
</section>
<section class="form-panel">
<div class="form-card">
<p class="section-index">Existing account / 01</p>
<h2>Sign in</h2>
<p class="form-intro">Use your username or email.</p>
<?php if (is_string($registered) && $registered !== ''): ?><div class="notice notice--success" role="status"><?= e($registered) ?></div><?php endif; ?>
<?php if (is_string($notice) && $notice !== ''): ?><div class="notice" role="status"><?= e($notice) ?></div><?php endif; ?>
<?php if (($errors['form'] ?? null) !== null): ?><div class="notice notice--error" role="alert"><?= e((string)$errors['form']) ?></div><?php endif; ?>

<form id="login-form" method="post" action="/" novalidate>
<input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
<div class="field">
<label for="identity">Username or email</label>
<input id="identity" name="identity" autocomplete="username" maxlength="254" required value="<?= e(is_string($oldIdentity)?$oldIdentity:'') ?>" <?= field_error($errors,'identity') ? 'aria-invalid="true" aria-describedby="identity-error"' : '' ?>>
<?php if ($error=field_error($errors,'identity')): ?><p id="identity-error" class="field-error"><?= e($error) ?></p><?php endif; ?>
</div>
<div class="field">
<label for="password">Password</label>
<input id="password" name="password" type="password" autocomplete="current-password" maxlength="4096" required <?= field_error($errors,'password') ? 'aria-invalid="true" aria-describedby="password-error"' : '' ?>>
<?php if ($error=field_error($errors,'password')): ?><p id="password-error" class="field-error"><?= e($error) ?></p><?php endif; ?>
</div>
<button class="primary-button" type="submit">Sign in →</button>
</form>

<p class="switch-link">New here? <a href="/register.php">Create an account</a></p>
</div>
</section>
</main>
</body>
</html>