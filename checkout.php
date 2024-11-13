<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to check out a book.");
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    $checkout_date = date('Y-m-d'); // Get the current date

    // Update the book's checked_out_by and checkout_date columns
    $sql = "UPDATE books SET checked_out_by = ?, checkout_date = ? WHERE id = ? AND checked_out_by IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $user_id, $checkout_date, $book_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Book checked out successfully!";
        } else {
            echo "Book is already checked out.";
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
