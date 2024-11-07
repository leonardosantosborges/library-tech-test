<?php

namespace Employee;

class Employee extends \Person
{
    private string $password;

    public function __construct(string $name, Cpf $cpf, Email $email, string $password)
    {
        parent::__construct($name, $cpf, $email);
        $this->password = \PasswordHasherPhp::hash($password);
    }
}