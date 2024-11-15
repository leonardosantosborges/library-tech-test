<?php

namespace Src\Domain\Repositories;

use Src\Application\DTOs\ManagerDto;

interface ManagerRepository
{
    public function save(ManagerDto $managerDto): void;
    public function remove(string $cpf): bool;

    public function listAllManagers(): array;
    public function findManagerByCpf(string $cpf): ManagerDto;
}
