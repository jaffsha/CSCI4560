<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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

// Fetch available books with additional details
$books_query = "SELECT id, title, author, copies_available, description, image, length, genre FROM books WHERE copies_available > 0";
$books_result = $conn->query($books_query);


// Fetch checked-out books for the user including image
$checked_out_query = "SELECT books.id, books.title, books.author, books.image FROM checked_out
                      JOIN books ON checked_out.book_id = books.id
                      WHERE checked_out.user_id = ?";
$checked_out_stmt = $conn->prepare($checked_out_query);
$checked_out_stmt->bind_param("i", $_SESSION['user_id']);
$checked_out_stmt->execute();
$checked_out_result = $checked_out_stmt->get_result();



// Handle book checkout
if (isset($_POST['checkout_book'])) {


    if (!isset($_SESSION['user_id']) || empty($_POST['book_id'])) {
        die('Invalid input or session.');
    }

      // Insert into checked_out table
      $book_id = (int)$_POST['book_id'];
      $user_id = (int)$_SESSION['user_id'];



    try {

          // Begin transaction to handle checkout process
        $conn->begin_transaction();
        $line = "INSERT INTO checked_out (user_id, book_id) VALUES (?, ?)";
        $stmt = $conn->prepare($line);
        $stmt->bind_param('ii', $user_id, $book_id);
        $stmt->execute();


        // Decrease the copies available in the books table
        $update_stmt = $conn->prepare("UPDATE books SET copies_available = copies_available - 1 WHERE id = ?");
        $update_stmt->bind_param("i", $book_id);
        $update_stmt->execute();

        // Commit transaction
        $conn->commit();

        echo "<script>alert('Book checked out successfully!');</script>";

        
    } catch (Exception $e) {

        // Rollback transaction on error
        $conn->rollback();
        echo "<script>console.log('Message " . $e->getMessage() . "')</script>";
        echo "<script>alert('Error checking out book. Please try again later.');</script>";

    } finally{
        if (isset($stmt)) $stmt->close();
        if (isset($update_stmt)) $update_stmt->close();
    }
}

// Handle book check-in
if (isset($_POST['checkin_book'])) {
    $book_id = $_POST['book_id'];

    // Begin transaction to handle check-in process
    $conn->begin_transaction();

    try {
        // Delete the record from checked_out table
        $delete_stmt = $conn->prepare("DELETE FROM checked_out WHERE user_id = ? AND book_id = ?");
        $delete_stmt->bind_param("ii", $_SESSION['user_id'], $book_id);
        $delete_stmt->execute();

        // Increase the copies available in the books table
        $update_stmt = $conn->prepare("UPDATE books SET copies_available = copies_available + 1 WHERE id = ?");
        $update_stmt->bind_param("i", $book_id);
        $update_stmt->execute();

        // Commit transaction
        $conn->commit();

        echo "<script>alert('Book checked in successfully!');</script>";

        $delete_stmt->close();
        $update_stmt->close();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<script>alert('Error checking in book. Please try again later.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css">

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
                        <th>Description</th>
                        <th>Length</th>
                        <th>Genre</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($book = $books_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['copies_available']); ?></td>
                            <td><?php echo htmlspecialchars($book['description']); ?></td>
                            <td><?php echo htmlspecialchars($book['length']); ?></td>
                            <td><?php echo htmlspecialchars($book['genre']); ?></td>
                            <td>
                                <?php if (!empty($book['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="Book Image">
                                <?php else: ?>
                                    <p>No Image Available</p>
                                <?php endif; ?>
                            </td>
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
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($book = $checked_out_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td>
                                <?php if (!empty($book['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="Book Image">
                                <?php else: ?>
                                    <p>No Image Available</p>
                                <?php endif; ?>
                            </td>
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

