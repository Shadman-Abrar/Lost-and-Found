<?php
session_start();

$cookie_lifetime = 86400 * 7;
if (isset($_SESSION['user_id'])) {
    setcookie(session_name(), session_id(), time() + $cookie_lifetime, "/");
} else {
    header("Location: login.php"); 
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lostandfound";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>view-lost - Lost & Found</title></head>
<body><h1>view-lost page</h1></body>
</html>
