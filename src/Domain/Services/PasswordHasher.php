<?php

namespace Services;

interface PasswordHasher
{
    public static function hash(string $password): string;
    public function verify(string $password, string $passwordHashed): bool;
}
