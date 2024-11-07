<?php

namespace Customer;

class PhoneNumber
{
    private string $areaCode;
    private string $number;

    public function __construct(string $areaCode, string $number)
    {
        $this->setAreaCode($areaCode);
        $this->setNumber($number);
    }

    public function setAreaCode(string $areaCode): void
    {
        if (preg_match('/\d{2}/', $areaCode) !== 1) {
            throw new \InvalidArgumentException('This area code is not a valid number.');
        }

        $this->areaCode = $areaCode;
    }

    public function setNumber(string $number): void
    {
        if (preg_match('/\d{8,9}/', $number) !== 1) {
            throw new \InvalidArgumentException('This phone number is not a valid number.');
        }

        $this->number = $number;
    }
}
