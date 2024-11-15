<?php

namespace Src\Application\DTOs;


use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Email;
use Src\Domain\ValueObjects\PhoneNumber;

class CustomerDto
{
    private string $name;
    private Cpf $cpf;
    private Email $email;
    private PhoneNumber $phoneNumber;

    /**
     * @param string      $name
     * @param Cpf         $cpf
     * @param Email       $email
     * @param PhoneNumber $phoneNumber
     */
    public function __construct(string $name, Cpf $cpf, Email $email, PhoneNumber $phoneNumber)
    {
        $this->name = $name;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCpf(): Cpf
    {
        return $this->cpf;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }
}
