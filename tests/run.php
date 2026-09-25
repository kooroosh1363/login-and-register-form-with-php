<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Config.php';
require_once dirname(__DIR__) . '/src/Database.php';
require_once dirname(__DIR__) . '/src/RegistrationValidator.php';
require_once dirname(__DIR__) . '/src/LoginValidator.php';
require_once dirname(__DIR__) . '/src/UserRepository.php';
require_once dirname(__DIR__) . '/src/RegistrationService.php';
require_once dirname(__DIR__) . '/src/AuthService.php';

$tests = [];

function test(string $name, callable $callback): void { global $tests; $tests[] = [$name, $callback]; }
function expect_true(bool $condition, string $message = 'Expected true.'): void { if (!$condition) throw new RuntimeException($message); }
function expect_same(mixed $expected, mixed $actual): void {
    if ($expected !== $actual) throw new RuntimeException(sprintf('Expected %s, got %s.', var_export($expected, true), var_export($actual, true)));
}

function repo_for(string $dsn, ?string $user = null, ?string $password = null): UserRepository {
    $pdo = Database::connect($dsn, $user, $password);
    $repo = new UserRepository($pdo);
    $repo->migrate();
    return $repo;
}

function exercise_account_flow(UserRepository $repo): void {
    $registration = new RegistrationService($repo);
    $data = [
        'username' => 'user_' . bin2hex(random_bytes(3)),
        'email' => 'user+' . bin2hex(random_bytes(3)) . '@example.com',
        'phone' => '+1 555 123 4567',
        'password' => 'correct-horse-battery-staple',
        'password_confirm' => 'correct-horse-battery-staple',
    ];

    $created = $registration->register($data);
    expect_same(true, $created['ok']);
    expect_true(is_array($created['user']));

    $stored = $repo->findByIdentity($data['email']);
    expect_true(is_array($stored));
    expect_true($stored['password_hash'] !== $data['password']);
    expect_true(password_verify($data['password'], $stored['password_hash']));

    $duplicate = $registration->register($data);
    expect_same(false, $duplicate['ok']);

    $auth = new AuthService($repo);
    expect_same(false, $auth->attempt($data['username'], 'wrong-password', 1000)['ok']);
    expect_same(true, $auth->attempt($data['username'], $data['password'], 1001)['ok']);
    expect_same(true, $auth->attempt($data['email'], $data['password'], 1002)['ok']);
}

test('validates registration rules', function (): void {
    $valid = RegistrationValidator::validate(RegistrationValidator::normalize([
        'username' => 'demo.user',
        'email' => 'demo@example.com',
        'phone' => '',
        'password' => 'correct-horse-battery-staple',
        'password_confirm' => 'correct-horse-battery-staple',
    ]));
    expect_same([], $valid);

    $invalid = RegistrationValidator::validate(RegistrationValidator::normalize([
        'username' => 'x',
        'email' => 'bad',
        'phone' => 'call-me',
        'password' => 'short',
        'password_confirm' => 'different',
    ]));
    foreach (['username','email','phone','password','password_confirm'] as $field) expect_true(isset($invalid[$field]));
});

test('validates login input', function (): void {
    expect_same([], LoginValidator::validate(LoginValidator::normalize([
        'identity' => 'user@example.com',
        'password' => 'secret',
    ])));
});

test('runs registration and login flow on SQLite', function (): void {
    expect_true(in_array('sqlite', PDO::getAvailableDrivers(), true));
    exercise_account_flow(repo_for('sqlite::memory:'));
});

test('locks repeated login failures', function (): void {
    $repo = repo_for('sqlite::memory:');
    $registration = new RegistrationService($repo);
    $registration->register([
        'username' => 'lockeduser',
        'email' => 'locked@example.com',
        'phone' => '',
        'password' => 'correct-horse-battery-staple',
        'password_confirm' => 'correct-horse-battery-staple',
    ]);

    $auth = new AuthService($repo);
    for ($i=1; $i<=5; $i++) $result = $auth->attempt('lockeduser', 'wrong', 2000 + $i);
    expect_same('locked', $result['reason']);
    expect_same(false, $auth->attempt('lockeduser', 'correct-horse-battery-staple', 2100)['ok']);
    expect_same(true, $auth->attempt('lockeduser', 'correct-horse-battery-staple', 2400)['ok']);
});

$mysqlDsn = getenv('TEST_MYSQL_DSN');
if (is_string($mysqlDsn) && $mysqlDsn !== '') {
    test('runs registration and login flow on MySQL', function () use ($mysqlDsn): void {
        expect_true(in_array('mysql', PDO::getAvailableDrivers(), true));
        exercise_account_flow(repo_for(
            $mysqlDsn,
            getenv('TEST_MYSQL_USER') ?: null,
            getenv('TEST_MYSQL_PASSWORD') ?: null,
        ));
    });
}

$failures = 0;
foreach ($tests as [$name,$callback]) {
    try { $callback(); fwrite(STDOUT, "[pass] {$name}\n"); }
    catch (Throwable $error) { $failures++; fwrite(STDERR, "[fail] {$name}: {$error->getMessage()}\n"); }
}
fwrite(STDOUT, sprintf("\n%d test(s), %d failure(s).\n", count($tests), $failures));
exit($failures === 0 ? 0 : 1);
