<?php include('../partials/header.php');

session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}
?>

<section class="success">
    <div class="container">
        <h2>Booking Successful!</h2>
        <p>Thank you for booking with Wired Hotel.</p>
        <a href="index.php" class="btn">Return Home</a>
    </div>
</section>

<?php include('../partials/footer.php'); ?>
