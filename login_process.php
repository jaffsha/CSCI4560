<?php
session_start(); // Start the session
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and validate form inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit();
    }

    // Prepare and execute the statement
    $stmt = $conn->prepare("SELECT user_id, password, is_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $is_admin);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = $is_admin; // Store admin status

            // Redirect based on user type
            if ($is_admin) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit(); // Ensure no further code is executed after redirection
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

