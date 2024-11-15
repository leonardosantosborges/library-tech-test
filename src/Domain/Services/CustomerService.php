<?php

namespace Src\Domain\Services;

use InvalidArgumentException;
use Src\Application\DTOs\CustomerDto;
use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Email;
use Src\Domain\ValueObjects\PhoneNumber;
use Src\Infrastructure\Repositories\CustomerRepositorySqlite;
use Src\Infrastructure\Repositories\LoanRepositorySqlite;

class CustomerService
{
    private CustomerRepositorySqlite $customerRepository;
    private LoanService $loanService;

    public function __construct(CustomerRepositorySqlite $customerRepository, LoanService $loanService = null)
    {
        $this->customerRepository = $customerRepository;
        $this->loanService = $loanService ?: new LoanService(new LoanRepositorySqlite());
    }

    public function save(
        string $name,
        Cpf $cpf,
        Email $email,
        PhoneNumber $phoneNumber
    ): void {
        $customerDto = new CustomerDto(
            $name,
            $cpf,
            $email,
            $phoneNumber
        );

        $this->customerRepository->save($customerDto);
    }

    public function remove(string $cpf): bool
    {
        $customer = $this->customerRepository->findCustomerByCpf($cpf);
        if (!$customer) {
            throw new InvalidArgumentException("Customer with CPF {$cpf} not found.");
        }

        return $this->customerRepository->remove($cpf);
    }

    public function getCustomerByCpf(string $cpf): ?CustomerDto
    {
        return $this->customerRepository->findCustomerByCpf($cpf);
    }

    public function listAllCustomers(): array
    {
        return $this->customerRepository->listAllCustomers();
    }

    public function listLoansByCustomerCpf(string $cpf): array
    {
        return $this->loanService->findAllByBorrowerCpf($cpf);
    }
}
