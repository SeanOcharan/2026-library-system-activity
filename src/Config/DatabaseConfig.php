<?php

declare(strict_types=1);

namespace App\Library;

use mysqli;
use mysqli_stmt;
use App\Library\Exception\DatabaseException;

/**
 * Database Connection
 *
 * Manages the database connection and provides methods for preparing
 * and executing SQL statements. Uses MySQLi for database access with
 * support for prepared statements to prevent SQL injection.
 *
 * @author Library Developer
 * @since 2026-05-09
 */
class DatabaseConnection
{
    /**
     * @var mysqli The MySQLi connection object
     */
    private mysqli $connection;

    /**
     * Constructor
     *
     * Creates a new database connection using the given credentials.
     * Throws an exception if connection fails.
     *
     * @param string $host The database host
     * @param string $user The database username
     * @param string $password The database password
     * @param string $database The database name
     *
     * @throws DatabaseException If connection fails
     */
    public function __construct(
        string $host,
        string $user,
        string $password,
        string $database
    ) {
        $this->connection = new mysqli($host, $user, $password, $database);

        if ($this->connection->connect_error) {
            throw new DatabaseException(
                'Database connection failed: ' . $this->connection->connect_error
            );
        }
    }

    /**
     * Prepare an SQL statement
     *
     * Used to prevent SQL injection and improve security.
     *
     * @param string $sql The SQL statement to prepare
     *
     * @return mysqli_stmt The prepared statement object
     *
     * @throws DatabaseException If preparation fails
     */
    public function prepare(string $sql): mysqli_stmt
    {
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new DatabaseException(
                'Failed to prepare statement: ' . $this->connection->error
            );
        }

        return $stmt;
    }

    /**
     * Get the ID of the last inserted record
     *
     * Useful after INSERT queries.
     *
     * @return int The last inserted ID
     */
    public function getInsertId(): int
    {
        return $this->connection->insert_id;
    }

    /**
     * Get the raw MySQLi connection object
     *
     * Useful if advanced database operations are needed.
     *
     * @return mysqli The MySQLi connection
     */
    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    /**
     * Close the database connection
     *
     * @return void
     */
    public function close(): void
    {
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }
}