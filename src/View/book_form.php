<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        textarea {
            resize: none;
            min-height: 80px;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 3px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        .required::after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Add New Book</h1>

        <form action="?act=add" method="POST">

            <div class="form-group">
                <label for="title" class="required">Book Title</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    required
                    placeholder="Enter book title"
                    maxlength="255"
                >
            </div>

            <div class="form-group">
                <label for="author" class="required">Author</label>
                <input
                    type="text"
                    id="author"
                    name="author"
                    required
                    placeholder="Enter author name"
                    maxlength="255"
                >
            </div>

            <div class="form-group">
                <label for="year" class="required">Publication Year</label>
                <input
                    type="number"
                    id="year"
                    name="year"
                    required
                    min="1000"
                    max="2099"
                    placeholder="2026"
                >
            </div>

            <div class="form-group">
                <label for="genre" class="required">Genre</label>
                <input
                    type="text"
                    id="genre"
                    name="genre"
                    required
                    placeholder="Enter genre (e.g., Fiction, Non-fiction, Science, History)"
                    maxlength="100"
                >
            </div>

            <button type="submit">Add Book</button>

        </form>
    </div>

</body>
</html>
