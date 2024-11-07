<?php

namespace App\Domain;

class Email
{
    private string $email;

    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException("This is not a valid email address");
        }

        $this->email = $email;
    }

    public function __toString()
    {
        return $this->email;
    }
}
