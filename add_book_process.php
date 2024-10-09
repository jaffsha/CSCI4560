<?php
session_start();
include 'db_connect.php';

// Check if user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: index.php");
    exit(); // Redirect non-admins to home
}

// Handle book addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];

    $stmt = $conn->prepare("INSERT INTO books (title, author, isbn) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $author, $isbn);

    if ($stmt->execute()) {
        echo "Book added successfully!";
    } else {
        echo "Error adding book: " . $stmt->error;
    }

    $stmt->close();
}
?>

<a href="admin_dashboard.php">Back to Admin Dashboard</a>
