<?php

namespace Src\Domain\Entities;

use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Email;

class User
{
    private string $name;
    private Cpf $cpf;
    private Email $email;

    /**
     * @param string $name
     * @param Cpf    $cpf
     * @param Email  $email
     */
    public function __construct(string $name, Cpf $cpf, Email $email)
    {
        $this->name = $name;
        $this->cpf = $cpf;
        $this->email = $email;
    }
}
