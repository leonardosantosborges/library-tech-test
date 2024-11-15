<?php

namespace Src\Infrastructure\Repositories;

use DateTimeImmutable;
use PDO;
use RuntimeException;
use Src\Application\DTOs\LoanDto;
use Src\Domain\Entities\Loan;
use Src\Domain\Repositories\LoanRepository;
use Src\Domain\ValueObjects\Cpf;
use Src\ValueObjects\Isbn;

class LoanRepositorySqlite implements LoanRepository
{
    private PDO $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO(
                'sqlite:' . __DIR__ . '/../../../library.sqlite', '', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        } catch (RuntimeException $e) {
            throw new RuntimeException('Failed to connect to the database: ' . $e->getMessage());
        }
    }

    public function save(LoanDto $loanDto)
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO loans (isbn, borrower_cpf, loan_date, due_date, status)
                VALUES (?, ?, ?, ?, ?)'
            );

            $stmt->execute(
                [
                $loanDto->getIsbn()->getIsbn(),
                $loanDto->getBorrowerCpf()->getCpf(),
                ($loanDto->getLoanDate())->format('Y-m-d'),
                ($loanDto->getDueDate())->format('Y-m-d'),
                $loanDto->getStatus()
                ]
            );
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new \PDOException('Loan already exists: ' . $e->getMessage(), (int) $e->getCode());
            }

            throw $e;
        }
    }


    public function findAllByBorrowerCpf(string $borrowerCpf)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM loans WHERE borrower_cpf = ?');
        $stmt->execute([$borrowerCpf]);

        $loansData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $loans = [];
        foreach ($loansData as $loanData) {
            $loans[] = $this->mapLoan($loanData);
        }

        return $loans;
    }

    public function getLoanDetails(string $isbn, string $borrowerCpf): ?Loan
    {
        $stmt = $this->pdo->prepare('SELECT * FROM loans WHERE isbn = ? AND borrower_cpf = ? LIMIT 1');
        $stmt->execute([$isbn, $borrowerCpf]);

        $loanData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $loanData ? $this->mapLoan($loanData) : null;
    }

    public function findAll()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM loans');
        $stmt->execute();

        $loansData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $loans = [];
        foreach ($loansData as $loanData) {
            $loans[] = $this->mapLoan($loanData);
        }

        return $loans;
    }

    public function updateLoanStatus(string $isbn, string $borrowerCpf, string $status): ?Loan
    {
        $stmt = $this->pdo->prepare('UPDATE loans SET status = ? WHERE isbn = ? AND borrower_cpf = ?');
        $stmt->execute([$status, $isbn, $borrowerCpf]);

        $stmt = $this->pdo->prepare('SELECT * FROM loans WHERE isbn = ? AND borrower_cpf = ? LIMIT 1');
        $stmt->execute([$isbn, $borrowerCpf]);
        $loanData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $loanData ? $this->mapLoan($loanData) : null;
    }

    private function mapLoan(array $loanData): ?Loan
    {
        return new Loan(
            new Isbn($loanData['isbn']),
            new Cpf($loanData['borrower_cpf']),
            new DateTimeImmutable($loanData['loan_date']),
            new DateTimeImmutable($loanData['due_date']),
            $loanData['status']
        );
    }
}
