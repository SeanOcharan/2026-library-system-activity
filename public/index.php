<?php

declare(strict_types=1);

namespace App\Library;

// Autoloader for PSR-4 compliant classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\Library\\';
    if (strncmp($class, $prefix, strlen($prefix)) === 0) {
        $relativeClass = substr($class, strlen($prefix));
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

use App\Library\DatabaseConnection;
use App\Library\Entity\Book;
use App\Library\Entity\BorrowRecord;
use App\Library\Entity\Student;
use App\Library\Repository\BookRepository;
use App\Library\Repository\BorrowRepository;
use App\Library\Service\LibraryService;
use App\Library\Config\LibraryConfig;
use App\Library\Exception\DatabaseException;
use App\Library\Exception\ValidationException;
use DateTime;

try {
    // Initialize database connection
    $connection = new DatabaseConnection(
        LibraryConfig::DB_HOST,
        LibraryConfig::DB_USERNAME,
        LibraryConfig::DB_PASSWORD,
        LibraryConfig::DB_NAME
    );

    // Initialize repositories
    $bookRepository = new BookRepository($connection);
    $borrowRepository = new BorrowRepository($connection);

    // Initialize services
    $libraryService = new LibraryService();

    // Get the action from GET parameters
    $action = $_GET['act'] ?? '';

    // Route the action
    switch ($action) {
        case 'list':
            // Display all books
            $books = $bookRepository->findAll();
            require '../src/View/book_list.php';
            break;

        case 'add':
            // Handle book addition
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $book = new Book(
                        null,
                        trim($_POST['title'] ?? ''),
                        trim($_POST['author'] ?? ''),
                        (int) ($_POST['year'] ?? 0),
                        trim($_POST['genre'] ?? '')
                    );

                    $bookId = $bookRepository->addBook($book);
                    echo '<div style="background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 3px;">';
                    echo 'Book added successfully. Book ID: ' . htmlspecialchars((string) $bookId);
                    echo '</div>';
                } catch (ValidationException $e) {
                    echo '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px;">';
                    echo 'Validation Error: ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                } catch (DatabaseException $e) {
                    echo '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px;">';
                    echo 'Database Error: ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
            }
            require '../src/View/book_form.php';
            break;

        case 'borrow':
            // Handle book borrowing
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $studentId = (int) ($_POST['student_id'] ?? 0);
                    $bookId = (int) ($_POST['book_id'] ?? 0);
                    $days = (int) ($_POST['days'] ?? LibraryConfig::DEFAULT_BORROW_DAYS);

                    $borrowDate = new DateTime();
                    $dueDate = $libraryService->calculateDueDate($borrowDate, $days);

                    $record = new BorrowRecord(
                        null,
                        $studentId,
                        $bookId,
                        $borrowDate,
                        $dueDate,
                        null,
                        LibraryConfig::STATUS_BORROWED,
                        0.0
                    );

                    $recordId = $borrowRepository->borrowBook($record);
                    echo '<div style="background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 3px;">';
                    echo 'Book borrowed successfully. Record ID: ' . htmlspecialchars((string) $recordId);
                    echo '</div>';
                } catch (ValidationException $e) {
                    echo '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px;">';
                    echo 'Validation Error: ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                } catch (DatabaseException $e) {
                    echo '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px;">';
                    echo 'Database Error: ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
            }
            require '../src/View/borrow_form.php';
            break;

        case 'return':
            // Handle book return
            $recordId = (int) ($_GET['record_id'] ?? 0);
            if ($recordId > 0) {
                try {
                    $record = $borrowRepository->findById($recordId);
                    if ($record) {
                        $returnDate = new DateTime();
                        $fine = $libraryService->calculateFine(
                            $record->getDueDate(),
                            $returnDate
                        );

                        $borrowRepository->returnBook($recordId, $returnDate);
                        $borrowRepository->updateFineAmount($recordId, $fine);

                        echo '<div style="background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 3px;">';
                        echo 'Book returned successfully. Fine: ₱' . htmlspecialchars(number_format($fine, 2));
                        echo '</div>';
                    } else {
                        echo '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px;">';
                        echo 'Record not found.';
                        echo '</div>';
                    }
                } catch (DatabaseException $e) {
                    echo '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px;">';
                    echo 'Database Error: ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
            }
            break;

        case 'report':
            // Generate library report
            try {
                $totalBooks = count($bookRepository->findAll());
                $overdueBooks = $borrowRepository->getOverdueBooks();
                $totalBorrowed = 0; // Would need to implement count method
                $totalReturned = 0; // Would need to implement count method
                $totalFines = 0.0; // Would need to implement sum method

                require '../src/View/report_view.php';
            } catch (DatabaseException $e) {
                echo '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px;">';
                echo 'Database Error: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
            break;

        default:
            // Display home page with menu
            echo '<div style="font-family: Arial, sans-serif; margin: 20px; max-width: 800px; margin: 0 auto;">';
            echo '<h1 style="color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px;">Student Library Management System</h1>';
            echo '<p style="color: #666; line-height: 1.6;">Welcome to the library management system. Select an action below:</p>';
            echo '<ul style="list-style: none; padding: 0;">';
            echo '<li style="margin: 10px 0;"><a href="?act=list" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; display: inline-block;">📚 View All Books</a></li>';
            echo '<li style="margin: 10px 0;"><a href="?act=add" style="background-color: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; display: inline-block;">➕ Add New Book</a></li>';
            echo '<li style="margin: 10px 0;"><a href="?act=borrow" style="background-color: #ffc107; color: #333; padding: 10px 15px; text-decoration: none; border-radius: 3px; display: inline-block;">🔖 Borrow a Book</a></li>';
            echo '<li style="margin: 10px 0;"><a href="?act=report" style="background-color: #17a2b8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; display: inline-block;">📊 View Report</a></li>';
            echo '</ul>';
            echo '</div>';
            break;
    }
} catch (DatabaseException $e) {
    die('<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; margin: 20px; border-radius: 3px;">'
        . '<strong>Database Connection Error:</strong> ' . htmlspecialchars($e->getMessage())
        . '</div>');
}

            break;
    }
} catch (Exception $exception) {

    echo 'Error: ' . htmlspecialchars($exception->getMessage());
}