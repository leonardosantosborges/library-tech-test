<?php

namespace Domain\Entities;

use Cpf;
use Email;
use ValueObjects\PhoneNumber;

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
