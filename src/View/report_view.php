<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Report</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            text-align: center;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin: 30px 0;
        }

        .stat-card {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
        }

        .stat-card h3 {
            color: #666;
            margin: 0 0 10px 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f0f0f0;
        }

        .summary-text {
            margin-top: 20px;
            padding: 15px;
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            border-radius: 3px;
            color: #333;
        }

        @media print {
            body {
                background-color: white;
            }
            .container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Library Management Report</h1>

        <div class="stats">
            <div class="stat-card">
                <h3>Total Books</h3>
                <div class="stat-value"><?= htmlspecialchars((string) ($totalBooks ?? 0)) ?></div>
            </div>

            <div class="stat-card">
                <h3>Currently Borrowed</h3>
                <div class="stat-value"><?= htmlspecialchars((string) ($totalBorrowed ?? 0)) ?></div>
            </div>

            <div class="stat-card">
                <h3>Returned</h3>
                <div class="stat-value"><?= htmlspecialchars((string) ($totalReturned ?? 0)) ?></div>
            </div>

            <div class="stat-card">
                <h3>Total Fines</h3>
                <div class="stat-value">₱<?= htmlspecialchars(number_format((float) ($totalFines ?? 0), 2)) ?></div>
            </div>
        </div>

        <?php if (!empty($overdueBooks)): ?>
            <h2>Overdue Books</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Book Title</th>
                        <th>Due Date</th>
                        <th>Days Overdue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($overdueBooks as $book): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $book['student_id']) ?></td>
                            <td><?= htmlspecialchars($book['name']) ?></td>
                            <td><?= htmlspecialchars($book['title']) ?></td>
                            <td><?= htmlspecialchars($book['due_date']) ?></td>
                            <td><?= htmlspecialchars((string) $book['days_overdue']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="summary-text">
                <strong>✓ Good News:</strong> There are no overdue books at the moment.
            </div>
        <?php endif; ?>

        <div class="summary-text">
            <strong>Report Generated:</strong> <?= htmlspecialchars(date('Y-m-d H:i:s')) ?>
        </div>
    </div>

</body>
</html>