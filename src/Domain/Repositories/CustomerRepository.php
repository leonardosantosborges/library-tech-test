<?php

namespace Domain\Repositories;

use Src\Application\DTOs\CustomerDto;

interface CustomerRepository
{
    public function save(CustomerDto $customerDto): void;

    public function remove(string $cpf): bool;

    public function findCustomerByCpf(string $cpf): ?CustomerDto;

    public function listAllCustomers(): array;
}
