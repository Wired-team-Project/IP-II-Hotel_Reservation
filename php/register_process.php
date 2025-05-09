<?php
require_once('../config/db_connect.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $old = [];

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $country = trim($_POST['country']);
    $id_passport = trim($_POST['id_passport']);
    $dob = $_POST['dob'];

    $old = compact('fullname', 'email', 'phone', 'address', 'country', 'id_passport', 'dob');

    if (empty($fullname) || !preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        $errors['fullname'] = "Name must only contain letters and spaces.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($password) || !preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/", $password)) {
        $errors['password'] = "Password must be at least 6 characters, with uppercase, lowercase, and a number.";
    }

    if (empty($phone) || !preg_match("/^\+?\d{10,15}$/", $phone)) {
        $errors['phone'] = "Phone must be valid with 10-15 digits.";
    }

    if (empty($address)) {
        $errors['address'] = "Address is required.";
    }

    if (empty($country)) {
        $errors['country'] = "Country is required.";
    }

    if (empty($id_passport) || !preg_match("/^[a-zA-Z0-9]{5,20}$/", $id_passport)) {
        $errors['id_passport'] = "ID/Passport must be 5-20 letters/numbers.";
    }

    if (empty($dob)) {
        $errors['dob'] = "Date of birth is required.";
    }

    if (strtotime($dob) > time()) {
        $errors['dob'] = "Date of birth cannot be in the future.";
    }

    if (!empty($errors)) {
        $old = compact('fullname', 'email', 'phone', 'address', 'country', 'id_passport', 'dob');
        header('Location: ../pages/register.php?errors=' . urlencode(json_encode($errors)) . '&old=' . urlencode(json_encode($old)));
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {

        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors['email'] = "Email is already registered.";
            header('Location: ../pages/register.php?errors=' . urlencode(json_encode($errors)) . '&old=' . urlencode(json_encode($old)));
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO customers (full_name, email, password, phone, address, country, id_passport, dob) 
                                VALUES (:fullname, :email, :password, :phone, :address, :country, :id_passport, :dob)");

        $stmt->execute([
            'fullname' => $fullname,
            'email' => $email,
            'password' => $passwordHash,
            'phone' => $phone,
            'address' => $address,
            'country' => $country,
            'id_passport' => $id_passport,
            'dob' => $dob
        ]);

        $_SESSION['success'] = "Registration successful. Please login.";
        // $_SESSION['customer_id'] = $conn->lastInsertId();
        $_SESSION['customer_name'] = $fullname;
        header('Location: ../pages/login.php');
        exit();
    } catch (PDOException $e) {
        $errors['general'] = "Registration error. Please try again.";
        header('Location: ../pages/register.php?errors=' . urlencode(json_encode($errors)) . '&old=' . urlencode(json_encode($old)));
        exit();
    }
} else {
    header('Location: ../pages/register.php');
    exit();
}
?>
