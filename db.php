<?php
$servername = "localhost";
$username = "user1";
$password = "123";
$dbname = "online-quiz";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
