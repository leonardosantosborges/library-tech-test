<?php

namespace LibraryTechTest\ValueObjectsTest;

use Biblys\Isbn\IsbnValidationException;
use Domain\ValueObjects\Isbn;
use PHPUnit\Framework\TestCase;

class IsbnTest extends TestCase
{
    public function testIsbn10ShouldBeInvalid()
    {
        $this->expectException(IsbnValidationException::class);
        new Isbn('0-3785-7041-5');
    }

    public function testIsbn13WithoutHyphenShouldBeValid()
    {
        $isbn = new Isbn('9783161484100');
        $this->assertInstanceOf(Isbn::class, $isbn);
    }

    public function testIsbn10WithoutHyphenShouldBeValid()
    {
        $isbn = new Isbn('0708109683');
        $this->assertInstanceOf(Isbn::class, $isbn);
    }


    public function testIsbn10WithInvalidLengthShouldNotBeValid()
    {
        $this->expectException(IsbnValidationException::class);
        new Isbn('0-7081-0968');
    }

    public function testIsbn13WithInvalidLengthShouldNotBeValid()
    {
        $this->expectException(IsbnValidationException::class);
        new Isbn('978-3-16-14841');
    }

    public function testIsbn13ShouldBeInvalid()
    {
        $this->expectException(IsbnValidationException::class);
        new Isbn('978-8-4362-4754-7');
    }

    public function testIsbn10ShouldBeValid()
    {
        $isbn = new Isbn('0-7081-0968-3');
        $this->assertInstanceOf(Isbn::class, $isbn);
        $this->assertEquals('0-7081-0968-3', $isbn->getIsbn());
    }

    public function testIsbnWithInvalidCharactersShouldNotBeValid()
    {
        $this->expectException(IsbnValidationException::class);
        new Isbn('978-3a-16-148410-0');
    }

    public function testIsbn13ShouldBeValid()
    {
        $isbn = new Isbn('978-3-16-148410-0');
        $this->assertInstanceOf(Isbn::class, $isbn);
        $this->assertEquals('978-3-16-148410-0', $isbn->getIsbn());
    }
}