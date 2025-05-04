<?php include '../partials/header.php'; ?>

<section class="booking">
    <div class="container">
        <h2>Book Your Stay</h2>
        

        <form action= "../php/booking_process.php" method="POST" class="booking-form">

            <div class="form-group">
                <label>Check-in Date:</label>
                <input type="date" name="checkin_date" required>
            </div>

            <div class="form-group">
                <label>Check-out Date:</label>
                <input type="date" name="checkout_date" required>
            </div>

            <div class="form-group">
                <label>Adults:</label>
                <input type="number" name="adults" min="1" value="1" required>
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
