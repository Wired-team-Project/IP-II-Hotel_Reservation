<?php
session_start();
require_once '../php/db_connect.php'; // Connect to database

// 1. Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

// 2. Check if required data is available
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room_id'], $_POST['check_in'], $_POST['check_out'])) {
    $room_id = intval($_POST['room_id']);
    $check_in = trim($_POST['check_in']);
    $check_out = trim($_POST['check_out']);
    $customer_id = $_SESSION['customer_id'];

    try {
        // 3. Insert booking into database
        $stmt = $pdo->prepare("INSERT INTO bookings (customer_id, room_id, check_in_date, check_out_date, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$customer_id, $room_id, $check_in, $check_out]);

        // 4. Update room status to 'booked'
        $updateRoom = $pdo->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
        $updateRoom->execute([$room_id]);

        // 5. Redirect to a confirmation page
        header('Location: booking_success.php');
        exit();
    } catch (PDOException $e) {
        die("Booking failed: " . $e->getMessage());
    }
} else {
    die('Invalid access.');
}
?>
