<?php
include '../partials/header.php';

// Collect data from POST if user came from 'available_rooms.php'
$selected_room = $_POST['selected_room'] ?? '';
$checkin_date = $_POST['checkin_date'] ?? '';
$checkout_date = $_POST['checkout_date'] ?? '';
$adults = $_POST['adults'] ?? '';
$children = $_POST['children'] ?? '';
?>

<section class="login-section">
    <div class="container">
        <h2>Login</h2>

        <form action="../php/login_process.php" method="POST" class="form">
            <!-- Hidden fields to carry booking info -->
            <input type="hidden" name="selected_room" value="<?php echo htmlspecialchars($selected_room); ?>">
            <input type="hidden" name="checkin_date" value="<?php echo htmlspecialchars($checkin_date); ?>">
            <input type="hidden" name="checkout_date" value="<?php echo htmlspecialchars($checkout_date); ?>">
            <input type="hidden" name="adults" value="<?php echo htmlspecialchars($adults); ?>">
            <input type="hidden" name="children" value="<?php echo htmlspecialchars($children); ?>">

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn">Login</button>
        </form>

        <p class="small-text">
            Don't have an account? <a href="register.php">Register Now</a>
        </p>
    </div>
</section>

<?php include '../partials/footer.php'; ?>
