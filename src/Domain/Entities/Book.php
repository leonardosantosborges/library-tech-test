<?php

namespace Entities;

use ValueObjects\Isbn;

class Book
{
    private Isbn $isbn;
    private string $title;
    private string $author;
    private \DateTimeImmutable $publicationDate;
    private int $stock;

    /**
     * @param string $isbn
     * @param string $title
     * @param string $author
     * @param \DateTimeImmutable $publicationDate
     * @param int $stock
     */
    public function __construct(
        string $isbn,
        string $title,
        string $author,
        \DateTimeImmutable $publicationDate,
        int $stock
    ) {
        $this->isbn = $isbn;
        $this->title = $title;
        $this->author = $author;
        $this->publicationDate = $publicationDate;
        $this->stock = $stock;
    }
}
