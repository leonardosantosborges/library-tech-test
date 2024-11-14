<?php

namespace Domain\Repositories;

use Src\Application\DTOs\BookDto;
use Src\Application\DTOs\CustomerDto;
use Src\Application\DTOs\EmployeeDto;

interface ManagerRepository
{
    public function addBook(BookDto $bookDto): void;
    
    public function removeBook(string $isbn): bool;

    public function addEmployee(EmployeeDto $customerDto): void;

    public function removeEmployee(string $cpf): bool;

    public function listAllLoans(): array;
    
    public function listAllCustomers(): array;
}
