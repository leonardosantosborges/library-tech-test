<?php

namespace Src\Application\DTOs;

use DateTimeImmutable;
use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Isbn;

class LoanDto
{
    private Isbn $isbn;
    private Cpf $borrowerCpf;
    private ?DateTimeImmutable $loanDate;
    private ?DateTimeImmutable $dueDate;
    private ?string $status;

    /**
     * @param Isbn $isbn
     * @param Cpf $borrowerCpf
     * @param ?DateTimeImmutable $loanDate
     * @param ?DateTimeImmutable $dueDate
     * @param ?string $status
     */
    public function __construct(
        Isbn $isbn,
        Cpf $borrowerCpf,
        ?DateTimeImmutable $loanDate = null,
        ?DateTimeImmutable $dueDate = null,
        string $status = null
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

    public function getBorrowerCpf(): Cpf
    {
        return $this->borrowerCpf;
    }

    public function getLoanDate(): ?DateTimeImmutable
    {
        return $this->loanDate;
    }

    public function getDueDate(): ?DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
