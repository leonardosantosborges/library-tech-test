<?php

namespace Src\Infrastructure\Repositories;

use Domain\Repositories\CustomerRepository;
use Domain\ValueObjects\Cpf;
use Domain\ValueObjects\Email;
use Src\Application\DTOs\CustomerDto;
use PDO;
use RuntimeException;

class CustomerRepositorySqlite implements CustomerRepository
{
    private PDO $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO('sqlite:' . __DIR__ . '/../../../library.sqlite', '', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (RuntimeException $e) {
            throw new RuntimeException('Failed to connect to the database: ' . $e->getMessage());
        }
    }

    public function save(CustomerDto $customerDto): void
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO customers (cpf, name, email, phone_number) VALUES (?, ?, ?, ?)');
            $stmt->execute([
                $customerDto->getCpf()->getCpf(),
                $customerDto->getName(),
                $customerDto->getEmail()->getEmail(),
                $customerDto->getPhoneNumber()->getPhoneNumber()
            ]);
        } catch (RuntimeException $e) {
            throw new RuntimeException('Failed to add customer: ' . $e->getMessage());
        }
    }

    public function remove(string $cpf): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM customers WHERE cpf = ?');
            return $stmt->execute([$cpf]);
        } catch (RuntimeException $e) {
            throw new RuntimeException('Failed to remove customer: ' . $e->getMessage());
        }
    }

    public function findCustomerByCpf(string $cpf): ?CustomerDto
    {
        $stmt = $this->pdo->prepare('SELECT * FROM customers WHERE cpf = ? LIMIT 1');
        $stmt->execute([$cpf]);
        $customerData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $customerData ? $this->mapCustomer($customerData) : null;
    }

    public function listAllCustomers(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM customers');
        $customersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $customers = [];
        foreach ($customersData as $customerData) {
            $customers[] = $this->mapCustomer($customerData);
        }

        return $customers;
    }

    private function mapCustomer(array $customerData): CustomerDto
    {
        return new CustomerDto(
            $customerData['name'],
            new Cpf($customerData['cpf']),
            new Email($customerData['email']),
            $customerData['phone_number']
        );
    }
}