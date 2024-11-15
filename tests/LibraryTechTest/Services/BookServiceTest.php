<?php

namespace LibraryTechTest\Services;


use PHPUnit\Framework\TestCase;
use Src\Application\DTOs\BookDto;
use Src\Domain\Entities\Book;
use Src\Domain\Services\BookService;
use Src\Domain\ValueObjects\Isbn;
use Src\Infrastructure\Repositories\BookRepositorySqlite;

class BookServiceTest extends TestCase
{
    private BookRepositorySqlite $bookRepository;
    private BookService $bookService;

    protected function setUp(): void
    {
        $this->bookRepository = $this->createMock(BookRepositorySqlite::class);
        $this->bookService = new BookService($this->bookRepository);
    }

    public function testGetBookDetails()
    {
        $isbn = '978-3-10-476591-4';
        $title = 'How to Win Friends and Influence People';
        $author = 'Dale Carnegie';
        $publicationDate = new \DateTimeImmutable('1936-11-01');
        $stock = 5;

        $book = new Book(new Isbn($isbn), $title, $author, $publicationDate, $stock);

        $this->bookRepository->expects($this->once())
            ->method('getBookDetails')
            ->with($isbn)
            ->willReturn($book);

        $result = $this->bookService->getBookDetails($isbn);

        $this->assertNotNull($result);
        $this->assertEquals($isbn, $result->getIsbn()->getIsbn());
        $this->assertEquals($title, $result->getTitle());
        $this->assertEquals($author, $result->getAuthor());
        $this->assertEquals($publicationDate, $result->getPublicationDate());
        $this->assertEquals($stock, $result->getStock());
    }


    public function testCheckAvailabilityShouldReturnsTrue()
    {
        $isbn = '978-3-10-476591-4';

        $this->bookRepository->expects($this->once())
            ->method('checkAvailability')
            ->with($isbn)
            ->willReturn(true);

        $result = $this->bookService->checkAvailability($isbn);

        $this->assertTrue($result);
    }

    public function testCheckAvailabilityShouldReturnsFalse()
    {
        $isbn = '978-3-10-476591-4';

        $this->bookRepository->expects($this->once())
            ->method('checkAvailability')
            ->with($isbn)
            ->willReturn(false);

        $result = $this->bookService->checkAvailability($isbn);

        $this->assertFalse($result);
    }

    public function testUpdateStock()
    {
        $isbn = new Isbn('978-3-16-148410-0');
        $initialStock = 5;
        $additionalStock = 10;
        $expectedStock = $initialStock + $additionalStock;
        $title = 'Clean Code: A Handbook of Agile Software Craftsmanship';
        $author = 'Robert C. Martin';

        $book = new Book(
            $isbn,
            $title,
            $author,
            new \DateTimeImmutable('2008-08-01'),
            $initialStock
        );

        $bookDto = new BookDto(
            $isbn,
            $title,
            $author,
            new \DateTimeImmutable('2008-08-01'),
            $initialStock
        );

        $this->bookRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($bookDto));

        $this->bookService->create($isbn, $title, $author, new \DateTimeImmutable('2008-08-01'), $initialStock);

        $this->bookRepository->expects($this->once())
            ->method('updateStock')
            ->with($isbn->getIsbn(), $additionalStock)
            ->willReturn(true);

        $result = $this->bookService->updateStock($isbn->getIsbn(), $additionalStock);

        $this->assertTrue($result);

        $book->setStock($expectedStock);

        $this->bookRepository->expects($this->once())
            ->method('getBookDetails')
            ->with($isbn->getIsbn())
            ->willReturn($book);

        $updatedBook = $this->bookService->getBookDetails($isbn->getIsbn());
        $this->assertEquals($expectedStock, $updatedBook->getStock());
    }

    public function testGetBooksByAuthor()
    {
        $author = 'Dale Carnegie';
        $isbn1 = '978-3-10-476591-4';
        $isbn2 = '978-4-552-38808-3';
        $title1 = 'How to Win Friends and Influence People';
        $title2 = 'The Art of Public Speaking';
        $publicationDate1 = new \DateTimeImmutable('1936-11-01');
        $publicationDate2 = new \DateTimeImmutable('1915-03-05');
        $stock1 = 5;
        $stock2 = 8;

        $book1 = new Book(new Isbn($isbn1), $title1, $author, $publicationDate1, $stock1);
        $book2 = new Book(new Isbn($isbn2), $title2, $author, $publicationDate2, $stock2);
        $books = [$book1, $book2];

        $this->bookRepository->expects($this->once())
            ->method('getBooksByAuthor')
            ->with($author)
            ->willReturn($books);

        $result = $this->bookService->getBooksByAuthor($author);

        $this->assertNotEmpty($result);
        $this->assertCount(2, $result);

        $this->assertEquals($isbn1, $result[0]->getIsbn()->getIsbn());
        $this->assertEquals($title1, $result[0]->getTitle());

        $this->assertEquals($isbn2, $result[1]->getIsbn()->getIsbn());
        $this->assertEquals($title2, $result[1]->getTitle());
    }


    public function testShouldCreateBook()
    {
        $isbn = new Isbn('978-4-552-38808-3');
        $title = 'How to Win Friends and Influence People';
        $author = 'Dale Carnegie';
        $publicationDate = new \DateTimeImmutable('1936-11-01');
        $stock = 10;

        $bookDto = new BookDto($isbn, $title, $author, $publicationDate, $stock);

        $book = new Book(
            $bookDto->getIsbn(),
            $bookDto->getTitle(),
            $bookDto->getAuthor(),
            $bookDto->getPublicationDate(),
            $bookDto->getStock()
        );

        $this->bookRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($bookDto));

        $this->bookService->create($isbn, $title, $author, $publicationDate, $stock);

        $this->bookRepository->expects($this->once())
            ->method('getBookDetails')
            ->with($isbn->getIsbn())
            ->willReturn($book);

        $retrievedBook = $this->bookRepository->getBookDetails($isbn->getIsbn());

        $this->assertNotNull($retrievedBook);
        $this->assertEquals($isbn->getIsbn(), $retrievedBook->getIsbn()->getIsbn());
        $this->assertEquals($title, $retrievedBook->getTitle());
        $this->assertEquals($author, $retrievedBook->getAuthor());
        $this->assertEquals($publicationDate, $retrievedBook->getPublicationDate());
        $this->assertEquals($stock, $retrievedBook->getStock());
    }

    public function testRemoveBook()
    {
        $isbn = '978-0-7184-8098-1';
        $title = 'PHP Objects, Patterns, and Practice';
        $author = 'Matti Zandstra';
        $publicationDate = new \DateTimeImmutable('2017-01-01');
        $initialStock = 10;

        $bookDto = new BookDto(
            new Isbn($isbn),
            $title,
            $author,
            $publicationDate,
            $initialStock
        );

        $this->bookRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($bookDto));

        $this->bookService->create(new Isbn($isbn), $title, $author, $publicationDate, $initialStock);

        $this->bookRepository->expects($this->once())
            ->method('removeBook')
            ->with($isbn)
            ->willReturn(true);

        $result = $this->bookService->removeBook($isbn);

        $this->assertTrue($result);

        $this->bookRepository->expects($this->once())
            ->method('getBookDetails')
            ->with($isbn)
            ->willReturn(null);

        $retrievedBook = $this->bookService->getBookDetails($isbn);

        $this->assertNull($retrievedBook);
    }

    public function testRemoveNonExistentBook()
    {
        $isbn = '999-9-99-999999-9';

        $this->bookRepository->expects($this->once())
            ->method('removeBook')
            ->with($isbn)
            ->willReturn(false);

        $result = $this->bookService->removeBook($isbn);

        $this->assertFalse($result);
    }

    public function testRemoveBookThrowsException()
    {
        $isbn = '978-0-7184-8098-1';

        $this->bookRepository->expects($this->once())
            ->method('removeBook')
            ->with($isbn)
            ->willThrowException(new \RuntimeException('Failed to remove book'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to remove book');

        $this->bookService->removeBook($isbn);
    }

    public function testGetDetailsOfNonExistentBook()
    {
        $isbn = '000-0-00-000000-0';

        $this->bookRepository->expects($this->once())
            ->method('getBookDetails')
            ->with($isbn)
            ->willReturn(null);

        $result = $this->bookService->getBookDetails($isbn);

        $this->assertNull($result);
    }

    public function testUpdateStockThrowsExceptionForInvalidQuantity()
    {
        $isbn = '978-3-16-148410-0';
        $invalidQuantity = -5;

        $this->bookRepository
            ->method('updateStock')
            ->will($this->throwException(new \InvalidArgumentException('Quantity must be greater than zero.')));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero.');

        $this->bookService->updateStock($isbn, $invalidQuantity);
    }

}
