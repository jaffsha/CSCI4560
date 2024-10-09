<?php
include 'db_connect.php';

// Define an array of books to insert
$books = [
    ['title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'copies_available' => 5],
    ['title' => '1984', 'author' => 'George Orwell', 'copies_available' => 3],
    ['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'copies_available' => 4],
    ['title' => 'Moby Dick', 'author' => 'Herman Melville', 'copies_available' => 2],
    ['title' => 'The Catcher in the Rye', 'author' => 'J.D. Salinger', 'copies_available' => 6],
];

// Prepare the SQL statement
$sql = "INSERT INTO books (title, author, copies_available) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

// Loop through the array and execute the statement for each book
foreach ($books as $book) {
    $stmt->bind_param("ssi", $book['title'], $book['author'], $book['copies_available']);
    $stmt->execute();
}

echo "Books added successfully!";
$stmt->close();
$conn->close();
?>
