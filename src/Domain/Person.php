<?php

class Person
{
    private string $name;
    private Cpf $cpf;
    private Email $email;

    /**
     * @param string $name
     * @param Cpf $cpf
     * @param Email $email
     */
    public function __construct(string $name, Cpf $cpf, Email $email)
    {
        $this->name = $name;
        $this->cpf = $cpf;
        $this->email = $email;
    }
}