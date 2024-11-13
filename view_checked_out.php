<?php
session_start();
require 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

// Fetch books checked out by users, including image
$checked_out_query = "SELECT users.username, books.title, books.image, books.id FROM checked_out
                      JOIN users ON checked_out.user_id = users.user_id
                      JOIN books ON checked_out.book_id = books.id";
$checked_out_result = $conn->query($checked_out_query);

// Check for query error
if (!$checked_out_result) {
    die("Error fetching checked-out books: " . $conn->error);
}

// Handle book check-in
if (isset($_POST['checkin_book'])) {
    $book_id = $_POST['book_id'];

    // Begin transaction to handle check-in process
    $conn->begin_transaction();

    try {
        // Delete the record from checked_out table
        $delete_stmt = $conn->prepare("DELETE FROM checked_out WHERE book_id = ?");
        $delete_stmt->bind_param("i", $book_id);
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>View Checked-Out Books</title>
    <style>
        .table-responsive {
            margin-top: 20px;
        }
        .table th, .table td {
            text-align: center;
            padding: 10px;
        }
        .book-image {
            width: 50px;
            height: auto;
        }
        .button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .button:focus {
            outline: none;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <h1 class="text-center">View Checked-Out Books</h1>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>User</th>
                        <th>Book Title</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($checked_out_result->num_rows > 0): ?>
                        <?php while ($row = $checked_out_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Book Image" class="book-image">
                                    <?php else: ?>
                                        <p>No Image Available</p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="checkin_book" class="button">Check In</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No books are currently checked out.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>

</body>
</html>

