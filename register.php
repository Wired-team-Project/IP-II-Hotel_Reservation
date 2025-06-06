<?php
include('include/functions.php');

if (isset($_POST['user_registration'])) {
    // Sanitize input
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $contactno = trim($_POST['contactno']);
    $gender = trim($_POST['gender']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['conformPassword'];
    $profileImageName = $_FILES["profileImage"]["name"];
    $tempname = $_FILES["profileImage"]["tmp_name"];
    $folder = "assets/picture/profiles/" . basename($profileImageName);
    $imageFileType = strtolower(pathinfo($folder, PATHINFO_EXTENSION));
    $allowedExtensions = ["jpg", "jpeg", "png", "webp"];
    $errors = []; // Array to store validation errors

    if (!preg_match("/^[a-zA-Z]+$/", $firstname)) {
        $errors[] = "First name must contain only letters (no spaces, numbers, or symbols).";
    }
    if (!preg_match("/^[a-zA-Z]+$/", $lastname)) {
        $errors[] = "Last name must contain only letters (no spaces, numbers, or symbols).";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (!preg_match("/^\+[0-9]{10,15}$/", $contactno)) {
        $errors[] = "Phone number must be in international format (e.g., +2519234567890).";
    }
    if (strlen($password) < 8 ||
        !preg_match('@[0-9]@', $password) ||
        !preg_match('@[A-Z]@', $password) ||
        !preg_match('@[a-z]@', $password) ||
        !preg_match('@[^\w]@', $password)) {
        $errors[] = "Password must be at least 8 characters and contain uppercase, lowercase, number, and special character.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }
    if (!in_array($imageFileType, $allowedExtensions) && !empty($profileImageName)) { // Only validate if a file is uploaded
        $errors[] = "Only JPG, JPEG, PNG, or WEBP image formats are allowed.";
    }

    // Check if there are any validation errors
    if (!empty($errors)) {
        $errorMessage = implode("<br>", $errors);
        error("signup.php", $errorMessage);
    } else {
        // Check if user already exists (only if no other errors)
        $User_details = "SELECT * FROM users_details WHERE Email='$email'";
        $result = mysqli_query($con, $User_details);
        if (mysqli_num_rows($result) > 0) {
            error("signup.php", "Username or email is already taken!");
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert data
            $insert = "INSERT INTO users_details (FirstName, LastName, Email, Password, ContactNo, Gender, ProfileImage, Role)
                        VALUES ('$firstname', '$lastname', '$email', '$hashedPassword', '$contactno', '$gender', '$profileImageName','client')";

            if (mysqli_query($con, $insert)) {
                if (!empty($profileImageName)) { // Only attempt to move if a file was uploaded
                    if (move_uploaded_file($tempname, $folder)) {
                        header("Location: index.php");
                        exit;
                    } else {
                        error("signup.php", "Profile image upload failed. Try again.");
                    }
                } else {
                    header("Location: index.php");
                    exit;
                }
            } else {
                error("signup.php", "Registration failed. Try again.");
            }
        }
    }
}
?>