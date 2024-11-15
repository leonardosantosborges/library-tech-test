<?php

namespace LibraryTechTest\Services;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Src\Application\DTOs\ManagerDto;
use Src\Domain\Services\BookService;
use Src\Domain\Services\EmployeeService;
use Src\Domain\Services\ManagerService;
use Src\Domain\ValueObjects\Cpf;
use Src\Infrastructure\Repositories\BookRepositorySqlite;
use Src\Infrastructure\Repositories\EmployeeRepositorySqlite;
use Src\Infrastructure\Repositories\ManagerRepositorySqlite;
use Src\Domain\ValueObjects\Email;

class ManagerServiceTest extends TestCase
{
    private ManagerRepositorySqlite $managerRepositoryMock;
    private EmployeeRepositorySqlite $employeeRepositoryMock;
    private BookRepositorySqlite $bookRepositoryMock;
    private ManagerService $managerService;

    protected function setUp(): void
    {
        $this->managerRepositoryMock = $this->createMock(ManagerRepositorySqlite::class);
        $this->employeeRepositoryMock = $this->createMock(EmployeeRepositorySqlite::class);
        $this->bookRepositoryMock = $this->createMock(BookRepositorySqlite::class);

        $this->employeeServiceMock = $this->createMock(EmployeeService::class);
        $this->bookServiceMock = $this->createMock(BookService::class);

        $this->managerService = new ManagerService(
            $this->managerRepositoryMock,
            $this->employeeServiceMock,
            $this->bookServiceMock
        );
    }


    public function testSaveManagerSuccessfully(): void
    {
        $managerDto = new ManagerDto(
            'John Doe',
            new Cpf('599.398.130-07'),
            new Email('john.doe@example.com'),
            'password123'
        );

        $this->managerRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($managerDto);

        $this->managerService->saveManager($managerDto);
    }

    public function testSaveManagerThrowsException(): void
    {
        $managerDto = new ManagerDto(
            'John Doe',
            new Cpf('599.398.130-07'),
            new Email('john.doe@example.com'),
            'password123'
        );

        $this->managerRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new RuntimeException('Manager already exists.'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to save manager: Manager already exists.');

        $this->managerService->saveManager($managerDto);
    }

    public function testRemoveManagerSuccessfully(): void
    {
        $cpf = '599.398.130-07';

        $this->managerRepositoryMock
            ->expects($this->once())
            ->method('remove')
            ->with($cpf)
            ->willReturn(true);

        $result = $this->managerService->removeManager($cpf);

        $this->assertTrue($result);
    }

    public function testRemoveManagerThrowsException(): void
    {
        $cpf = '599.398.130-07';

        $this->managerRepositoryMock
            ->expects($this->once())
            ->method('remove')
            ->willThrowException(new RuntimeException('Manager not found.'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to remove manager: Manager not found.');

        $this->managerService->removeManager($cpf);
    }

    public function testListAllManagers(): void
    {
        $managerDto1 = new ManagerDto(
            'John Doe',
            new Cpf('371.399.630-02'),
            new Email('john.doe@example.com'),
            'password123'
        );
        $managerDto2 = new ManagerDto(
            'Jane Doe',
            new Cpf('436.408.690-87'),
            new Email('jane.doe@example.com'),
            'password123'
        );

        $this->managerRepositoryMock
            ->expects($this->once())
            ->method('listAllManagers')
            ->willReturn([$managerDto1, $managerDto2]);

        $result = $this->managerService->listAllManagers();

        $this->assertCount(2, $result);
        $this->assertContains($managerDto1, $result);
        $this->assertContains($managerDto2, $result);
    }

    public function testFindManagerByCpfSuccessfully(): void
    {
        $cpf = new Cpf('371.399.630-02');
        $managerDto = new ManagerDto(
            'John Doe',
            $cpf,
            new Email('john.doe@example.com'),
            'password123'
        );

        $this->managerRepositoryMock
            ->expects($this->once())
            ->method('findManagerByCpf')
            ->with($cpf->getCpf())
            ->willReturn($managerDto);

        $result = $this->managerService->findManagerByCpf($cpf->getCpf());

        $this->assertEquals($managerDto, $result);
    }


    public function testFindManagerByCpfThrowsException(): void
    {
        $cpf = '599.398.130-07';

        $this->managerRepositoryMock
            ->expects($this->once())
            ->method('findManagerByCpf')
            ->willThrowException(new RuntimeException("Manager with CPF $cpf not found."));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Failed to find manager: Manager with CPF $cpf not found.");

        $this->managerService->findManagerByCpf($cpf);
    }
}
