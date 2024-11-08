<?php

namespace App\Infrastructure\Employee;

class PasswordHasherPhp implements \Services\PasswordHasher
{
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    public function verify(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }
}
