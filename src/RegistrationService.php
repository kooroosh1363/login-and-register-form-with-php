<?php

declare(strict_types=1);

final class RegistrationService
{
    public function __construct(private UserRepository $users) {}

    /** @param array{username:string,email:string,phone:string,password:string,password_confirm:string} $data
     *  @return array{ok:bool,error:?string,user:?array{id:int,username:string,email:string,phone:?string,created_at:string}}
     */
    public function register(array $data): array
    {
        if ($this->users->identityExists($data['username'], $data['email'])) {
            return [
                'ok' => false,
                'error' => 'That username or email is already in use.',
                'user' => null,
            ];
        }

        try {
            $id = $this->users->createUser(
                $data['username'],
                $data['email'],
                $data['phone'],
                password_hash($data['password'], PASSWORD_DEFAULT),
            );
        } catch (PDOException $error) {
            if ((string) $error->getCode() === '23000'
                || str_contains($error->getMessage(), 'UNIQUE constraint failed')
                || str_contains($error->getMessage(), 'Duplicate entry')) {
                return [
                    'ok' => false,
                    'error' => 'That username or email is already in use.',
                    'user' => null,
                ];
            }

            throw $error;
        }

        $user = $this->users->findById($id);
        if ($user === null) {
            throw new RuntimeException('Created account could not be reloaded.');
        }

        return [
            'ok' => true,
            'error' => null,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'created_at' => $user['created_at'],
            ],
        ];
    }
}
