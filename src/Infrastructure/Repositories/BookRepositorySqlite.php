<?php

namespace Src\Infrastructure\Repositories;

use Domain\Repositories\BookRepository;
use PDO;
use Domain\Entities\Book;
use Domain\ValueObjects\Isbn;
use Src\Application\DTOs\BookDto;

class BookRepositorySqlite implements BookRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('sqlite:' . __DIR__ . '/../../../library.sqlite', '', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function getBookDetails(string $isbn): ?Book
    {
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE isbn = ? LIMIT 1');
        $stmt->execute([$isbn]);
        $bookData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $bookData ? $this->mapBook($bookData) : null;
    }

    public function checkAvailability(string $isbn): bool
    {
        $stmt = $this->pdo->prepare('SELECT stock FROM books WHERE isbn = ? LIMIT 1');
        $stmt->execute([$isbn]);
        $stock = $stmt->fetchColumn();

        return $stock > 0;
    }

    public function updateStock(string $isbn, int $quantity): bool
    {
        $stmt = $this->pdo->prepare('UPDATE books SET stock = stock + ? WHERE isbn = ?');
        return $stmt->execute([$quantity, $isbn]);
    }

    public function getBooksByAuthor(string $author): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE author = ?');
        $stmt->execute([$author]);
        $booksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $books = [];
        foreach ($booksData as $bookData) {
            $books[] = $this->mapBook($bookData);
        }

        return $books;
    }

    public function save(BookDto $bookDto): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO books (isbn, title, author, stock) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $bookDto->getIsbn()->getIsbn(),
            $bookDto->getTitle(),
            $bookDto->getAuthor(),
            $bookDto->getStock()
        ]);
    }

    public function removeBook(string $isbn): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM books WHERE isbn = ?');
        return $stmt->execute([$isbn]);
    }

    private function mapBook(array $bookData): Book
    {
        return new Book(
            new Isbn($bookData['isbn']),
            $bookData['title'],
            $bookData['author'],
            new \DateTimeImmutable($bookData['publication_date']),
            $bookData['stock']
        );
    }
}
