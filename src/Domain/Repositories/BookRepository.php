<?php

namespace Domain\Repositories;

use Src\Application\DTOs\BookDto;
use Domain\Entities\Book;

interface BookRepository
{
    public function getBookDetails(string $isbn): ?Book;

    public function checkAvailability(string $isbn): bool;

    public function updateStock(string $isbn, int $quantity): bool;

    public function getBooksByAuthor(string $author): array;

    public function save(BookDto $bookDto): void;

    public function removeBook(string $isbn): bool;
}
