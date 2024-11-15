<?php

namespace Src\Domain\Repositories;

use Src\Application\DTOs\LoanDto;

interface LoanRepository
{
    public function save(LoanDto $loanDto);
    public function findAllByBorrowerCpf(string $borrowerCpf);
    public function getLoanDetails(string $isbn, string $borrowerCpf);
    public function updateLoanStatus(string $isbn, string $borrowerCpf, string $status);
    public function findAll();
}
