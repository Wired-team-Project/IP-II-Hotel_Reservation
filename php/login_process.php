<?php
require_once('../config/db_connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $values = [];

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $values = ['email' => $email];

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $errors['password'] = "Please enter your password.";
    }

    if (!empty($errors)) {
        header('Location: ../pages/login.php?errors=' . urlencode(json_encode($errors)) . '&values=' . urlencode(json_encode($values)));
        exit();
    }

    // Check user
    try {
        $stmt = $conn->prepare("SELECT * FROM customers WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Success: login user
            $_SESSION['customer_id'] = $user['id'];
            $_SESSION['customer_name'] = $user['fullname'];

            header('Location: ../pages/booking.php');// chnaged from available_rooms to booking
            exit();
        } else {
            // Wrong credentials
            $errors['general'] = "Invalid email or password.";
            header('Location: ../pages/login.php?errors=' . urlencode(json_encode($errors)) . '&values=' . urlencode(json_encode($values)));
            exit();
        }

    } catch (PDOException $e) {
         $errors['general'] = "Login error. Please try again.";
        header('Location: ../pages/login.php?errors=' . urlencode(json_encode($errors)) . '&values=' . urlencode(json_encode($values)));
    }

} else {
    header('Location: ../pages/login.php');
    exit();
}
?>
