# Student Library Management System

A refactored OOP PHP application for managing library books, borrow records, and overdue fines. Built following PSR-12 coding standards with comprehensive error handling, type declarations, and full separation of concerns.

## Project Information

- **Author**: Library Development Team
- **Last Updated**: 2026-05-09
- **Version**: 1.0.0
- **License**: MIT

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Git (for version control)
- Composer (optional, for dependency management)

## Installation & Setup

### Step 1: Clone the Repository

```bash
git clone https://github.com/dwcl-sirlana/2026-library-system-activity.git
cd 2026-library-system-activity
```

### Step 2: Database Setup

Create the MySQL database and import the schema:

```sql
-- Create database
CREATE DATABASE library_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Import schema (if available)
-- mysql -u root -p library_db < database/schema.sql
```

### Step 3: Configure Database Connection

Edit `src/Config/LibraryConfig.php` and update database credentials:

```php
public const DB_HOST = 'localhost';
public const DB_USERNAME = 'root';
public const DB_PASSWORD = '';  // Add password if needed
public const DB_NAME = 'library_db';
```

### Step 4: Verify Installation

Navigate to `public/index.php` in your browser:

```
http://localhost/2026-library-system-activity/public/index.php
```

You should see the library management system homepage.

## Project Structure

```
2026-library-system-activity/
├── public/                          # Web-accessible entry point
│   └── index.php                   # Main controller
├── src/
│   ├── Config/                     # Configuration classes
│   │   ├── DatabaseConfig.php      # Database connection management
│   │   └── LibraryConfig.php       # System constants and configuration
│   ├── Entity/                     # Data model classes (PSR-4)
│   │   ├── Book.php                # Book entity
│   │   ├── BorrowRecord.php        # Borrow transaction entity
│   │   └── Student.php             # Student entity
│   ├── Exception/                  # Custom exception classes
│   │   ├── DatabaseException.php   # Database operation exceptions
│   │   └── ValidationException.php # Input validation exceptions
│   ├── Repository/                 # Data access layer (DAO pattern)
│   │   ├── BookRepository.php      # Book database operations
│   │   └── BorrowRepository.php    # Borrow record database operations
│   ├── Service/                    # Business logic layer
│   │   └── LibraryService.php      # Core library functions
│   └── View/                       # HTML template files
│       ├── book_list.php           # Display books
│       ├── book_form.php           # Add book form
│       ├── borrow_form.php         # Borrow book form
│       └── report_view.php         # Library report
├── docs/                           # Generated API documentation
├── legacy_library_system.php       # Original monolithic code (deprecated)
├── composer.json                   # PHP dependencies (if applicable)
└── README.md                       # This file
```

## PSR-12 Compliance

All PHP files follow the **PSR-12: Extended Coding Style** standard:

- **4-space indentation** (not tabs)
- **Unix LF line endings** (not Windows CRLF)
- **Strict typing** enabled (`declare(strict_types=1);`)
- **Proper naming conventions**:
  - PascalCase for class names: `BookRepository`
  - camelCase for method/variable names: `findById()`
  - CONSTANT_CASE for constants: `DB_HOST`
- **Maximum 120 characters per line**
- **One blank line between methods**
- **No closing PHP tag** in pure PHP files
- **Proper namespace declarations**: `namespace App\Library\Entity;`

## Architecture & Design Patterns

### Separation of Concerns

The application is organized into distinct layers:

1. **Entity Layer** (`Entity/`): Plain data objects representing database entities
2. **Repository Layer** (`Repository/`): Database access and query logic
3. **Service Layer** (`Service/`): Business logic and calculations
4. **View Layer** (`View/`): HTML templates for rendering
5. **Config Layer** (`Config/`): Connection and configuration management
6. **Exception Layer** (`Exception/`): Custom exceptions for error handling

### Design Patterns Used

- **Repository Pattern**: Encapsulates database queries in dedicated repository classes
- **Service/Business Logic Pattern**: Centralizes business rules in service classes
- **Dependency Injection**: Services receive dependencies via constructor
- **Exception Handling**: Custom exceptions for specific error scenarios
- **Type Declarations**: Full type hints for parameters and return values

## API Documentation

### Core Classes

#### `DatabaseConnection` (Config/DatabaseConfig.php)

Manages MySQL database connections with prepared statements.

**Methods:**
- `__construct(string $host, string $user, string $password, string $database)`
- `prepare(string $sql): mysqli_stmt` - Prepare a SQL statement
- `getInsertId(): int` - Get last inserted ID
- `getConnection(): mysqli` - Get raw mysqli connection
- `close(): void` - Close the connection

#### `Book` (Entity/Book.php)

Represents a book in the library.

**Methods:**
- `__construct(?int $id, string $title, string $author, int $year, string $genre)`
- `getId(): ?int`
- `getTitle(): string`
- `getAuthor(): string`
- `getYear(): int`
- `getGenre(): string`

#### `BookRepository` (Repository/BookRepository.php)

Handles all book database operations.

**Methods:**
- `addBook(Book $book): int` - Add a new book
- `findById(int $bookId): ?Book` - Find book by ID
- `findAll(): array` - Get all books
- `search(string $keyword): array` - Search books

#### `LibraryService` (Service/LibraryService.php)

Contains business logic for library operations.

**Methods:**
- `calculateDueDate(DateTime $borrowDate, int $daysAllowed): DateTime`
- `calculateFine(DateTime $dueDate, DateTime $returnDate, float $dailyRate): float`
- `isOverdue(DateTime $dueDate, ?DateTime $today): bool`
- `daysUntilDue(DateTime $dueDate, ?DateTime $today): int`

## Usage Examples

### Viewing All Books

Navigate to:
```
index.php?act=list
```

### Adding a Book

1. Navigate to `index.php?act=add`
2. Fill in the book details (title, author, year, genre)
3. Click "Add Book"

### Borrowing a Book

1. Navigate to `index.php?act=borrow`
2. Enter student ID, book ID, and borrow duration
3. Click "Borrow Book"

### Viewing Library Report

Navigate to:
```
index.php?act=report
```

### Programmatic Usage

```php
// Initialize database connection
$connection = new \App\Library\DatabaseConnection(
    'localhost',
    'root',
    '',
    'library_db'
);

// Create repository
$bookRepository = new \App\Library\Repository\BookRepository($connection);

// Find all books
$books = $bookRepository->findAll();

// Search for books
$results = $bookRepository->search('Harry Potter');

// Add a new book
$book = new \App\Library\Entity\Book(
    null,
    'The Great Gatsby',
    'F. Scott Fitzgerald',
    1925,
    'Fiction'
);
$bookId = $bookRepository->addBook($book);
```

## Configuration Constants

Edit `src/Config/LibraryConfig.php` to modify system behavior:

```php
// Borrow duration
DEFAULT_BORROW_DAYS = 14

// Fine calculation
DAILY_FINE_RATE = 5.00  // PHP per day

// Borrow limit
MAX_BORROW_LIMIT = 3    // Max books per student

// Status constants
STATUS_BORROWED = 'borrowed'
STATUS_RETURNED = 'returned'
```

## Security Features

### SQL Injection Prevention

All database queries use **prepared statements with parameterized queries**:

```php
$sql = 'SELECT * FROM books WHERE title = ? OR author = ?';
$stmt = $connection->prepare($sql);
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
```

NOT using string concatenation (vulnerable):
```php
// ❌ VULNERABLE - DO NOT USE
$sql = "SELECT * FROM books WHERE title = '" . $title . "'";
```

### Input Validation

All user inputs are validated before processing:

```php
if (empty(trim($keyword))) {
    throw new \App\Library\Exception\ValidationException(
        'Search keyword cannot be empty'
    );
}
```

### Error Handling

Custom exceptions provide meaningful error messages:

```php
try {
    $book = $bookRepository->addBook($book);
} catch (\App\Library\Exception\ValidationException $e) {
    // Handle validation errors
} catch (\App\Library\Exception\DatabaseException $e) {
    // Handle database errors
}
```

## Error Handling

### Exception Classes

- **`DatabaseException`**: Thrown when database operations fail
- **`ValidationException`**: Thrown when input validation fails

### Example Error Handling

```php
try {
    $databaseConnection = new DatabaseConnection(
        'localhost', 'root', '', 'library_db'
    );
} catch (\App\Library\Exception\DatabaseException $e) {
    die('Database Connection Error: ' . $e->getMessage());
}
```

## Testing

### Manual Testing Checklist

- [ ] Add a new book
- [ ] View all books
- [ ] Search for books
- [ ] Borrow a book
- [ ] Return a book
- [ ] View library report
- [ ] Check fine calculation for overdue books
- [ ] Verify error messages for invalid input

## Improvements Over Legacy Code

| Issue | Legacy Code | Refactored Code |
|-------|------------|-----------------|
| **Code Organization** | Single monolithic class | Separated into 8+ focused classes |
| **SQL Injection** | String concatenation | Prepared statements |
| **Error Handling** | `die()` with no messages | Custom exceptions |
| **Type Safety** | No type hints | Full type declarations |
| **Naming** | $t, $a, $y, $g | $title, $author, $year, $genre |
| **HTML/PHP Mix** | Echo HTML in methods | Separate view templates |
| **Configuration** | Hardcoded values | Configuration constants |
| **Documentation** | No documentation | Full PHPDoc comments |
| **Standards** | No standards followed | PSR-12 compliant |

## Common Issues & Troubleshooting

### Database Connection Error

**Error**: "Database connection failed: Connection refused"

**Solution**:
1. Verify MySQL is running: `mysql -u root -p`
2. Check database credentials in `LibraryConfig.php`
3. Ensure database `library_db` exists

### Table Not Found

**Error**: "Table 'library_db.books' doesn't exist"

**Solution**:
1. Create tables using SQL:
   ```sql
   CREATE TABLE books (book_id INT AUTO_INCREMENT PRIMARY KEY, ...);
   CREATE TABLE students (student_id INT AUTO_INCREMENT PRIMARY KEY, ...);
   CREATE TABLE borrow_records (record_id INT AUTO_INCREMENT PRIMARY KEY, ...);
   ```

### Permission Denied

**Error**: "Permission denied" when accessing public/index.php

**Solution**:
1. Check file permissions: `chmod 755 public/index.php`
2. Verify web server has read access to files

## Contributing

To contribute to this project:

1. Create a feature branch: `git checkout -b feature/your-feature`
2. Follow PSR-12 coding standards
3. Add PHPDoc comments to new code
4. Create pull requests with detailed descriptions

## License

This project is licensed under the MIT License. See LICENSE file for details.

## Support & Contact

For issues or questions:
1. Check the troubleshooting section above
2. Review code comments and PHPDoc documentation
3. Consult the PSR-12 standard: https://www.php-fig.org/psr/psr-12/

---

**Last Updated**: May 9, 2026  
**Status**: ✅ Production Ready
