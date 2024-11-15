<?php

namespace Src\Domain\ValueObjects;

use InvalidArgumentException;

class Cpf
{
    private $cpfNumber;

    /**
     * @param $cpfNumber
     */
    public function __construct($cpfNumber)
    {
        if ($this->validateCpfNumber($cpfNumber) === false) {
            throw new InvalidArgumentException("This cpf number is not valid");
        }
        
        $this->cpfNumber = $cpfNumber;
    }

    private function validateCpfNumber($cpfNumber): bool
    {
        $cpf = preg_replace('/\D/', '', $cpfNumber);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        if ($cpf[9] == $digit1 && $cpf[10] == $digit2) {
            return true;
        } else {
            return false;
        }
    }

    public function getCpf(): string
    {
        return $this->cpfNumber;
    }
}
