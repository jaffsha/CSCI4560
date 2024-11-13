<?php
session_start();
require 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

// Handle user deletion and promotion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];

        // Step 1: Check if the user has any checked-out books
        $checked_out_query = "SELECT book_id FROM checked_out WHERE user_id = ?";
        $stmt = $conn->prepare($checked_out_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Step 2: Check in all books (delete from checked_out and increment book count)
            while ($row = $result->fetch_assoc()) {
                $book_id = $row['book_id'];

                // Check in the book by deleting it from the checked_out table
                $delete_checked_out = $conn->prepare("DELETE FROM checked_out WHERE user_id = ? AND book_id = ?");
                $delete_checked_out->bind_param("ii", $user_id, $book_id);
                $delete_checked_out->execute();

                // Increment the copies_available in the books table
                $update_book = $conn->prepare("UPDATE books SET copies_available = copies_available + 1 WHERE id = ?");
                $update_book->bind_param("i", $book_id);
                $update_book->execute();
            }
        }

        // Step 3: Delete the user after checking in all books
        $delete_user_query = "DELETE FROM users WHERE user_id = ?";
        $delete_stmt = $conn->prepare($delete_user_query);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        
        echo "<script>alert('User deleted successfully, and all checked-out books have been checked in!');</script>";
    }

    if (isset($_POST['promote_user'])) {
        $user_id = $_POST['user_id'];
        // Prevent SQL injection by using prepared statement
        $stmt = $conn->prepare("UPDATE users SET is_admin = 1 WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('User promoted to admin successfully!');</script>";
    }
}

// Fetch all users
$users_query = "SELECT user_id, username, is_admin FROM users";
$users_result = $conn->query($users_query);

// Check for query error
if (!$users_result) {
    die("Error fetching users: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>View Users</title>
    <style>
        .table-responsive {
            margin-top: 20px;
        }
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        button:focus {
            outline: none;
            box-shadow: none;
        }

        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="text-center">View Users</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users_result->num_rows > 0): ?>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                                <td>
                                    <form method="POST" style="display:inline-block;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user and check in all their books?');">Delete</button>
                                    </form>
                                    <?php if (!$user['is_admin']): ?>
                                        <form method="POST" style="display:inline-block;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                            <button type="submit" name="promote_user" class="btn btn-success btn-sm">Make Admin</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
