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
    $year = $_POST['year'];

    // Insert book into the database
    $stmt = $conn->prepare("INSERT INTO books (title, author, published_year) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $author, $year);

    if ($stmt->execute()) {
        echo "Book added successfully.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    $stmt->close();
}

$conn->close();
?>
