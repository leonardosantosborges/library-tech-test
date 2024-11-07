<?php

namespace Customer;

use Cpf;
use Email;

class Customer extends \Person
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