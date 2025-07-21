<?php
// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'banana_chip';

// Create connection
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (mysqli_connect_error()) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Helper function for escaping data
function escape_data($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, $data);
}
