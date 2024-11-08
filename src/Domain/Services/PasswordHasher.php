<?php

namespace Services;

interface PasswordHasher
{
    static public function hash(string $password): string;
    public function verify(string $password, string $passwordHashed): bool;
}
