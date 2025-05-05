<?php
session_start();
include('config.php');

// Check if user is logged in and is a customer
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION["user_id"];
    $reservation_date = mysqli_real_escape_string($conn, $_POST['reservation_date']);
    $reservation_time = mysqli_real_escape_string($conn, $_POST['reservation_time']);
    $people_count = mysqli_real_escape_string($conn, $_POST['people_count']);

    // Insert reservation into the database
    $sql = "INSERT INTO reservations (user_id, reservation_date, reservation_time, people_count) 
            VALUES ('$user_id', '$reservation_date', '$reservation_time', '$people_count')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Reservation made successfully'); window.location.href = 'view_reservations.php';</script>";
    } else {
        echo "<script>alert('Error making reservation');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Make Reservation</title>
    <link rel="stylesheet" href="./CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div style="max-width: 600px; margin: auto;">
        <h2>Make a Reservation</h2>
        <form method="POST">
            <label for="reservation_date">Reservation Date:</label>
            <input type="date" name="reservation_date" required><br><br>

            <label for="reservation_time">Reservation Time:</label>
            <input type="time" name="reservation_time" required><br><br>

            <label for="people_count">Number of People:</label>
            <input type="number" name="people_count" required><br><br>

            <button type="submit" style="padding: 10px; background: green; color: white;">Make Reservation</button>
            <a href="view_reservations.php" class="button-link">View Reservations</a>

        </form>
    </div>
</body>
</html>
``
