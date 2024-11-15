<?php

namespace LibraryTechTest\Services;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Src\Application\DTOs\LoanDto;
use Src\Domain\Entities\Loan;
use Src\Domain\Enums\LoanStatus;
use Src\Domain\Services\LoanService;
use Src\Domain\ValueObjects\Cpf;
use Src\Infrastructure\Repositories\LoanRepositorySqlite;
use Src\Domain\ValueObjects\Isbn;

class LoanServiceTest extends TestCase
{
    private LoanService $loanService;
    private LoanRepositorySqlite $loanRepository;

    protected function setUp(): void
    {
        $this->loanRepository = $this->createMock(LoanRepositorySqlite::class);
        $this->loanService = new LoanService($this->loanRepository);
    }

    public function testShouldCreateLoan()
    {
        $isbn = new Isbn("0-491-54348-4");
        $cpf = new Cpf("847.532.760-58");
        $loanDate = new DateTimeImmutable();
        $dueDate = new DateTimeImmutable('+7 days');
        $loanStatus = LoanStatus::BORROWED;

        $this->loanService->create($isbn, $cpf);

        $loanDto = new LoanDto($isbn, $cpf, $loanDate, $dueDate, $loanStatus);

        $loan = new Loan(
            $loanDto->getIsbn(),
            $loanDto->getBorrowerCpf(),
            $loanDto->getLoanDate(),
            $loanDto->getDueDate(),
            $loanDto->getStatus()
        );

        $this->loanRepository->expects($this->once())
            ->method('getLoanDetails')
            ->with($isbn->getIsbn(), $cpf->getCpf())
            ->willReturn($loan);

        $retrievedLoan = $this->loanRepository->getLoanDetails($isbn->getIsbn(), $cpf->getCpf());

        $this->assertNotNull($retrievedLoan);
        $this->assertEquals($isbn->getIsbn(), $retrievedLoan->getIsbn()->getIsbn());
        $this->assertEquals($cpf->getCpf(), $retrievedLoan->getBorrowerCpf()->getCpf());
    }


    public function testShouldThrowExceptionIfLoanAlreadyExists()
    {
        $isbn = new Isbn("0-491-54348-4");
        $cpf = new Cpf("847.532.760-58");

        $this->loanRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(LoanDto::class))
            ->willThrowException(new \PDOException('Loan already exists'));

        $loanService = new LoanService($this->loanRepository);

        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage('Loan already exists');

        $loanService->create($isbn, $cpf);
    }

    public function testShouldReturnLoanIfFound()
    {
        $isbn = new Isbn("0-7286-3855-X");
        $cpf = new Cpf("847.532.760-58");
        $loanDate = new DateTimeImmutable();
        $dueDate = new DateTimeImmutable('+7 days');
        $loanStatus = LoanStatus::BORROWED;

        $expectedLoan = new Loan($isbn, $cpf, $loanDate, $dueDate, $loanStatus);

        $this->loanRepository->expects($this->once())
            ->method('getLoanDetails')
            ->with($isbn->getIsbn(), $cpf->getCpf())
            ->willReturn($expectedLoan);

        $loanService = new LoanService($this->loanRepository);

        $retrievedLoan = $loanService->getLoanDetails($isbn->getIsbn(), $cpf->getCpf());

        $this->assertNotNull($retrievedLoan);
        $this->assertEquals($isbn->getIsbn(), $retrievedLoan->getIsbn()->getIsbn());
        $this->assertEquals($cpf->getCpf(), $retrievedLoan->getBorrowerCpf()->getCpf());
        $this->assertEquals($loanDate, $retrievedLoan->getLoanDate());
        $this->assertEquals($dueDate, $retrievedLoan->getDueDate());
        $this->assertEquals($loanStatus, $retrievedLoan->getStatus());
    }



    public function testShouldReturnNullIfLoanNotFound()
    {
        $isbn = new Isbn("0-7286-3855-X");
        $cpf = new Cpf("847.532.760-58");

        $this->loanRepository
            ->expects($this->once())
            ->method('getLoanDetails')
            ->with($isbn->getIsbn(), $cpf->getCpf())
            ->willReturn(null);

        $retrievedLoan = $this->loanService->getLoanDetails($isbn->getIsbn(), $cpf->getCpf());

        $this->assertNull($retrievedLoan);
    }

    public function testShouldThrowExceptionIfInvalidLoanStatus()
    {
        $isbn = new Isbn("0-491-54348-4");
        $cpf = new Cpf("847.532.760-58");

        $invalidStatus = 'invalid';

        $this->loanRepository
            ->expects($this->once())
            ->method('updateLoanStatus')
            ->will($this->throwException(new \InvalidArgumentException("Invalid loan status: $invalidStatus")));

        $this->expectException(\InvalidArgumentException::class);
        $this->loanService->updateLoanStatus($isbn->getIsbn(), $cpf->getCpf(), $invalidStatus);
    }

    public function testShouldReturnLoansForGivenBorrowerCpf()
    {
        $cpf = new Cpf("847.532.760-58");

        $loan1 = new Loan(
            new Isbn("0-7286-3855-X"),
            $cpf,
            new \DateTimeImmutable("2024-01-01"),
            new \DateTimeImmutable("2024-01-08"),
            LoanStatus::BORROWED
        );

        $loan2 = new Loan(
            new Isbn("0-7286-1234-8"),
            $cpf,
            new \DateTimeImmutable("2024-01-09"),
            new \DateTimeImmutable("2024-01-16"),
            LoanStatus::BORROWED
        );

        $expectedLoans = [$loan1, $loan2];

        $this->loanRepository->expects($this->once())
            ->method('findAllByBorrowerCpf')
            ->with($cpf->getCpf())
            ->willReturn($expectedLoans);

        $retrievedLoans = $this->loanService->findAllByBorrowerCpf($cpf->getCpf());

        $this->assertCount(2, $retrievedLoans);
        $this->assertEquals($loan1, $retrievedLoans[0]);
        $this->assertEquals($loan2, $retrievedLoans[1]);
    }

    public function testShouldReturnAllLoans()
    {
        $loan1 = new Loan(
            new Isbn("0-7286-3855-X"),
            new Cpf("035.324.020-68"),
            new \DateTimeImmutable("2024-01-01"),
            new \DateTimeImmutable("2024-01-08"),
            LoanStatus::BORROWED
        );

        $loan2 = new Loan(
            new Isbn("0-7286-1234-8"),
            new Cpf("035.324.020-68"),
            new \DateTimeImmutable("2024-01-09"),
            new \DateTimeImmutable("2024-01-16"),
            LoanStatus::RETURNED
        );

        $expectedLoans = [$loan1, $loan2];

        $this->loanRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedLoans);

        $retrievedLoans = $this->loanService->findAll();

        $this->assertCount(2, $retrievedLoans);
        $this->assertEquals($loan1, $retrievedLoans[0]);
        $this->assertEquals($loan2, $retrievedLoans[1]);
    }

    public function testShouldUpdateLoanStatus()
    {
        $isbn = "0-491-54348-4";
        $cpf = "847.532.760-58";
        $newStatus = LoanStatus::RETURNED;

        $updatedLoan = new Loan(
            new Isbn($isbn),
            new Cpf($cpf),
            new \DateTimeImmutable("2024-01-01"),
            new \DateTimeImmutable("2024-01-08"),
            LoanStatus::RETURNED
        );

        $this->loanRepository->expects($this->once())
            ->method('updateLoanStatus')
            ->with($isbn, $cpf, $newStatus)
            ->willReturn($updatedLoan);

        $result = $this->loanService->updateLoanStatus($isbn, $cpf, $newStatus);

        $this->assertInstanceOf(Loan::class, $result);
        $this->assertEquals($newStatus, $result->getStatus());
    }
}
