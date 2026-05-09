<?php

declare(strict_types=1);

namespace App\Library\Repository;

use App\Library\Entity\BorrowRecord;
use App\Library\DatabaseConnection;
use App\Library\Exception\DatabaseException;
use App\Library\Exception\ValidationException;
use DateTime;

/**
 * Borrow Repository
 *
 * Manages all database operations for BorrowRecord entities.
 * Handles borrowing, returning books, and retrieving borrow records.
 *
 * @author Library Developer
 * @since 2026-05-09
 */
class BorrowRepository
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
     * Create a new borrow record
     *
     * @param BorrowRecord $record The borrow record to persist
     *
     * @return int The auto-generated record ID
     *
     * @throws DatabaseException If the database query fails
     * @throws ValidationException If the record data is invalid
     */
    public function borrowBook(BorrowRecord $record): int
    {
        $this->validateBorrowRecord($record);

        $sql = 'INSERT INTO borrow_records (student_id, book_id, borrow_date, due_date, status) '
             . 'VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new DatabaseException('Failed to prepare statement for borrowBook');
        }

        $borrowDate = $record->getBorrowDate()->format('Y-m-d');
        $dueDate = $record->getDueDate()->format('Y-m-d');
        $status = 'borrowed';

        $stmt->bind_param(
            'iisss',
            $record->getStudentId(),
            $record->getBookId(),
            $borrowDate,
            $dueDate,
            $status
        );

        if (!$stmt->execute()) {
            throw new DatabaseException('Failed to create borrow record: ' . $stmt->error);
        }

        return $this->connection->getInsertId();
    }

    /**
     * Find a borrow record by ID
     *
     * @param int $recordId The record ID to search for
     *
     * @return BorrowRecord|null The BorrowRecord if found, null otherwise
     *
     * @throws DatabaseException If the database query fails
     */
    public function findById(int $recordId): ?BorrowRecord
    {
        if ($recordId <= 0) {
            throw new ValidationException('Record ID must be greater than 0');
        }

        $sql = 'SELECT * FROM borrow_records WHERE record_id = ?';
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new DatabaseException('Failed to prepare statement for findById');
        }

        $stmt->bind_param('i', $recordId);

        if (!$stmt->execute()) {
            throw new DatabaseException('Failed to fetch record: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            return null;
        }

        return $this->createBorrowRecordFromRow($row);
    }

    /**
     * Return a borrowed book and record the return date
     *
     * @param int $recordId The borrow record ID
     * @param DateTime $returnDate The date the book was returned
     *
     * @return void
     *
     * @throws DatabaseException If the database query fails
     * @throws ValidationException If record is invalid
     */
    public function returnBook(int $recordId, DateTime $returnDate): void
    {
        if ($recordId <= 0) {
            throw new ValidationException('Record ID must be greater than 0');
        }

        $sql = 'UPDATE borrow_records SET return_date = ?, status = ? WHERE record_id = ?';
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new DatabaseException('Failed to prepare statement for returnBook');
        }

        $returnDateStr = $returnDate->format('Y-m-d');
        $status = 'returned';

        $stmt->bind_param('ssi', $returnDateStr, $status, $recordId);

        if (!$stmt->execute()) {
            throw new DatabaseException('Failed to return book: ' . $stmt->error);
        }
    }

    /**
     * Get all overdue books
     *
     * @return array Array of overdue borrow records
     *
     * @throws DatabaseException If the database query fails
     */
    public function getOverdueBooks(): array
    {
        $today = date('Y-m-d');

        $sql = 'SELECT br.*, b.title, s.name '
             . 'FROM borrow_records br '
             . 'JOIN books b ON br.book_id = b.book_id '
             . 'JOIN students s ON br.student_id = s.student_id '
             . 'WHERE br.due_date < ? AND br.status = ?';

        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new DatabaseException('Failed to prepare statement for getOverdueBooks');
        }

        $status = 'borrowed';
        $stmt->bind_param('ss', $today, $status);

        if (!$stmt->execute()) {
            throw new DatabaseException('Failed to fetch overdue books: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $overdue = [];

        while ($row = $result->fetch_assoc()) {
            $overdue[] = $row;
        }

        return $overdue;
    }

    /**
     * Update fine amount for a borrow record
     *
     * @param int $recordId The record ID
     * @param float $fineAmount The fine amount to set
     *
     * @return void
     *
     * @throws DatabaseException If the database query fails
     */
    public function updateFineAmount(int $recordId, float $fineAmount): void
    {
        if ($recordId <= 0) {
            throw new ValidationException('Record ID must be greater than 0');
        }

        if ($fineAmount < 0) {
            throw new ValidationException('Fine amount cannot be negative');
        }

        $sql = 'UPDATE borrow_records SET fine_amount = ? WHERE record_id = ?';
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new DatabaseException('Failed to prepare statement for updateFineAmount');
        }

        $stmt->bind_param('di', $fineAmount, $recordId);

        if (!$stmt->execute()) {
            throw new DatabaseException('Failed to update fine amount: ' . $stmt->error);
        }
    }

    /**
     * Validate borrow record data before saving
     *
     * @param BorrowRecord $record The record to validate
     *
     * @return void
     *
     * @throws ValidationException If validation fails
     */
    private function validateBorrowRecord(BorrowRecord $record): void
    {
        if ($record->getStudentId() <= 0) {
            throw new ValidationException('Invalid student ID');
        }

        if ($record->getBookId() <= 0) {
            throw new ValidationException('Invalid book ID');
        }

        if ($record->getBorrowDate() > $record->getDueDate()) {
            throw new ValidationException('Borrow date cannot be after due date');
        }
    }

    /**
     * Create a BorrowRecord object from a database row
     *
     * @param array $row The database row
     *
     * @return BorrowRecord The created BorrowRecord object
     */
    private function createBorrowRecordFromRow(array $row): BorrowRecord
    {
        $borrowDate = new DateTime($row['borrow_date']);
        $dueDate = new DateTime($row['due_date']);
        $returnDate = $row['return_date'] ? new DateTime($row['return_date']) : null;

        return new BorrowRecord(
            $row['record_id'],
            $row['student_id'],
            $row['book_id'],
            $borrowDate,
            $dueDate,
            $returnDate,
            $row['status'],
            (float) $row['fine_amount']
        );
    }
}