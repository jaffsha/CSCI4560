<?php
$servername = "localhost";  // Your server name
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password (default is empty for XAMPP)
$dbname = "library_database"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
