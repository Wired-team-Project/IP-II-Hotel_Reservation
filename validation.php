<?php
ob_start();

    // session_start();

include('include/functions.php');

if (isset($_POST['user_login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error("login.php", "Invalid email format.");
    }

    // Secure SQL query using prepared statements
    $stmt = $con->prepare("SELECT * FROM users_details WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['Password'])) {
            $_SESSION['loggedUserName'] = $user['FirstName'];
            $_SESSION['loggedUserId'] = $user['UserId'];
            $_SESSION['userRole'] = $user['Role'];

            //DO NOT use ob_clean() here. Just redirect:
            switch ($user['Role']) {
                case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                case 'client':
                    header("Location: client/dashboard.php");
                    break;
                case 'receptionist':
                    header("Location: receptionist/dashboard.php");
                    break;
                default:
                    header("Location: index.php");
            }
            exit;
        } else {
            error("login.php", "Incorrect password.");
        }
    } else {
        error("login.php", "No user found with that email.");
    }

    $stmt->close();
}
ob_end_flush();
?>
