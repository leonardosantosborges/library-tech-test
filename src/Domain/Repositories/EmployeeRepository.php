<?php

namespace Domain\Repositories;

use Domain\Entities\Loan;
use Domain\ValueObjects\Cpf;
use Domain\ValueObjects\Isbn;
use Src\Application\DTOs\CustomerDto;
use Src\Application\DTOs\EmployeeDto;

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