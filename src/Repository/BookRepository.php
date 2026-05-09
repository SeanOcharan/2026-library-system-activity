<?php

declare(strict_types=1);

namespace App\Library\Repository;

use App\Library\Entity\Book;
use App\Library\DatabaseConnection;
use App\Library\Exception\DatabaseException;
use App\Library\Exception\ValidationException;

/**
 * Book Repository
 *
 * Manages all database operations for Book entities.
 * Encapsulates all SQL queries related to books, ensuring consistent data access
 * and preventing SQL injection through prepared statements.
 *
 * @author Library Developer
 * @since 2026-05-09
 */
class BookRepository
{
    /**
     * @var DatabaseConnection The active database connection instance
     */
    private DatabaseConnection $connection;

    /**
     * Constructor
     *
     * @param DatabaseConnection $connection The database connection to use
     */
    public function __construct(DatabaseConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Add a new book to the library database
     *
     * Validates the book data and executes a prepared INSERT statement,
     * then returns the auto-generated book ID.
     *
     * @param Book $book The book entity to persist
     *
     * @return int The auto-generated book ID
     *
     * @throws DatabaseException If the database query fails
     * @throws ValidationException If the book data is invalid
     */
    public function addBook(Book $book): int
    {
        $this->validateBook($book);

        $sql = 'INSERT INTO books (title, author, year, genre) VALUES (?, ?, ?, ?)';
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new DatabaseException('Failed to prepare statement for addBook');
        }

        $stmt->bind_param(
            'ssis',
            $book->getTitle(),
            $book->getAuthor(),
            $book->getYear(),
            $book->getGenre()
        );

        if (!$stmt->execute()) {
            throw new DatabaseException('Failed to insert book: ' . $stmt->error);
        }

        return $this->connection->getInsertId();
    }

    /**
     * Find a book by ID
     *
     * @param int $bookId The book ID to search for
     *
     * @return Book|null The Book object if found, null otherwise
     *
     * @throws DatabaseException If the database query fails
     */
    public function findById(int $bookId): ?Book
    {
        if ($bookId <= 0) {
            throw new ValidationException('Book ID must be greater than 0');
        }

        $sql = 'SELECT * FROM books WHERE book_id = ?';
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new DatabaseException('Failed to prepare statement for findById');
        }

        $stmt->bind_param('i', $bookId);

        if (!$stmt->execute()) {
            throw new DatabaseException('Failed to fetch book: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            return null;
        }

        return new Book(
            $row['book_id'],
            $row['title'],
            $row['author'],
            $row['year'],
            $row['genre']
        );
    }

    /**
     * Get all books from the database
     *
     * @return array Array of Book objects
     *
     * @throws DatabaseException If the database query fails
     */
    public function findAll(): array
    {
        $sql = 'SELECT * FROM books ORDER BY title ASC';
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new DatabaseException('Failed to prepare statement for findAll');
        }

        if (!$stmt->execute()) {
            throw new DatabaseException('Failed to fetch books: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $books = [];

        while ($row = $result->fetch_assoc()) {
            $books[] = new Book(
                $row['book_id'],
                $row['title'],
                $row['author'],
                $row['year'],
                $row['genre']
            );
        }

        return $books;
    }

    /**
     * Search for books by title or author
     *
     * @param string $keyword The search keyword
     *
     * @return array Array of Book objects matching the search
     *
     * @throws DatabaseException If the database query fails
     * @throws ValidationException If keyword is empty
     */
    public function search(string $keyword): array
    {
        if (empty(trim($keyword))) {
            throw new ValidationException('Search keyword cannot be empty');
        }

        $sql = 'SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY title ASC';
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new DatabaseException('Failed to prepare statement for search');
        }

        $searchTerm = '%' . $keyword . '%';
        $stmt->bind_param('ss', $searchTerm, $searchTerm);

        if (!$stmt->execute()) {
            throw new DatabaseException('Failed to search books: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $books = [];

        while ($row = $result->fetch_assoc()) {
            $books[] = new Book(
                $row['book_id'],
                $row['title'],
                $row['author'],
                $row['year'],
                $row['genre']
            );
        }

        return $books;
    }

    /**
     * Validate book data before saving
     *
     * @param Book $book The book to validate
     *
     * @return void
     *
     * @throws ValidationException If validation fails
     */
    private function validateBook(Book $book): void
    {
        if (empty($book->getTitle())) {
            throw new ValidationException('Book title cannot be empty');
        }

        if (empty($book->getAuthor())) {
            throw new ValidationException('Book author cannot be empty');
        }

        if ($book->getYear() < 1000 || $book->getYear() > (int) date('Y')) {
            throw new ValidationException('Invalid publication year: ' . $book->getYear());
        }

        if (empty($book->getGenre())) {
            throw new ValidationException('Book genre cannot be empty');
        }
    }
}