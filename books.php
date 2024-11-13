<?php
session_start();
include 'db_connect.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: index.php"); // Redirect non-admins
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $copies_available = $_POST['copies_available'];
    $description = $_POST['description'];
    $length = $_POST['length'];
    $genre = $_POST['genre'];
    $image = $_POST['image'];

    // Insert book into the database
    $stmt = $conn->prepare("INSERT INTO books (title, author, copies_available, description, length, genre, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissis", $title, $author, $copies_available, $description, $length, $genre, $image);

    if ($stmt->execute()) {
        echo "Book added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
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
    <h1>Add a New Book</h1>
    <form action="books.php" method="POST">
        <input type="text" name="title" placeholder="Title" required>
        <input type="text" name="author" placeholder="Author" required>
        <input type="number" name="copies_available" placeholder="Copies Available" required min="1">
        <input type="text" name="description" placeholder="Description (optional)">
        <input type="number" name="length" placeholder="Length (pages, optional)">
        <input type="text" name="genre" placeholder="Genre (optional)">
        <input type="text" name="image" placeholder="Image URL (optional)">
        <button type="submit">Add Book</button>
    </form>
    <a href="admin_dashboard.php">Back to Admin Dashboard</a>
</body>
</html>
