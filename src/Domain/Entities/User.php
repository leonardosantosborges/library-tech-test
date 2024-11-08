<?php

namespace Entities;

use ValueObjects\Cpf;
use ValueObjects\Email;

class User
{
    private string $name;
    private Cpf $cpf;
    private Email $email;
    private string $role;

    /**
     * @param string $name
     * @param Cpf $cpf
     * @param Email $email
     */
    public function __construct(string $name, Cpf $cpf, Email $email, string $role)
    {
        $this->name = $name;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->role = $role;
    }
}
