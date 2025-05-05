<?php
require_once('..\config\db_connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $old = [];

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $old = ['email' => $email];

    // Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $errors['password'] = "Please enter your password.";
    }

    if (!empty($errors)) {
        header('Location: ../views/login.php?errors=' . urlencode(json_encode($errors)) . '&old=' . urlencode(json_encode($old)));
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

            header('Location: ../pages/booking_success.php');
            exit();
        } else {
            // Wrong credentials
            $errors['general'] = "Invalid email or password.";
            header('Location: ../pages/login.php?errors=' . urlencode(json_encode($errors)) . '&old=' . urlencode(json_encode($old)));
            exit();
        }

    } catch (PDOException $e) {
        echo "Login failed: " . $e->getMessage();
    }

} else {
    header('Location: ../views/login.php');
    exit();
}
?>

