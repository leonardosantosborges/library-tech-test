<?php

namespace Domain\Services;

use Domain\Entities\Book;
use Domain\ValueObjects\Isbn;
use Src\Infrastructure\Repositories\BookRepositorySqlite;
use Src\Application\DTOs\BookDto;

class BookService
{
    private BookRepositorySqlite $bookRepository;

    public function __construct(BookRepositorySqlite $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function getBookDetails(string $isbn): ?Book
    {
        return $this->bookRepository->getBookDetails($isbn);
    }

    public function checkAvailability(string $isbn): bool
    {
        return $this->bookRepository->checkAvailability($isbn);
    }

    public function updateStock(string $isbn, int $quantity): bool
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Stock quantity cannot be negative.');
        }

        return $this->bookRepository->updateStock($isbn, $quantity);
    }

    public function getBooksByAuthor(string $author): array
    {
        return $this->bookRepository->getBooksByAuthor($author);
    }

    public function create(
        Isbn $isbn,
        string $title,
        string $author,
        \DateTimeImmutable $publicationDate,
        int $stock
    ): void {
        $bookDto = new BookDto(
            $isbn,
            $title,
            $author,
            $publicationDate,
            $stock
        );

        $this->bookRepository->save($bookDto);
    }

    public function removeBook(string $isbn): bool
    {
        return $this->bookRepository->removeBook($isbn);
    }
}
