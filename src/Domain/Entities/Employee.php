<?php

namespace Entities;

use Employee\Cpf;
use Employee\Email;

class Employee extends User
{
    private string $password;

    public function __construct(string $name, Cpf $cpf, Email $email, string $password)
    {
        parent::__construct($name, $cpf, $email);
        $this->password = \PasswordHasherPhp::hash($password);
    }
}
