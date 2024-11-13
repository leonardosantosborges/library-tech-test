<?php

namespace Domain\Entities;

use DateTimeImmutable;
use Domain\ValueObjects\Isbn;

class Book
{
    private Isbn $isbn;
    private string $title;
    private string $author;
    private DateTimeImmutable $publicationDate;
    private int $stock;

    /**
     * @param Isbn $isbn
     * @param string $title
     * @param string $author
     * @param \DateTimeImmutable $publicationDate
     * @param int $stock
     */
    public function __construct(
        Isbn $isbn,
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

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }

    public function getIsbn(): Isbn
    {
        return $this->isbn;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getPublicationDate(): DateTimeImmutable
    {
        return $this->publicationDate;
    }

    public function getStock(): int
    {
        return $this->stock;
    }
}
