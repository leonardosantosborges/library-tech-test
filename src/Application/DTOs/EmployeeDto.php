<?php

namespace Src\Application\DTOs;

use Domain\ValueObjects\PhoneNumber;
use Domain\ValueObjects\Cpf;
use Domain\ValueObjects\Email;

class EmployeeDto
{
    private string $name;
    private Cpf $cpf;
    private Email $email;
    private string $password;

    /**
     * @param string $name
     * @param Cpf $cpf
     * @param Email $email
     * @param string $password
     */
    public function __construct(string $name, Cpf $cpf, Email $email, string $password)
    {
        $this->name = $name;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->password = $password;
    }
}
