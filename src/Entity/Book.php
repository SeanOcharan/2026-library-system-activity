<?php

declare(strict_types=1);

namespace App\Library\Entity;

/**
 * Book Entity
 *
 * Represents a single book in the library system.
 * Stores book details like title, author, year, and genre.
 *
 * @author Library Developer
 * @since 2026-05-09
 */
class Book
{
    /**
     * @var int|null The unique book ID
     */
    private ?int $id;

    /**
     * @var string The book's title
     */
    private string $title;

    /**
     * @var string The author of the book
     */
    private string $author;

    /**
     * @var int The year the book was published
     */
    private int $year;

    /**
     * @var string The genre/category of the book
     */
    private string $genre;

    /**
     * Constructor
     *
     * Initializes a Book object with given values.
     *
     * @param int|null $id The book ID (null if new)
     * @param string $title The book title
     * @param string $author The author name
     * @param int $year The publication year
     * @param string $genre The genre
     */
    public function __construct(
        ?int $id,
        string $title,
        string $author,
        int $year,
        string $genre
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->genre = $genre;
    }

    /**
     * Get the book ID
     *
     * @return int|null The book ID or null if new record
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the book title
     *
     * @return string The book title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the book author
     *
     * @return string The author name
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Get the publication year
     *
     * @return int The publication year
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Get the book genre
     *
     * @return string The genre
     */
    public function getGenre(): string
    {
        return $this->genre;
    }
}