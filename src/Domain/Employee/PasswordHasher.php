<?php

namespace Employee;

interface PasswordHasher
{
    public function hash(string $password): string;
    public function verify(string $password, string $passwordHashed): bool;
}
