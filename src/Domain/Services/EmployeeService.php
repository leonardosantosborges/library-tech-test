<?php

namespace Src\Domain\Services;

use Src\Application\DTOs\CustomerDto;
use Src\Application\DTOs\EmployeeDto;
use Src\Domain\Entities\Loan;
use Src\Domain\Repositories\BookRepository;
use Src\Domain\Repositories\CustomerRepository;
use Src\Domain\Repositories\LoanRepository;
use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Email;
use Src\Domain\ValueObjects\Isbn;
use Src\Domain\ValueObjects\PhoneNumber;
use Src\Infrastructure\Repositories\EmployeeRepositorySqlite;

class EmployeeService
{
    private EmployeeRepositorySqlite $employeeRepository;
    private CustomerService $customerService;
    private LoanService $loanService;
    private BookService $bookService;

    public function __construct(
        EmployeeRepositorySqlite $employeeRepository,
        CustomerRepository $customerRepository,
        LoanRepository $loanRepository,
        BookRepository $bookRepository
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->customerService = new CustomerService($customerRepository);
        $this->loanService = new LoanService($loanRepository);
        $this->bookService = new BookService($bookRepository);
    }

    public function saveEmployee(
        string $name,
        Cpf $cpf,
        Email $email,
        string $password,
    ): void {
        $employeeDto = new EmployeeDto(
            $name,
            $cpf,
            $email,
            $password
        );

        $this->employeeRepository->saveEmployee($employeeDto);
    }

    public function removeEmployee(Cpf $cpf): bool
    {
        return $this->employeeRepository->removeEmployee($cpf);
    }

    public function findEmployeeByCpf(Cpf $cpf): ?EmployeeDto
    {
        return $this->employeeRepository->findEmployeeByCpf($cpf);
    }

    public function saveCustomer(
        string $name,
        Cpf $cpf,
        Email $email,
        PhoneNumber $phoneNumber
    ): void {
        $this->customerService->save(
            $name,
            $cpf,
            $email,
            $phoneNumber
        );
    }

    public function removeCustomer(Cpf $cpf): bool
    {
        return $this->customerService->remove($cpf->getCpf());
    }

    public function findCustomerByCpf(Cpf $cpf): ?CustomerDto
    {
        return $this->employeeRepository->findCustomerByCpf($cpf);
    }

    public function listAllCustomers(): array
    {
        return $this->employeeRepository->listAllCustomers();
    }

    public function loanBook(Isbn $isbn, Cpf $cpf): void
    {
        $this->employeeRepository->loanBook($isbn, $cpf);
    }

    public function receiveBook(string $isbn, string $cpf): ?Loan
    {
        return $this->employeeRepository->receiveBook($isbn, $cpf);
    }

    public function listLoansByCustomerCpf(Cpf $cpf): array
    {
        return $this->employeeRepository->listLoansByCustomerCpf($cpf);
    }
}
