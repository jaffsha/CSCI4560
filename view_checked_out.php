<?php
session_start();
require 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

// Fetch books checked out by users
$checked_out_query = "SELECT users.username, books.title FROM checked_out
                      JOIN users ON checked_out.user_id = users.user_id
                      JOIN books ON checked_out.book_id = books.id";
$checked_out_result = $conn->query($checked_out_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>View Checked-Out Books</title>
</head>
<body>
    <h1>View Checked-Out Books</h1>
    <?php if ($checked_out_result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $checked_out_result->fetch_assoc()): ?>
                <li><?php echo htmlspecialchars($row['username']) . ' checked out "' . htmlspecialchars($row['title']) . '"'; ?></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No books are currently checked out.</p>
    <?php endif; ?>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
