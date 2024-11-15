<?php

namespace LibraryTechTest\ValueObjectsTest;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Src\Domain\ValueObjects\PhoneNumber;

class PhoneNumberTest extends TestCase
{
    public function testPhoneNumberWithEightDigitsShouldBeValid()
    {
        $phoneNumber = new PhoneNumber('11', '23456789');
        $this->assertInstanceOf(PhoneNumber::class, $phoneNumber);
    }

    public function testPhoneNumberWithNineDigitsShouldBeValid()
    {
        $phoneNumber = new PhoneNumber('21', '987654321');
        $this->assertInstanceOf(PhoneNumber::class, $phoneNumber);
    }

    public function testAreaCodeMoreThanTwoDigitsShouldBeInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        new PhoneNumber('123', '987654321');
    }

    public function testAreaCodeLessThanTwoDigitsShouldBeInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        new PhoneNumber('1', '987654321');
    }

    public function testPhoneNumberLessThanEightDigitsShouldBeInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        new PhoneNumber('11', '1234567');
    }

    public function testPhoneNumberMoreThanNineDigitsShouldBeInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        new PhoneNumber('11', '1234567890');
    }

    public function testPhoneNumberWithNonNumericCharactersShouldBeInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        new PhoneNumber('11', '98765abc');
    }
}
