<?php

namespace Entities;

use ValueObjects\Cpf;
use ValueObjects\Isbn;

class Loan
{
    private Isbn $isbn;
    private Cpf $borrowerCpf;
    private \DateTimeImmutable $loanDate;
    private \DateTimeImmutable $dueDate;

    /**
     * @param Isbn $isbn
     * @param Cpf $borrowerCpf
     * @param \DateTimeImmutable $loanDate
     * @param \DateTimeImmutable $dueDate
     */
    public function __construct(Isbn $isbn, Cpf $borrowerCpf, \DateTimeImmutable $loanDate, \DateTimeImmutable $dueDate)
    {
        $this->isbn = $isbn;
        $this->borrowerCpf = $borrowerCpf;
        $this->loanDate = $loanDate;
        $this->dueDate = $dueDate;
    }


}
