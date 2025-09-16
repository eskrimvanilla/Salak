<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "salakbogor";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql_create_db) === TRUE) {
    $conn->select_db($dbname);

    $sql_create_table = "CREATE TABLE IF NOT EXISTS doctors (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        specialization VARCHAR(100),
        experience TEXT,
        image_url VARCHAR(500),
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql_create_table) !== TRUE) {
        echo "Error creating table: " . $conn->error;
    }
} else {
    echo "Error creating database: " . $conn->error;
}
?>
