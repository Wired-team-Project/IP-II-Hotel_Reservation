<?php 
include("include/dbConnect.php");

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function handle_contact_form($con) {
    $sendData = ["msg" => "", "error" => ""];

    $fname = sanitize_input($_POST['FirstName'] ?? '');
    $lname = sanitize_input($_POST['LastName'] ?? '');
    $email = sanitize_input($_POST['Email'] ?? '');
    $msg = sanitize_input($_POST['Message'] ?? '');

    // Basic server-side validation
    if (empty($fname) || empty($lname) || empty($email) || empty($msg)) {
        $sendData['error'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sendData['error'] = "Invalid email format.";
    } elseif (strlen($msg) < 10) {
        $sendData['error'] = "Message should be at least 10 characters.";
    } else {
        $stmt = $con->prepare("INSERT INTO contact (FirstName, LastName, Email, Message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fname, $lname, $email, $msg);

        if ($stmt->execute()) {
            $sendData['msg'] = "Thanks For Your Feedback!";
        } else {
            $sendData['error'] = "Server Error. Please try again.";
        }

        $stmt->close();
    }

    echo json_encode($sendData);
}

function error($page, $msg) {
    header("Location: $page?error=" . urlencode($msg));
    exit;
}

// Only handle the contact form submission when that specific form is submitted
if (isset($_POST['Message'])) {
    handle_contact_form($con);
}
