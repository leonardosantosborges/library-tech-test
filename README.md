# Library Tech Test

## Description

This project is a library management system, developed as part of a technical test. The system allows the management of books, loans, customers, and employees.
It follows **Domain-Driven Design (DDD)** principles and uses **PHP 8.0+**, **PHPUnit** for unit testing, **PHP CodeSniffer** for code standards, and an **SQLite** database for persistence.


## Technologies

- **PHP 8.0+**
- **SQLite** (Database)
- **PHPUnit** (Testing)
- **PHP CodeSniffer** (PSR-2 Code Standard)
- **Composer** (Dependency Management)

## Project Structure
```
src/
  ├── Application/          # Application layer (DTOs)
  ├── Domain/               # Domain layer (Entities, Services, Repositories)
  └── Infrastructure/       # Infrastructure layer (Database Repositories)
tests/
  ├── LibraryTechTest/      # Unit tests for the application
```

### 1. Clone the repository
```
git clone https://github.com/leonardosantosborges/library-tech-test.git
```

### 2. Install dependences
```
composer install
```

### 3. Verify code style
```
composer cs
```

### 4. Execute tests
```
./vendor/bin/phpunit --configuration phpunit.xml
```
