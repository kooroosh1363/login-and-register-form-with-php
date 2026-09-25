# AccountFoundry — Secure PHP Registration + Login

[![Quality](https://github.com/kooroosh1363/login-and-register-form-with-php/actions/workflows/quality.yml/badge.svg)](https://github.com/kooroosh1363/login-and-register-form-with-php/actions/workflows/quality.yml)

AccountFoundry modernizes the original 2023 PHP/MySQL registration and login exercise into a secure self-service account lifecycle demo.

## What changed

The original project used interpolated SQL, plaintext passwords, hard-coded database credentials, GET logout, and a committed SQL dump containing sample credentials and personal contact data.

The current version replaces those patterns with:

- PDO prepared statements
- `password_hash()` / `password_verify()`
- unique username and email constraints
- password confirmation and registration policy
- username-or-email login
- CSRF protection
- hardened rotating sessions
- 30-minute inactivity expiration
- generic credential errors
- temporary login throttling
- POST-only logout
- environment-based database configuration
- SQLite + MySQL support
- CI that runs the full registration/login flow against both database engines

## Focus

This repository is intentionally different from the database-login-only project: its primary engineering focus is **safe self-service account creation** and identity lifecycle boundaries.

## Local SQLite setup

```bash
php bin/migrate.php
php -S localhost:8000
```

Open `http://localhost:8000/register.php`, create an account, then sign in.

## MySQL setup

```bash
export DB_DSN='mysql:host=127.0.0.1;port=3306;dbname=account_demo;charset=utf8mb4'
export DB_USER='account_app'
export DB_PASSWORD='change-me'

php bin/migrate.php
php -S localhost:8000
```

## Registration policy

- username: 3–32 characters
- username characters: letters, numbers, dot, underscore, hyphen
- unique username
- valid unique email
- optional phone
- password: 12–128 characters
- password confirmation must match

## Architecture

```text
register.php
   │
   ├── RegistrationValidator
   └── RegistrationService
              │
              ▼
         UserRepository
              │
              ▼
             PDO
        /            \
    SQLite          MySQL

index.php
   │
   ├── LoginValidator
   └── AuthService
          ├── password verification
          └── throttling
```

## Tests

```bash
php tests/run.php
```

GitHub Actions also starts MySQL 8 and repeats the account lifecycle tests there.

## Historical data warning

The old public Git history contains a SQL dump with plaintext sample credentials plus an email address and phone number. The maintained branch removes that dump, but deleting it from the latest revision does **not** erase older commits.

Do not reuse any historical password or credential from this repository.

## Scope

This is a security-minded account lifecycle demo, not a full identity provider. Email verification, password reset, MFA, OAuth/OIDC, audit retention, and distributed rate limiting remain out of scope.

## Deployment

GitHub Pages cannot run PHP. Use a PHP-capable host and keep database credentials in environment configuration, never in Git.

## License

No license is currently included.
