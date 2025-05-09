<?php include '../partials/header.php';
session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$errors = [];
$values = [
    'checkin_date' => '',
    'checkout_date' => '',
    'adults' => 1,
    'children' => 0
];

if (isset($_GET['errors'])) {
    $decodedErrors = json_decode($_GET['errors'], true);
    if (is_array($decodedErrors)) {
        $errors = $decodedErrors;
    }
}

if (isset($_GET['values'])) {
    $decodedValues = json_decode($_GET['values'], true);
    if (is_array($decodedValues)) {
        $values = array_merge($values, $decodedValues);
    }
}
?>

<section class="booking">
    <div class="container">
        <h2>Book Your Stay</h2>
        

        <form action= "check_availability.php" method="POST" class="booking-form">

            <div class="form-group">
                <label>Check-in Date:</label>
                <input type="date" name="checkin_date" required>
                 <?php if (isset($errors['checkin_date'])): ?>
                    <div class="error" style="color: red;"><?= $errors['checkin_date'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Check-out Date:</label>
                <input type="date" name="checkout_date" required>
                <?php if (isset($errors['checkout_date'])): ?>
                    <div class="error" style="color: red;"><?= $errors['checkout_date'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Adults:</label>
                <input type="number" name="adults" min="1" value="1" required>
                <?php if (isset($errors['adults'])): ?>
                    <div class="error" style="color: red;"><?= $errors['adults'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Children:</label>
                <input type="number" name="children" min="0" value="0" required>
            </div>

            <button type="submit" class="btn">Check Availability</button>

        </form>
    </div>
</section>

<?php include '../partials/footer.php'; ?>
