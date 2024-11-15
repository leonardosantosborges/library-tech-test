<?php

namespace Src\Infrastructure\Repositories;

use Src\Domain\Repositories\ManagerRepository;
use Src\Application\DTOs\ManagerDto;
use PDO;
use RuntimeException;
use Src\Infrastructure\Employee\PasswordHasherPhp;

class ManagerRepositorySqlite implements ManagerRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        try {
            $this->pdo = new PDO(
                'sqlite:' . __DIR__ . '/../../../library.sqlite',
                '',
                '',
                [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        } catch (RuntimeException $e) {
            throw new RuntimeException('Failed to connect to the database: ' . $e->getMessage());
        }
    }

    public function save(ManagerDto $managerDto): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM managers WHERE cpf = ?');
        $stmt->execute([$managerDto->getCpf()]);

        if ($stmt->fetch()) {
            throw new RuntimeException("Manager with CPF {$managerDto->getCpf()} already exists.");
        }

        $stmt = $this->pdo->prepare(
            '
            INSERT INTO managers (name, cpf, email, password)
            VALUES (?, ?, ?, ?)
        '
        );
        $stmt->execute(
            [
            $managerDto->getName(),
            $managerDto->getCpf(),
            $managerDto->getEmail(),
            PasswordHasherPhp::hash($managerDto->getPassword())
            ]
        );
    }

    public function remove(string $cpf): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM managers WHERE cpf = ?');
        $stmt->execute([$cpf]);

        return $stmt->rowCount() > 0;
    }

    public function listAllManagers(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM managers');
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $managers = [];
        foreach ($result as $row) {
            $managers[] = new ManagerDto($row['name'], $row['cpf'], $row['email'], $row['password']);
        }

        return $managers;
    }

    public function findManagerByCpf(string $cpf): ManagerDto
    {
        $stmt = $this->pdo->prepare('SELECT * FROM managers WHERE cpf = ?');
        $stmt->execute([$cpf]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new RuntimeException("Manager with CPF $cpf not found.");
        }

        return new ManagerDto($row['name'], $row['cpf'], $row['email'], $row['password']);
    }
}
