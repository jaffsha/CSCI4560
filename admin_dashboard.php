<?php
session_start();
require 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

// Fetch all books
$books_query = "SELECT id, title, author, copies_available FROM books";
$books_result = $conn->query($books_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            flex: 1;
            margin: 0 10px;
        }

        .form-container {
            margin: 20px 0;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .inventory {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>Admin Dashboard</h1>

    <div class="button-container">
        <a href="add_book.php" class="button">Add Book</a>
        <a href="view_users.php" class="button">View Users</a>
        <a href="view_checked_out.php" class="button">View Checked-Out Books</a>
        <a href="logout.php" class="button logout">Logout</a>
    </div>

    <div class="form-container">
        <h2>Book Inventory</h2>
        <?php if ($books_result->num_rows > 0): ?>
            <ul>
                <?php while ($row = $books_result->fetch_assoc()): ?>
                    <li><?php echo htmlspecialchars($row['title']) . ' by ' . htmlspecialchars($row['author']) . ' (Available: ' . htmlspecialchars($row['copies_available']) . ')'; ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No books available.</p>
        <?php endif; ?>
    </div>

</body>
</html>

