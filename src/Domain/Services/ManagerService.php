<?php

namespace Domain\Services;

use Domain\Repositories\ManagerRepository;
use Domain\ValueObjects\Cpf;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\Isbn;
use Src\Application\DTOs\ManagerDto;
use RuntimeException;

class ManagerService
{
    private ManagerRepository $managerRepository;
    private EmployeeService $employeeService;
    private BookService $bookService;

    public function __construct(
        ManagerRepository $managerRepository,
        EmployeeService $employeeService,
        BookService $bookService
    ) {
        $this->managerRepository = $managerRepository;
        $this->employeeService = $employeeService;
        $this->bookService = $bookService;
    }


    public function saveManager(ManagerDto $managerDto): void
    {
        try {
            $this->managerRepository->save($managerDto);
        } catch (RuntimeException $e) {
            throw new RuntimeException("Failed to save manager: " . $e->getMessage());
        }
    }

    public function removeManager(string $cpf): bool
    {
        try {
            return $this->managerRepository->remove($cpf);
        } catch (RuntimeException $e) {
            throw new RuntimeException("Failed to remove manager: " . $e->getMessage());
        }
    }

    public function saveEmployee(
        string $name,
        Cpf $cpf,
        Email $email,
        string $password
    ): void {
        try {
            $this->employeeService->saveEmployee($name, $cpf, $email, $password);
        } catch (RuntimeException $e) {
            throw new RuntimeException("Failed to save employee: " . $e->getMessage());
        }
    }

    public function removeEmployee(string $cpf): bool
    {
        try {
            $cpf = new Cpf($cpf);
            return $this->employeeService->removeEmployee($cpf);
        } catch (RuntimeException $e) {
            throw new RuntimeException("Failed to remove employee: " . $e->getMessage());
        }
    }

    public function saveBook(
        Isbn $isbn,
        string $title,
        string $author,
        \DateTimeImmutable $publicationDate,
        int $stock
    ): void {
        try {
            $this->bookService->create(
                $isbn,
                $title,
                $author,
                $publicationDate,
                $stock
            );
        } catch (RuntimeException $e) {
            throw new RuntimeException("Failed to save book: " . $e->getMessage());
        }
    }

    public function removeBook(string $isbn): bool
    {
        try {
            return $this->bookService->removeBook($isbn);
        } catch (RuntimeException $e) {
            throw new RuntimeException("Failed to remove book: " . $e->getMessage());
        }
    }

    public function listAllManagers(): array
    {
        return $this->managerRepository->listAllManagers();
    }

    public function findManagerByCpf(string $cpf): ManagerDto
    {
        try {
            return $this->managerRepository->findManagerByCpf($cpf);
        } catch (RuntimeException $e) {
            throw new RuntimeException("Failed to find manager: " . $e->getMessage());
        }
    }
}
