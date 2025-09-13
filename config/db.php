<?php

// define('DB_HOST', 'localhost');
// define('DB_USER', 'codefes2_user');
// define('DB_PASSWORD', 'admin@codefest.africa');
// define('DB_NAME', 'codefes2_cbt');

// $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

require __DIR__ . '/../vendor/autoload.php';

// Load .env from project root
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASSWORD'];
$name = $_ENV['DB_NAME'];

$conn = mysqli_connect($host, $user, $pass, $name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
