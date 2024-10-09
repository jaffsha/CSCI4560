<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Initialize variables
$show_available_books = true;
$show_checked_out_books = false;

// Handle button clicks
if (isset($_POST['show_available_books'])) {
    $show_available_books = true;
    $show_checked_out_books = false;
} elseif (isset($_POST['show_checked_out_books'])) {
    $show_available_books = false;
    $show_checked_out_books = true;
}

// Fetch available books
$books_query = "SELECT id, title, author, copies_available FROM books WHERE copies_available > 0";
$books_result = $conn->query($books_query);

// Fetch checked-out books for the user
$checked_out_query = "SELECT books.id, books.title, books.author FROM checked_out
                      JOIN books ON checked_out.book_id = books.id
                      WHERE checked_out.user_id = ?";
$checked_out_stmt = $conn->prepare($checked_out_query);
$checked_out_stmt->bind_param("i", $_SESSION['user_id']);
$checked_out_stmt->execute();
$checked_out_result = $checked_out_stmt->get_result();

// Handle book checkout
if (isset($_POST['checkout_book'])) {
    $book_id = $_POST['book_id'];

    // Update the checked_out table and decrease the book copies
    $stmt = $conn->prepare("INSERT INTO checked_out (user_id, book_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $_SESSION['user_id'], $book_id);
    $stmt->execute();
    
    // Decrease the copies available in the books table
    $update_stmt = $conn->prepare("UPDATE books SET copies_available = copies_available - 1 WHERE id = ?");
    $update_stmt->bind_param("i", $book_id);
    $update_stmt->execute();

    echo "<script>alert('Book checked out successfully!');</script>";
    $update_stmt->close();
    $stmt->close();
}

// Handle book check-in
if (isset($_POST['checkin_book'])) {
    $book_id = $_POST['book_id'];

    // Delete the record from checked_out table
    $delete_stmt = $conn->prepare("DELETE FROM checked_out WHERE user_id = ? AND book_id = ?");
    $delete_stmt->bind_param("ii", $_SESSION['user_id'], $book_id);
    $delete_stmt->execute();

    // Increase the copies available in the books table
    $update_stmt = $conn->prepare("UPDATE books SET copies_available = copies_available + 1 WHERE id = ?");
    $update_stmt->bind_param("i", $book_id);
    $update_stmt->execute();

    echo "<script>alert('Book checked in successfully!');</script>";
    $delete_stmt->close();
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>User Dashboard</title>
    <style>
    /* Reset form styles */
    form {
        margin: 0; /* Remove default margin */
        padding: 0; /* Remove default padding */
        border: none; /* Remove default border */
        background: none; /* Remove default background */
        box-shadow: none; /* Remove any box shadow */
    }

    .button {
        background-color: #007bff; /* Primary color */
        color: white;
        padding: 10px 20px;
        border: none; /* Remove default border */
        border-radius: 5px; /* Rounded corners */
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        display: inline-block; /* Ensure inline-block for buttons */
        transition: background-color 0.3s; /* Smooth transition for hover */
        outline: none; /* Remove outline when focused */
        box-shadow: none; /* Remove any box shadow */
        margin: 0; /* Ensure no margin */
        width: auto; /* Allow width to be auto */
        background-clip: padding-box; /* Background covers only padding */
    }

    .button:hover {
        background-color: #0056b3; /* Darken button on hover */
    }

    /* Remove default browser button styles */
    button {
        appearance: none; /* Remove default button styling */
        -webkit-appearance: none; /* Remove default button styling in Safari */
        -moz-appearance: none; /* Remove default button styling in Firefox */
        padding: 0; /* Reset padding */
        margin: 0; /* Reset margin */
        border: none; /* Reset border */
        background: none; /* Reset background */
        color: inherit; /* Inherit color */
        font: inherit; /* Inherit font settings */
    }
</style>


</head>
<body>
    <header>
        <nav>
            <ul class="navbar">
                <li><a href="home.php">Home</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>User Dashboard</h1>

        <form action="user_dashboard.php" method="POST">
            <button type="submit" name="show_available_books" class="button">Show Available Books</button>
            <button type="submit" name="show_checked_out_books" class="button">Show Checked-Out Books</button>
        </form>

        <?php if ($show_available_books): ?>
            <h2>Available Books</h2>
            <?php if ($books_result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Copies Available</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($book = $books_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['copies_available']); ?></td>
                            <td>
                                <form action="user_dashboard.php" method="POST">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" name="checkout_book" class="button">Check Out</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No books available for checkout.</p>
            <?php endif; ?>

        <?php elseif ($show_checked_out_books): ?>
            <h2>Your Checked-Out Books</h2>
            <?php if ($checked_out_result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($book = $checked_out_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td>
                                <form action="user_dashboard.php" method="POST">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" name="checkin_book" class="button">Check In</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>You have no checked-out books.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
