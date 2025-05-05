<?php
session_start();
include('config.php');

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM reservations WHERE user_id = '$user_id' ORDER BY reservation_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Reservations</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <style>
        .status-pending { background-color: orange; color: white; padding: 5px; }
        .status-confirmed { background-color: green; color: white; padding: 5px; }
        .status-cancelled { background-color: red; color: white; padding: 5px; }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div style="max-width: 1000px; margin: auto;">
        <h2>Your Reservations</h2>

        <?php if (mysqli_num_rows($result) == 0) : ?>
            <p>You have no reservations yet.</p>
        <?php else : ?>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; margin-top: 20px;">
                <tr>
                    <th>Reservation ID</th>
                    <th>Reservation Date</th>
                    <th>Reservation Time</th>
                    <th>People Count</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['reservation_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['reservation_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['people_count']); ?></td>
                        <td>
                            <?php 
                                $status = $row['status'];
                                if ($status == 'pending') {
                                    echo '<span class="status-pending">' . ucfirst($status) . '</span>';
                                } elseif ($status == 'confirmed') {
                                    echo '<span class="status-confirmed">' . ucfirst($status) . '</span>';
                                } else {
                                    echo '<span class="status-cancelled">' . ucfirst($status) . '</span>';
                                }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
