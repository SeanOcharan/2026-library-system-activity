<?php

declare(strict_types=1);

namespace App\Library\Entity;

use DateTime;

/**
 * Borrow Record Entity
 *
 * Represents a book borrow transaction in the library system.
 * Tracks when a book was borrowed, when it's due, when it was returned,
 * and any fines incurred.
 *
 * @author Library Developer
 * @since 2026-05-09
 */
class BorrowRecord
{
    /**
     * @var int|null The unique record ID
     */
    private ?int $id;

    /**
     * @var int The ID of the student who borrowed the book
     */
    private int $studentId;

    /**
     * @var int The ID of the borrowed book
     */
    private int $bookId;

    /**
     * @var DateTime The date the book was borrowed
     */
    private DateTime $borrowDate;

    /**
     * @var DateTime The date the book is due to be returned
     */
    private DateTime $dueDate;

    /**
     * @var DateTime|null The date the book was actually returned
     */
    private ?DateTime $returnDate;

    /**
     * @var string The status of the borrow (borrowed/returned)
     */
    private string $status;

    /**
     * @var float Any fine amount incurred for late return
     */
    private float $fineAmount;

    /**
     * Constructor
     *
     * @param int|null $id The record ID (null if new record)
     * @param int $studentId The student ID
     * @param int $bookId The book ID
     * @param DateTime $borrowDate The borrow date
     * @param DateTime $dueDate The due date
     * @param DateTime|null $returnDate The return date (null if not yet returned)
     * @param string $status The borrow status
     * @param float $fineAmount The fine amount (default 0)
     */
    public function __construct(
        ?int $id,
        int $studentId,
        int $bookId,
        DateTime $borrowDate,
        DateTime $dueDate,
        ?DateTime $returnDate = null,
        string $status = 'borrowed',
        float $fineAmount = 0.0
    ) {
        $this->id = $id;
        $this->studentId = $studentId;
        $this->bookId = $bookId;
        $this->borrowDate = $borrowDate;
        $this->dueDate = $dueDate;
        $this->returnDate = $returnDate;
        $this->status = $status;
        $this->fineAmount = $fineAmount;
    }

    /**
     * Get the record ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the student ID
     *
     * @return int
     */
    public function getStudentId(): int
    {
        return $this->studentId;
    }

    /**
     * Get the book ID
     *
     * @return int
     */
    public function getBookId(): int
    {
        return $this->bookId;
    }

    /**
     * Get the borrow date
     *
     * @return DateTime
     */
    public function getBorrowDate(): DateTime
    {
        return $this->borrowDate;
    }

    /**
     * Get the due date
     *
     * @return DateTime
     */
    public function getDueDate(): DateTime
    {
        return $this->dueDate;
    }

    /**
     * Get the return date
     *
     * @return DateTime|null
     */
    public function getReturnDate(): ?DateTime
    {
        return $this->returnDate;
    }

    /**
     * Set the return date
     *
     * @param DateTime $returnDate The return date
     *
     * @return void
     */
    public function setReturnDate(DateTime $returnDate): void
    {
        $this->returnDate = $returnDate;
    }

    /**
     * Get the status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set the status
     *
     * @param string $status The new status
     *
     * @return void
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the fine amount
     *
     * @return float
     */
    public function getFineAmount(): float
    {
        return $this->fineAmount;
    }

    /**
     * Set the fine amount
     *
     * @param float $fineAmount The fine amount
     *
     * @return void
     */
    public function setFineAmount(float $fineAmount): void
    {
        $this->fineAmount = $fineAmount;
    }
}