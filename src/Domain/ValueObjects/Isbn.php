<?php

namespace Src\Domain\ValueObjects;

use Biblys\Isbn\Isbn as IsbnObject;
use Biblys\Isbn\IsbnValidationException;

class Isbn
{
    private string $isbn;

    public function __construct(string $isbn)
    {
        $this->isbn = $this->validateIsbn($isbn);
    }

    public static function validateIsbn(string $isbn): string
    {
        $cleanIsbn = null;

        if (!preg_match('/^[0-9X]+$/', $isbn)) {
            $cleanIsbn = str_replace(['-', ' '], '', strtoupper($isbn));
        }

        if (strlen($cleanIsbn) === 13 || (strlen($isbn) === 13 && $cleanIsbn === null)) {
            if ($cleanIsbn === null) {
                $isbn = IsbnObject::convertToIsbn13($isbn);
            }
            IsbnObject::validateAsIsbn13($isbn);
            return $isbn;
        }
        if (strlen($cleanIsbn) === 10 || (strlen($isbn) === 10 && $cleanIsbn === null)) {
            if ($cleanIsbn === null) {
                $isbn = IsbnObject::convertToIsbn10($isbn);
            }
            IsbnObject::validateAsIsbn10($isbn);
            return $isbn;
        }

        throw new IsbnValidationException("Invalid ISBN format. Must be ISBN-10 or ISBN-13.");
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }
}
