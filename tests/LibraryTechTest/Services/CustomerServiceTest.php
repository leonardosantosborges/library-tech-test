<?php

namespace LibraryTechTest\Services;


use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Src\Application\DTOs\CustomerDto;
use Src\Domain\Services\CustomerService;
use Src\Domain\Services\LoanService;
use Src\Domain\ValueObjects\Cpf;
use Src\Domain\ValueObjects\Email;
use Src\Domain\ValueObjects\PhoneNumber;
use Src\Infrastructure\Repositories\CustomerRepositorySqlite;

class CustomerServiceTest extends TestCase
{
    private CustomerRepositorySqlite $customerRepository;
    private CustomerService $customerService;

    protected function setUp(): void
    {
        $this->customerRepository = $this->createMock(CustomerRepositorySqlite::class);
        $this->customerService = new CustomerService($this->customerRepository);
    }

    public function testAddCustomer()
    {
        $name = 'Leonardo Borges';
        $cpf = new Cpf('461.261.950-19');
        $email = new Email('leo.borges@example.com');
        $phoneNumber = new PhoneNumber('11', '970893041');

        $customerDto = new CustomerDto($name, $cpf, $email, $phoneNumber);

        $this->customerRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($customerDto));

        $this->customerRepository->expects($this->once())
            ->method('findCustomerByCpf')
            ->with($this->equalTo($cpf->getCpf()))
            ->willReturn($customerDto);

        $this->customerService->save($name, $cpf, $email, $phoneNumber);

        $result = $this->customerRepository->findCustomerByCpf($cpf->getCpf());

        $this->assertEquals($customerDto, $result);
    }


    public function testRemoveCustomer()
    {
        $cpf = new Cpf('461.261.950-19');
        $customer = new CustomerDto(
            'Leonardo Borges',
            $cpf,
            new Email('leo.borges@example.com'),
            new PhoneNumber('11', '987624321')
        );

        $this->customerRepository->expects($this->once())
            ->method('findCustomerByCpf')
            ->with($cpf->getCpf())
            ->willReturn($customer);

        $this->customerRepository->expects($this->once())
            ->method('remove')
            ->with($cpf->getCpf())
            ->willReturn(true);

        $result = $this->customerService->remove($cpf->getCpf());

        $this->assertTrue($result);
    }

    public function testRemoveCustomerNotFound()
    {
        $cpf = new Cpf('461.261.950-19');

        $this->customerRepository->expects($this->once())
            ->method('findCustomerByCpf')
            ->with($cpf->getCpf())
            ->willReturn(null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Customer with CPF {$cpf->getCpf()} not found.");

        $this->customerService->remove($cpf->getCpf());
    }

    public function testGetCustomerByCpf()
    {
        $cpf = new Cpf('761.008.500-60');
        $customer = new CustomerDto(
            'Leonardo Borges',
            $cpf,
            new Email('leo.borges@example.com'),
            new PhoneNumber('11', '987624321')
        );

        $this->customerRepository->expects($this->once())
            ->method('findCustomerByCpf')
            ->with($cpf->getCpf())
            ->willReturn($customer);

        $customer = $this->customerService->getCustomerByCpf($cpf->getCpf());

        $this->assertInstanceOf(CustomerDto::class, $customer);
        $this->assertEquals($cpf, $customer->getCpf());
    }

    public function testGetCustomerByCpfNotFound()
    {
        $cpf = '12345678901';

        $this->customerRepository->expects($this->once())
            ->method('findCustomerByCpf')
            ->with($cpf)
            ->willReturn(null);

        $customer = $this->customerService->getCustomerByCpf($cpf);

        $this->assertNull($customer);
    }

    public function testListAllCustomers()
    {
        $customerDto1 = new CustomerDto(
            'Leonardo Borges',
            new Cpf('716.939.860-51'),
            new Email('leo.borges@example.com'),
            new PhoneNumber('11','987624321')
        );

        $customerDto2 = new CustomerDto(
            'Jose Aldo',
            new Cpf('750.212.910-33'),
            new Email('jose.aldo@example.com'),
            new PhoneNumber('11', '987654321')
        );

        $this->customerRepository->expects($this->once())
            ->method('listAllCustomers')
            ->willReturn([
                $customerDto1,
                $customerDto2
            ]);

        $customers = $this->customerService->listAllCustomers();

        $this->assertCount(2, $customers);
        $this->assertInstanceOf(CustomerDto::class, $customers[0]);
    }

    public function testListLoansByCustomerCpf()
    {
        $cpf = new Cpf('028.341.780-38');

        $loanServiceMock = $this->createMock(LoanService::class);

        $loanServiceMock->expects($this->once())
            ->method('findAllByBorrowerCpf')
            ->with($cpf->getCpf())
            ->willReturn([]);

        $this->customerService = new CustomerService($this->customerRepository, $loanServiceMock);

        $loans = $this->customerService->listLoansByCustomerCpf($cpf->getCpf());

        $this->assertIsArray($loans);
        $this->assertEmpty($loans);
    }

}
