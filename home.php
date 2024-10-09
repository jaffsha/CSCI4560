<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: admin_dashboard.php'); // Redirect to admin dashboard if logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
    <title>Library Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f4f4f4;
        }

        h1 {
            margin-bottom: 20px;
        }

        .button {
            background-color: #007bff; /* Bootstrap primary color */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 18px;
            margin: 10px;
        }

        .button:hover {
            background-color: #0056b3; /* Darken button on hover */
        }

    </style>
</head>
<body>

    <h1>Welcome to the Library Management System</h1>
    <a href="login.php" class="button">Login</a>
    <a href="register.php" class="button">Register</a>

</body>
</html>
