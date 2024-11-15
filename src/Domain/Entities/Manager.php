<?php

namespace Src\Domain\Entities;

use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Email;
use Src\Infrastructure\Employee\PasswordHasherPhp;

class Manager extends User
{
    private string $password;

    public function __construct(string $name, Cpf $cpf, Email $email, string $password)
    {
        parent::__construct($name, $cpf, $email);
        $this->password = PasswordHasherPhp::hash($password);
    }
}
