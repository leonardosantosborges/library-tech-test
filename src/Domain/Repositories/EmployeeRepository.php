<?php

namespace Src\Domain\Repositories;

use Src\Application\DTOs\CustomerDto;
use Src\Application\DTOs\EmployeeDto;
use Src\Domain\Entities\Loan;
use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Isbn;
interface EmployeeRepository
{
    public function saveEmployee(EmployeeDto $employeeDto): void;

    public function removeEmployee(Cpf $cpf): bool;

    public function findEmployeeByCpf(Cpf $cpf): ?EmployeeDto;

    public function saveCustomer(CustomerDto $customerDto): void;

    public function removeCustomer(Cpf $cpf): bool;

    public function findCustomerByCpf(Cpf $cpf): ?CustomerDto;

    public function listAllCustomers(): array;

    public function listLoansByCustomerCpf(Cpf $cpf): array;

    public function loanBook(Isbn $isbn, Cpf $cpf): void;

    public function receiveBook(string $isbn, string $cpf): ?Loan;
}
