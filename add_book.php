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
    $copies_available = 0;
    $copies_available = (int)$_POST['copies_available'];
    $description = "";
    $description = $_POST['description'];
    $length = 0;
    $length = $_POST['length'];
    $genre = $_POST['genre'];
    $image = $_POST['image'];

    // Prepare and execute the statement to insert data into the books table
    $stmt = $conn->prepare("INSERT INTO books (title, author, copies_available, description, length, genre, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissis", $title, $author, $copies_available, $description, $length, $genre, $image);
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
    <style>.btn {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 20px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        button:focus {
            outline: none;
            box-shadow: none;
        }

        button{
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 20px
        }
        button:hover {
            background-color: #0056b3;
        }

        input[type = text]{

            width: 60%;
            margin-left: 20px;
        }
        
        input[type = number]{
            width: 60%;
            margin-left: 20px;
        }

        textarea{
            width: 60%;
            margin-left: 20px;
            border: 2px solid #007bff;
            border-radius: 2px;
            font-family: Arial, sans-serif;
            font-size: 16px;
        }
     
       
</style>
    
    <title>Add Book</title>
</head>
<body>
    <h1>Add Book</h1>
    <form action="add_book.php" method="POST">
        <input type="text" name="title" placeholder="Title" required>
        <input type="text" name="author" placeholder="Author" required>
        <input type="number" name="copies_available" placeholder="Copies Available" required min="1">
        <textarea name="description" placeholder="Description" rows="4"></textarea>
        <input type="number" name="length" placeholder="Length (pages)" min="1">
        <input type="text" name="genre" placeholder="Genre">
        <input type="text" name="image" placeholder="Image URL">
        <br>
        <button type="submit" name="add_book">Add Book</button>
    </form>
    <br>
    <a href="admin_dashboard.php" class="btn btn-primary" style = "text-decoration:none">Back to Dashboard</a>
</body>
</html>
