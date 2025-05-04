<?php
// Database configuration
$host = 'localhost'; 
$dbname = 'hotel_db';  
$username = 'root';      
$password = 'abcd@123';    
$port = '3307'; 

try {
    // Create PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, show error
    die("Database connection failed: " . $e->getMessage());
}
?>
