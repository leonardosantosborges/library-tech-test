<?php

namespace Src\Infrastructure\Employee;

use Domain\Services\PasswordHasher;

class PasswordHasherPhp implements PasswordHasher
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
