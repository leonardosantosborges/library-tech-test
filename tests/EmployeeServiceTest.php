<?php
namespace LibraryTechTest;

use Domain\Entities\Loan;
use Domain\Enums\LoanStatus;
use PHPUnit\Framework\TestCase;
use Src\Application\DTOs\EmployeeDto;
use Src\Infrastructure\Repositories\EmployeeRepositorySqlite;
use Src\Infrastructure\Repositories\CustomerRepositorySqlite;
use Src\Infrastructure\Repositories\LoanRepositorySqlite;
use Src\Infrastructure\Repositories\BookRepositorySqlite;
use Domain\Services\EmployeeService;
use Src\Application\DTOs\CustomerDto;
use Domain\ValueObjects\Cpf;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\PhoneNumber;
use Domain\ValueObjects\Isbn;
use function PHPUnit\Framework\assertEquals;

class EmployeeServiceTest extends TestCase
{
    private $employeeRepositoryMock;
    private $customerRepositoryMock;
    private $loanRepositoryMock;
    private $bookRepositoryMock;
    private $employeeService;

    protected function setUp(): void
    {
        $this->employeeRepositoryMock = $this->createMock(EmployeeRepositorySqlite::class);
        $this->customerRepositoryMock = $this->createMock(CustomerRepositorySqlite::class);
        $this->loanRepositoryMock = $this->createMock(LoanRepositorySqlite::class);
        $this->bookRepositoryMock = $this->createMock(BookRepositorySqlite::class);

        $this->employeeService = new EmployeeService(
            $this->employeeRepositoryMock,
            $this->customerRepositoryMock,
            $this->loanRepositoryMock,
            $this->bookRepositoryMock
        );
    }


    public function testSaveEmployee(): void
    {
        $cpf = new Cpf('494.730.800-18');
        $employeeDto = new EmployeeDto(
            'Joao Borges',
            $cpf,
            new Email('joao.borges@example.com'),
            'password123'
        );

        $this->employeeRepositoryMock->expects($this->once())
            ->method('saveEmployee')
            ->with($employeeDto);

        $this->employeeService->saveEmployee(
            'Joao Borges',
            $cpf,
            new Email('joao.borges@example.com'),
            'password123'
        );

        $this->assertEquals('Joao Borges', $employeeDto->getName());
        $this->assertEquals('494.730.800-18', $employeeDto->getCpf()->getCpf());
        $this->assertEquals('joao.borges@example.com', $employeeDto->getEmail()->getEmail());
        $this->assertNotEmpty($employeeDto->getPassword());
    }

    public function testRemoveEmployee(): void
    {
        $cpf = new Cpf('599.398.130-07');
        $employeeDto = new EmployeeDto(
            'Leonardo Borges',
            $cpf,
            new Email('leo.borges@example.com'),
            'password123'
        );

        $this->employeeRepositoryMock->expects($this->once())
            ->method('removeEmployee')
            ->with($cpf)
            ->willReturn(true);

        $result = $this->employeeService->removeEmployee($cpf);

        $this->assertTrue($result);
    }

    public function testFindEmployeeByCpf(): void
    {
        $cpf = new Cpf('599.398.130-07');
        $employeeDto = new EmployeeDto(
            'Leonardo Borges',
            $cpf,
            new Email('leo.borges@example.com'),
            'password123'
        );

        $this->employeeRepositoryMock->expects($this->once())
            ->method('findEmployeeByCpf')
            ->with($cpf)
            ->willReturn($employeeDto);

        $result = $this->employeeService->findEmployeeByCpf($cpf);

        $this->assertEquals($employeeDto, $result);
    }

    public function testFindEmployeeByCpfReturnsNullWhenNotFound(): void
    {
        $cpf = new Cpf('599.398.130-07');

        $this->employeeRepositoryMock->expects($this->once())
            ->method('findEmployeeByCpf')
            ->with($cpf)
            ->willReturn(null);

        $result = $this->employeeService->findEmployeeByCpf($cpf);

        $this->assertNull($result);
    }

    public function testSaveCustomer(): void
    {
        $name = 'Leonardo Borges';
        $cpf = new Cpf('699.475.910-50');
        $email = new Email('leo.borges@example.com');
        $phoneNumber = new PhoneNumber('11', '969045235');

        $customerDto = new CustomerDto($name, $cpf, $email, $phoneNumber);

        $this->customerRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->equalTo($customerDto));

        $this->customerRepositoryMock->expects($this->once())
            ->method('findCustomerByCpf')
            ->with($customerDto->getCpf()->getCpf())
            ->willReturn($customerDto);

        $result = $this->customerRepositoryMock->findCustomerByCpf($cpf->getCpf());

        $this->employeeService->saveCustomer($name, $cpf, $email, $phoneNumber);

        assertEquals($customerDto, $result);
    }


    public function testRemoveCustomer()
    {
        $cpf = new Cpf('599.398.130-07');
        $customer = new CustomerDto(
            'Leonardo Borges',
            $cpf,
            new Email('leo.borges@example.com'),
            new PhoneNumber('11', '987624321')
        );

        $this->customerRepositoryMock->expects($this->once())
            ->method('findCustomerByCpf')
            ->with($this->equalTo($cpf->getCpf()))
            ->willReturn($customer);

        $this->customerRepositoryMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($cpf->getCpf()))
            ->willReturn(true);

        $result = $this->employeeService->removeCustomer($cpf);

        $this->assertTrue($result);
    }


    public function testFindCustomerByCpf()
    {
        $cpf = new Cpf('599.398.130-07');
        $customerDto = new CustomerDto(
            'Leonardo Borges',
            $cpf,
            new Email('leo.borges@example.com'),
            new PhoneNumber('11','970894345')
        );

        $this->employeeRepositoryMock->expects($this->once())
            ->method('findCustomerByCpf')
            ->with($this->equalTo($cpf))
            ->willReturn($customerDto);

        $result = $this->employeeService->findCustomerByCpf($cpf);

        $this->assertEquals($customerDto, $result);
    }

    public function testLoanBook()
    {
        $isbn = new Isbn('978-3-16-148410-0');
        $cpf = new Cpf('599.398.130-07');

        $this->employeeRepositoryMock->expects($this->once())
            ->method('loanBook')
            ->with($this->equalTo($isbn), $this->equalTo($cpf));

        $this->employeeService->loanBook($isbn, $cpf);
    }

    public function testReceiveBook()
    {
        $isbn = new Isbn('0-7081-0968-3');
        $cpf = new Cpf('599.398.130-07');
        $loan = new Loan(
            $isbn,
            $cpf,
            new \DateTimeImmutable(),
            new \DateTimeImmutable('+7 days'),
            LoanStatus::BORROWED
        );

        $this->employeeRepositoryMock->expects($this->once())
            ->method('receiveBook')
            ->with($this->equalTo($isbn->getIsbn()), $this->equalTo($cpf->getCpf()))
            ->willReturn($loan);

        $result = $this->employeeService->receiveBook($isbn->getIsbn(), $cpf->getCpf());

        $this->assertEquals($loan, $result);
    }

    public function testListLoansByCustomerCpf()
    {
        $cpf = new Cpf('599.398.130-07');
        $loan1 = new Loan(
            new Isbn('978-3-16-148410-0'),
            $cpf, new \DateTimeImmutable(),
            new \DateTimeImmutable(),
            LoanStatus::BORROWED
        );

        $loan2 = new Loan(
            new Isbn('978-3-16-148411-7'),
            $cpf,
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
            LoanStatus::RETURNED
        );

        $loans = [
            $loan1,
            $loan2
        ];

        $this->employeeRepositoryMock->expects($this->once())
            ->method('listLoansByCustomerCpf')
            ->with($this->equalTo($cpf))
            ->willReturn($loans);

        $result = $this->employeeService->listLoansByCustomerCpf($cpf);

        $this->assertEquals($loans, $result);
    }

    public function testListAllCustomers()
    {
        $customers = [
            new CustomerDto(
                'Leonardo Borges',
                new Cpf('599.398.130-07'),
                new Email('leo.borges@example.com'),
                new PhoneNumber('11', '969045235')
            ),
            new CustomerDto(
                'Jose Aldo',
                new Cpf('987.654.321-00'),
                new Email('jose.aldo@example.com'),
                new PhoneNumber('11', '996075432')
            )
        ];

        $this->employeeRepositoryMock->expects($this->once())
            ->method('listAllCustomers')
            ->willReturn($customers);

        $result = $this->employeeService->listAllCustomers();

        $this->assertEquals($customers, $result);
        $this->assertCount(2, $result);
    }
}