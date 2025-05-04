<?php
session_start();
require_once '../config/db_connect.php'; // Make sure your DB connection is included!

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adults = isset($_POST['adults']) ? intval($_POST['adults']) : 0;
    $children = isset($_POST['children']) ? intval($_POST['children']) : 0;
    $check_in = trim($_POST['check_in'] ?? '');
    $check_out = trim($_POST['check_out'] ?? '');

    if ($adults <= 0 || empty($check_in) || empty($check_out)) {
        die("Invalid input. Please go back and fill all fields correctly.");
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE status = 'available'");
        $stmt->execute();
        $available_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Rooms</title>
</head>
<body>
    <h1>Available Rooms</h1>

    <?php if (!empty($available_rooms)): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Price per Night</th>
                <th>Action</th>
            </tr>
            <?php foreach ($available_rooms as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['room_number']) ?></td>
                    <td><?= htmlspecialchars($room['room_type']) ?></td>
                    <td>$<?= htmlspecialchars($room['price']) ?></td>
                    <td>
                        <form action="book_room.php" method="post">
                            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                            <input type="hidden" name="check_in" value="<?= htmlspecialchars($check_in) ?>">
                            <input type="hidden" name="check_out" value="<?= htmlspecialchars($check_out) ?>">
                            <input type="submit" value="Book Now">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No available rooms for your search. Please try different dates!</p>
    <?php endif; ?>
</body>
</html>
