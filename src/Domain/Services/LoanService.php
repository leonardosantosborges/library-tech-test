<?php

namespace Domain\Services;

use Domain\Entities\Loan;
use Domain\Enums\LoanStatus;
use Domain\ValueObjects\Cpf;
use Domain\ValueObjects\Isbn;
use Src\Application\DTOs\LoanDto;
use Src\Infrastructure\Repositories\LoanRepositorySqlite;

class LoanService
{
    private LoanRepositorySqlite $loanRepository;

    /**
     * @param LoanRepositorySqlite $loanRepository
     */
    public function __construct(LoanRepositorySqlite $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }

    public function create(Isbn $isbn, Cpf $cpf): void
    {
        try {
            $loanDate = new \DateTimeImmutable("now");
            $dueDate = new \DateTimeImmutable("now");
            $status = LoanStatus::BORROWED;

            $loanDto = new LoanDto(
                $isbn,
                $cpf,
                $loanDate,
                $dueDate,
                $status
            );

            $this->loanRepository->save($loanDto);
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    public function findAllByBorrowerCpf(string $borrowerCpf)
    {
        return $this->loanRepository->findAllByBorrowerCpf($borrowerCpf);
    }

    public function getLoanDetails(string $isbn, string $borrowerCpf)
    {
        return $this->loanRepository->getLoanDetails($isbn, $borrowerCpf);
    }

    public function findAll()
    {
        return $this->loanRepository->findAll();
    }

    public function updateLoanStatus(string $isbn, string $borrowerCpf, string $status)
    {
        return $this->loanRepository->updateLoanStatus($isbn, $borrowerCpf, $status);
    }
}
