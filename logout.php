<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: home.php"); // Redirect to home page after logout
exit();
?>