<?php

declare(strict_types=1);

namespace App\Library\Entity;

/**
 * Represents a student entity in the library system.
 *
 * Stores and manages student information such as
 * student ID, full name, course, and year level.
 *
 * @author Library Developer
 * @since 2026-05-09
 */
class Student
{
    /**
     * @var int The student's unique identifier
     */
    private int $studentId;

    /**
     * @var string The student's full name
     */
    private string $name;

    /**
     * @var string The student's course/program
     */
    private string $course;

    /**
     * @var int The student's year level (1-4)
     */
    private int $yearLevel;


    public function __construct (
        int $studentId,
        string $name,
        string $course,
        int $yearLevel
    ) {
        $this->studentId = $studentId;
        $this->name = $name;
        $this->course = $course;
        $this->yearLevel = $yearLevel;
    }

    /**
     * Get the student's ID
     *
     * @return int The student ID
     */
    public function getStudentId(): int
    {
        return $this->studentId;
    }

    /**
     * Get the student's name
     *
     * @return string The student's full name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the student's name
     *
     * @param string $name The new name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the student's course
     *
     * @return string The course name
     */
    public function getCourse(): string
    {
        return $this->course;
    }

    /**
     * Set the student's course
     *
     * @param string $course The course name
     *
     * @return void
     */
    public function setCourse(string $course): void
    {
        $this->course = $course;
    }

    /**
     * Get the student's year level
     *
     * @return int The year level
     */
    public function getYearLevel(): int
    {
        return $this->yearLevel;
    }

    /**
     * Set the student's year level
     *
     * @param int $yearLevel The year level (1-4)
     *
     * @return void
     */
    public function setYearLevel(int $yearLevel): void
    {
        $this->yearLevel = $yearLevel;
    }
