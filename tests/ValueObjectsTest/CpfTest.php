<?php

namespace LibraryTechTest\ValueObjectsTest;

use Domain\ValueObjects\Cpf;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CpfTest extends TestCase
{
    public function testCpfShouldBeValid()
    {
        $cpf = new Cpf('545.731.440-56');
        $this->assertInstanceOf(Cpf::class, $cpf);
        $this->assertEquals('545.731.440-56', $cpf->getCpf());
    }

    public function testCpfWithOnlyNumbersShouldBeValid()
    {
        $cpf = new Cpf('54573144056');
        $this->assertInstanceOf(Cpf::class, $cpf);
        $this->assertEquals('54573144056', $cpf->getCpf());
    }


    public function testCpfShouldNotBeValid()
    {
        $this->expectException(InvalidArgumentException::class);
        new Cpf('111.111.111-00');
    }

    public function testCpfWithLessThan11DigitsShouldNotBeValid()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Cpf('123.456.789-0');
    }

    public function testCpfWithMoreThan11DigitsShouldNotBeValid()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Cpf('123.456.789-012');
    }
}