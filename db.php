<?php
$conn = new mysqli(
    "localhost",
    "root",
    "",
    "it_support"
);

if ($conn->connect_error) {
    http_response_code(500);
    exit("Database connection failed.");
}

$conn->set_charset("utf8mb4");
?>