<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to check out a book.");
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Update the book's checked_out_by column
    $sql = "UPDATE books SET checked_out_by = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $book_id);

    if ($stmt->execute()) {
        echo "Book checked out successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
