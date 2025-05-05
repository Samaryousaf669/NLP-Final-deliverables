<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

include('config.php');

// Fetch all users from the database
$sql = "SELECT user_id, name, email, phone, address, city, role, created_at FROM users";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Users</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div>
        <h2>All Users</h2>
        <table border="1">
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>City</th>
                <th>Role</th>
                <th>Registered On</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["user_id"]); ?></td>
                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                    <td><?php echo htmlspecialchars($row["phone"]); ?></td>
                    <td><?php echo htmlspecialchars($row["address"]); ?></td>
                    <td><?php echo htmlspecialchars($row["city"]); ?></td>
                    <td><?php echo htmlspecialchars($row["role"]); ?></td>
                    <td><?php echo htmlspecialchars($row["created_at"]); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
