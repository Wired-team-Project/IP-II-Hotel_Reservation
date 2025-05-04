<?php 
include('../partials/header.php'); 
include('../config/db_connect.php');
session_start();

// Check if booking info exists in session
if (!isset($_SESSION['booking_info'])) {
    header('Location: ../pages/booking.php');
    exit();
}

if (!isset($_SESSION['customer_id'])) {
    // Redirect to login if not logged in
    header('Location: ../pages/login.php');
    exit();
}

$booking = $_SESSION['booking_info'];

// Fetch available rooms
try {
    $sql = "SELECT * FROM rooms WHERE status = 'available'";
    $stmt = $conn->query($sql);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching rooms: " . $e->getMessage();
    exit();
}
?>

<h2>Available Rooms</h2>
<p>From: <?php echo htmlspecialchars($booking['checkin_date']); ?> 
   To: <?php echo htmlspecialchars($booking['checkout_date']); ?></p>
<p>Adults: <?php echo htmlspecialchars($booking['adults']); ?>, 
   Children: <?php echo htmlspecialchars($booking['children']); ?></p>

<?php if (count($rooms) > 0): ?>
    <ul>
        <?php foreach ($rooms as $room): ?>
            <li>
                <h3><?php echo htmlspecialchars($room['room_type']); ?> - $<?php echo htmlspecialchars($room['price_per_night']); ?>/night</h3>
                <p><?php echo htmlspecialchars($room['description']); ?></p>

                <form action="../php/booking_process.php" method="POST">
                    <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                    <button type="submit">Book Now</button>
                </form>

            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No rooms available for the selected dates.</p>
<?php endif; ?>

<?php include('../partials/footer.php'); ?>
