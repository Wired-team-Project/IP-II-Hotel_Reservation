<?php
session_start();
require_once '../config/db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error=[];
    
    $adults = isset($_POST['adults']) ? intval($_POST['adults']) : 0;
    $children = isset($_POST['children']) ? intval($_POST['children']) : 0;
    $checkin_date = trim($_POST['checkin_date'] ?? '');
    $checkout_date = trim($_POST['checkout_date'] ?? '');

    if (empty($checkin_date)) {
        $errors['checkin_date'] = "Check-in date is required.";
    }
    if (empty($checkout_date)) {
        $errors['checkout_date'] = "Check-out date is required.";
    }
    if ($adults <= 0) {
        $errors['adults'] = "At least one adult is required.";
    }

        if (!empty($errors)) {
        $query = http_build_query([
            'errors' => json_encode($errors),
            'values' => json_encode([
                'checkin_date' => $checkin_date,
                'checkout_date' => $checkout_date,
                'adults' => $adults,
                'children' => $children
            ])
        ]);
        header("Location: ../pages/booking.php?$query");
        exit();
    }

    $_SESSION['booking_info'] = [
        'checkin_date' => $checkin_date,
        'checkout_date' => $checkout_date,
        'adults' => $adults,
        'children' => $children
    ];

    header('Location: available_rooms.php');
    exit();
}
?>

