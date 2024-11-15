<?php

namespace Src\Domain\Entities;

use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Isbn;

class Loan
{
    private Isbn $isbn;
    private Cpf $borrowerCpf;
    private \DateTimeImmutable $loanDate;
    private \DateTimeImmutable $dueDate;
    private string $status;

    /**
     * @param Isbn $isbn
     * @param Cpf $borrowerCpf
     * @param \DateTimeImmutable $loanDate
     * @param \DateTimeImmutable $dueDate
     * @param string $status
     */
    public function __construct(
        Isbn $isbn,
        Cpf $borrowerCpf,
        \DateTimeImmutable $loanDate,
        \DateTimeImmutable $dueDate,
        string $status
    ) {
        $this->isbn = $isbn;
        $this->borrowerCpf = $borrowerCpf;
        $this->loanDate = $loanDate;
        $this->dueDate = $dueDate;
        $this->status = $status;
    }

    public function getIsbn(): Isbn
    {
        return $this->isbn;
    }

    public function setIsbn(Isbn $isbn): void
    {
        $this->isbn = $isbn;
    }

    public function getBorrowerCpf(): Cpf
    {
        return $this->borrowerCpf;
    }

    public function setBorrowerCpf(Cpf $borrowerCpf): void
    {
        $this->borrowerCpf = $borrowerCpf;
    }

    public function getLoanDate(): \DateTimeImmutable
    {
        return $this->loanDate;
    }

    public function setLoanDate(\DateTimeImmutable $loanDate): void
    {
        $this->loanDate = $loanDate;
    }

    public function getDueDate(): \DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeImmutable $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
