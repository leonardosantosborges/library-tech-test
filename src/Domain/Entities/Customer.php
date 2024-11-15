<?php

namespace Src\Domain\Entities;


use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Email;
use Src\Domain\ValueObjects\PhoneNumber;

class Customer extends User
{
    private PhoneNumber $phone;

    /**
     * @param Cpf $cpf
     * @param string $name
     * @param Email $email
     * @param PhoneNumber $phone
     */
    public function __construct(Cpf $cpf, string $name, Email $email, PhoneNumber $phone)
    {
        parent::__construct($cpf, $name, $email);
        $this->phone = $phone;
    }
}
