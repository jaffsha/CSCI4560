<?php
session_start();
require 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

// Handle adding a book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $copies_available = $_POST['copies_available'];

    $stmt = $conn->prepare("INSERT INTO books (title, author, copies_available) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $author, $copies_available);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Book added successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Add Book</title>
</head>
<body>
    <h1>Add Book</h1>
    <form action="add_book.php" method="POST">
        <input type="text" name="title" placeholder="Title" required>
        <input type="text" name="author" placeholder="Author" required>
        <input type="number" name="copies_available" placeholder="Copies Available" required min="1">
        <button type="submit" name="add_book">Add Book</button>
    </form>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
