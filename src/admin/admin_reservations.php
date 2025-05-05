<?php
session_start();
include('config.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'], $_POST['status'])) {
    $reservation_id = mysqli_real_escape_string($conn, $_POST['reservation_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Update the reservation status
    $update_sql = "UPDATE reservations SET status = '$status' WHERE id = '$reservation_id'";
    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Reservation status updated successfully'); window.location.href = 'admin_reservations.php';</script>";
    } else {
        echo "<script>alert('Error updating reservation status');</script>";
    }
}

// Fetch all reservations
$sql = "SELECT r.id, r.reservation_date, r.reservation_time, r.people_count, r.status, u.name AS customer_name 
        FROM reservations r
        JOIN users u ON r.user_id = u.user_id
        ORDER BY r.reservation_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Reservations</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div style="max-width: 1000px; margin: auto;">
        <h2>All Reservations</h2>

        <?php if (mysqli_num_rows($result) == 0) : ?>
            <p>No reservations made yet.</p>
        <?php else : ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div style="border: 1px solid #ccc; padding: 20px; margin-bottom: 20px;">
                    <h3>Reservation for <?php echo htmlspecialchars($row['customer_name']); ?></h3>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($row['reservation_date']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($row['reservation_time']); ?></p>
                    <p><strong>People Count:</strong> <?php echo htmlspecialchars($row['people_count']); ?></p>
                    <p><strong>Status:</strong> 
                        <span style="padding: 5px; background-color: <?php echo $row['status'] == 'confirmed' ? 'green' : ($row['status'] == 'pending' ? 'orange' : 'red'); ?>; color: white;">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </p>

                    <form method="POST" action="admin_reservations.php">
                        <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                        <select name="status" required>
                            <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $row['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="cancelled" <?php echo $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <button type="submit">Update Status</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>
</html>
