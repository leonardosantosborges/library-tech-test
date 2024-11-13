<?php

namespace Src\Application\DTOs;

use Domain\ValueObjects\PhoneNumber;
use Domain\ValueObjects\Cpf;
use Domain\ValueObjects\Email;

class CustomerDto
{
    private string $name;
    private Cpf $cpf;
    private Email $email;
    private PhoneNumber $phoneNumber;

    /**
     * @param string $name
     * @param Cpf $cpf
     * @param Email $email
     * @param PhoneNumber $phoneNumber
     */
    public function __construct(string $name, Cpf $cpf, Email $email, PhoneNumber $phoneNumber)
    {
        $this->name = $name;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
    }
}
