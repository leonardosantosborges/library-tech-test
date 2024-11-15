<?php

namespace Src\Infrastructure\Repositories;

use PDO;
use RuntimeException;
use Src\Application\DTOs\CustomerDto;
use Src\Application\DTOs\EmployeeDto;
use Src\Domain\Entities\Loan;
use Src\Domain\Enums\LoanStatus;
use Src\Domain\Repositories\EmployeeRepository;
use Src\Domain\Services\BookService;
use Src\Domain\Services\CustomerService;
use Src\Domain\Services\LoanService;
use Src\Domain\ValueObjects\Cpf;
use Src\Infrastructure\Employee\PasswordHasherPhp;
use Src\Domain\ValueObjects\Email;
use Src\Domain\ValueObjects\Isbn;

class EmployeeRepositorySqlite implements EmployeeRepository
{
    private PDO $pdo;
    private LoanService $loanService;
    private BookService $bookService;
    private CustomerService $customerService;

    public function __construct()
    {
        try {
            $this->pdo = new PDO('sqlite:' . __DIR__ . '/../../../library.sqlite', '', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (RuntimeException $e) {
            throw new RuntimeException('Failed to connect to the database: ' . $e->getMessage());
        }

        $customerRepository = new CustomerRepositorySqlite();
        $loanRepository = new LoanRepositorySqlite();
        $bookRepository = new BookRepositorySqlite();

        $this->customerService = new CustomerService($customerRepository);
        $this->loanService = new LoanService($loanRepository);
        $this->bookService = new BookService($bookRepository);
    }

    public function saveEmployee(EmployeeDto $employeeDto): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM employees WHERE cpf = ?');
        $stmt->execute([$employeeDto->getCpf()->getCpf()]);

        if ($stmt->fetch()) {
            throw new RuntimeException("Employee with CPF {$employeeDto->getCpf()->getCpf()} already exists.");
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO employees (name, cpf, email, password)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([
            $employeeDto->getName(),
            $employeeDto->getCpf()->getCpf(),
            $employeeDto->getEmail()->getEmail(),
            PasswordHasherPhp::hash($employeeDto->getPassword())
        ]);
    }

    public function removeEmployee(Cpf $cpf): bool
    {
        $stmt = $this->pdo->prepare('SELECT * FROM employees WHERE cpf = ?');
        $stmt->execute([$cpf->getCpf()]);

        if (!$stmt->fetch()) {
            throw new RuntimeException("Employee with CPF {$cpf->getCpf()} not found.");
        }

        $stmt = $this->pdo->prepare('DELETE FROM employees WHERE cpf = ?');
        return $stmt->execute([$cpf->getCpf()]);
    }

    public function findEmployeeByCpf(Cpf $cpf): ?EmployeeDto
    {
        $stmt = $this->pdo->prepare('SELECT * FROM employees WHERE cpf = ?');
        $stmt->execute([$cpf->getCpf()]);

        $employeeData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$employeeData) {
            return null;
        }

        return new EmployeeDto(
            $employeeData['name'],
            new Cpf($employeeData['cpf']),
            new Email($employeeData['email']),
            $employeeData['password']
        );
    }

    public function saveCustomer(CustomerDto $customerDto): void
    {
        $existingCustomer = $this->customerService->getCustomerByCpf($customerDto->getCpf()->getCpf());
        if ($existingCustomer) {
            throw new RuntimeException("Customer with CPF {$customerDto->getCpf()->getCpf()} already exists.");
        }

        $this->customerService->save(
            $customerDto->getName(),
            $customerDto->getCpf(),
            $customerDto->getEmail(),
            $customerDto->getPhoneNumber()
        );
    }

    public function removeCustomer(Cpf $cpf): bool
    {
        $customer = $this->customerService->getCustomerByCpf($cpf->getCpf());
        if (!$customer) {
            throw new RuntimeException("Customer with CPF {$cpf->getCpf()} not found.");
        }

        return $this->customerService->remove($cpf->getCpf());
    }

    public function findCustomerByCpf(Cpf $cpf): ?CustomerDto
    {
        return $this->customerService->getCustomerByCpf($cpf->getCpf());
    }

    public function listAllCustomers(): array
    {
        return $this->customerService->listAllCustomers();
    }

    public function listLoansByCustomerCpf(Cpf $cpf): array
    {
        return $this->loanService->findAllByBorrowerCpf($cpf->getCpf());
    }

    public function loanBook(Isbn $isbn, Cpf $cpf): void
    {
        $customer = $this->customerService->getCustomerByCpf($cpf->getCpf());
        if (!$customer) {
            throw new RuntimeException("Customer with CPF {$cpf->getCpf()} not found.");
        }

        $book = $this->bookService->getBookDetails($isbn->getIsbn());
        if (!$book || !$this->bookService->checkAvailability($isbn->getIsbn())) {
            throw new RuntimeException("Book with ISBN {$isbn->getIsbn()} is not available.");
        }

        $this->loanService->create($isbn, $cpf);
    }

    public function receiveBook(string $isbn, string $cpf): ?Loan
    {
        $customer = $this->customerService->getCustomerByCpf($cpf);
        if (!$customer) {
            throw new RuntimeException("Customer with CPF {$cpf} not found.");
        }

        $loan = $this->loanService->getLoanDetails($isbn, $cpf);
        if (!$loan) {
            throw new RuntimeException("No loan found for customer {$cpf} and book with ISBN {$isbn}.");
        }

        return $this->loanService->updateLoanStatus($isbn, $cpf, LoanStatus::RETURNED);
    }
}
