<?php

namespace Domain\ValueObjects;

use Biblys\Isbn\Isbn as IsbnObject;
use Biblys\Isbn\IsbnValidationException;

class Isbn
{
    private string $isbn;

    public function __construct(string $isbn)
    {
        $this->isbn = $this->validateIsbn($isbn);
    }

    private function validateIsbn(string $isbn): string
    {
        $cleanIsbn = str_replace(['-', ' '], '', $isbn);

        try {
            if (strlen($isbn) === 17 || strlen($cleanIsbn) === 13) {
                if (ctype_digit($isbn)) {
                    $isbn = IsbnObject::convertToIsbn13($isbn);
                }
                IsbnObject::validateAsIsbn13($isbn);
            } elseif (strlen($isbn) === 13 || strlen($cleanIsbn) === 10) {
                if (ctype_digit($isbn)) {
                    $isbn = IsbnObject::convertToIsbn10($isbn);
                }
                IsbnObject::validateAsIsbn10($isbn);
            } else {
                throw new IsbnValidationException("Invalid ISBN format");
            }
        } catch (IsbnValidationException $e) {
            throw new IsbnValidationException("Invalid ISBN: " . $e->getMessage());
        }

        return $isbn;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }
}
