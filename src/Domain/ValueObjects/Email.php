<?php

namespace Src\Domain\ValueObjects;

use \InvalidArgumentException;

class Email
{
    private string $email;

    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("This is not a valid email address");
        }

        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
