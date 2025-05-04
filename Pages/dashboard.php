<?php 
include('../partials/header.php');
session_start();

if (!isset($_SESSION['customer_id'])) {
    echo "No session data found. Redirecting to login...";
    header('Location: login.php');
    exit();
}
?>

<h2>Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?>!</h2>
<p>You are logged in.</p>

<?php include('../partials/footer.php'); ?>
