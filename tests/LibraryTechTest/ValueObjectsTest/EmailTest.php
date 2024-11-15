<?php

namespace LibraryTechTest\ValueObjectsTest;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Src\Domain\ValueObjects\Email;

class EmailTest extends TestCase
{
    public function testEmailShouldBeValid()
    {
        $email = new Email('user@gmail.com');
        $this->assertInstanceOf(Email::class, $email);
        $this->assertEquals('user@gmail.com', $email->getEmail());
    }

    public function testEmailWithSubdomainShouldBeValid()
    {
        $email = new Email('manager@live.ecommerce.com');
        $this->assertInstanceOf(Email::class, $email);
        $this->assertEquals('manager@live.ecommerce.com', $email->getEmail());
    }

    public function testEmailWithoutAtSymbolShouldBeInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('userexample.com');
    }

    public function testEmailWithoutDomainShouldBeInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('user@');
    }

    public function testEmailWithInvalidDomainShouldBeInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('user@invalid_domain');
    }

    public function testEmailWithSpacesShouldBeInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('manager rafael@liveecommerce.com');
    }
}