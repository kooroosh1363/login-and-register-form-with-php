<?php

declare(strict_types=1);

final class LoginValidator
{
    public const IDENTITY_MAX = 254;
    public const PASSWORD_MAX = 4096;

    /** @param array<string,mixed> $input
     *  @return array{identity:string,password:string}
     */
    public static function normalize(array $input): array
    {
        return [
            'identity' => trim((string) ($input['identity'] ?? '')),
            'password' => (string) ($input['password'] ?? ''),
        ];
    }

    /** @param array{identity:string,password:string} $data
     *  @return array<string,string>
     */
    public static function validate(array $data): array
    {
        $errors = [];

        if ($data['identity'] === '' || self::length($data['identity']) > self::IDENTITY_MAX) {
            $errors['identity'] = 'Enter your email address or username.';
        }

        if ($data['password'] === '') {
            $errors['password'] = 'Please enter your password.';
        } elseif (self::length($data['password']) > self::PASSWORD_MAX) {
            $errors['password'] = 'Password is too long.';
        }

        return $errors;
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }
}
