<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "restaurant_chatbot-sameer");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>