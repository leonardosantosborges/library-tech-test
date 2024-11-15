<?php

namespace Src\Application\DTOs;

use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Email;

class ManagerDto
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

    public function getPassword(): string
    {
        return $this->password;
    }
}
