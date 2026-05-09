<?php

declare(strict_types=1);

namespace App\Library\Service;

use DateTime;
use DateInterval;
use App\Library\Config\LibraryConfig;

/**
 * Library Service
 *
 * Contains business logic for the library system including
 * date calculations, fine calculations, and other library rules.
 *
 * @author Library Developer
 * @since 2026-05-09
 */
class LibraryService
{
    /**
     * Calculate the due date for a borrowed book
     *
     * @param DateTime $borrowDate The date the book was borrowed
     * @param int $daysAllowed The number of days allowed for borrowing (optional)
     *
     * @return DateTime The calculated due date
     */
    public function calculateDueDate(
        DateTime $borrowDate,
        int $daysAllowed = LibraryConfig::DEFAULT_BORROW_DAYS
    ): DateTime {
        $dueDate = clone $borrowDate;
        $dueDate->add(new DateInterval('P' . $daysAllowed . 'D'));
        return $dueDate;
    }

    /**
     * Calculate the fine amount for an overdue book
     *
     * If the return date is before or equal to the due date, no fine is charged.
     * Otherwise, a fine is calculated for each day overdue multiplied by the daily fine rate.
     *
     * @param DateTime $dueDate The date the book was due
     * @param DateTime $returnDate The date the book was returned
     * @param float $dailyRate The daily fine rate (optional, uses default from config)
     *
     * @return float The calculated fine amount
     */
    public function calculateFine(
        DateTime $dueDate,
        DateTime $returnDate,
        float $dailyRate = LibraryConfig::DAILY_FINE_RATE
    ): float {
        // If returned on time or early, no fine
        if ($returnDate <= $dueDate) {
            return 0.0;
        }

        // Calculate days overdue
        $interval = $dueDate->diff($returnDate);
        $daysOverdue = (int) $interval->format('%r%a');

        // Ensure we don't count partial days as zero
        if ($daysOverdue < 1) {
            $daysOverdue = 1;
        }

        return (float) ($daysOverdue * $dailyRate);
    }

    /**
     * Check if a book is overdue
     *
     * @param DateTime $dueDate The date the book was due
     * @param DateTime|null $today The current date (default: today)
     *
     * @return bool True if overdue, false otherwise
     */
    public function isOverdue(DateTime $dueDate, ?DateTime $today = null): bool
    {
        if ($today === null) {
            $today = new DateTime();
        }

        return $today > $dueDate;
    }

    /**
     * Calculate days remaining until due date
     *
     * Returns negative number if overdue.
     *
     * @param DateTime $dueDate The due date
     * @param DateTime|null $today The current date (default: today)
     *
     * @return int Days remaining (negative if overdue)
     */
    public function daysUntilDue(DateTime $dueDate, ?DateTime $today = null): int
    {
        if ($today === null) {
            $today = new DateTime();
        }

        $interval = $today->diff($dueDate);

        if ($today > $dueDate) {
            return -((int) $interval->format('%a'));
        }

        return (int) $interval->format('%a');
    }

    /**
     * Get the status message for a borrow record
     *
     * @param bool $isReturned Whether the book has been returned
     * @param DateTime $dueDate The due date
     * @param float $fineAmount The fine amount
     *
     * @return string A human-readable status message
     */
    public function getStatusMessage(
        bool $isReturned,
        DateTime $dueDate,
        float $fineAmount = 0.0
    ): string {
        if ($isReturned) {
            if ($fineAmount > 0) {
                return sprintf('Returned with fine: PHP %.2f', $fineAmount);
            }
            return 'Returned on time';
        }

        if ($this->isOverdue($dueDate)) {
            $daysOverdue = abs($this->daysUntilDue($dueDate));
            return sprintf('Overdue by %d day%s', $daysOverdue, $daysOverdue !== 1 ? 's' : '');
        }

        $daysLeft = $this->daysUntilDue($dueDate);
        return sprintf('Due in %d day%s', $daysLeft, $daysLeft !== 1 ? 's' : '');
    }
}