<?php

namespace Src\Domain\Services;

interface PasswordHasher
{
    public static function hash(string $password): string;
    public function verify(string $password, string $passwordHashed): bool;
}
