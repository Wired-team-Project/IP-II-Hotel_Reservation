<?php
include_once('../config/db_connect.php');
session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

if (!isset($_SESSION['booking_info']) || !isset($_POST['room_id'])) {
    header('Location: ../pages/available_rooms.php');
    exit();
}

$booking = $_SESSION['booking_info'];
$room_id = (int)$_POST['room_id'];
$customer_id = $_SESSION['customer_id'];

$checkin_date = $booking['checkin_date'];
$checkout_date = $booking['checkout_date'];
$adults = $booking['adults'];
$children = $booking['children'];
$booking_date = date('Y-m-d');
$status = 'confirmed';

try {

    $stmt = $conn->prepare("
        INSERT INTO bookings (customer_id, room_id, checkin_date, checkout_date, adults, children, booking_date, status)
        VALUES (:customer_id, :room_id, :checkin_date, :checkout_date, :adults, :children, :booking_date, :status)
    ");
    $stmt->execute([
        ':customer_id' => $customer_id,
        ':room_id' => $room_id,
        ':checkin_date' => $checkin_date,
        ':checkout_date' => $checkout_date,
        ':adults' => $adults,
        ':children' => $children,
        ':booking_date' => $booking_date,
        ':status' => $status
    ]);

    $updateRoom = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE id = :room_id");
    $updateRoom->execute([':room_id' => $room_id]);

    unset($_SESSION['booking_info']);

    header('Location: ../pages/booking_success.php');
    exit();

} catch (PDOException $e) {
    echo "Booking failed: " . $e->getMessage();
    exit();
}
?>
