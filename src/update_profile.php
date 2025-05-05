<?php
session_start();

// Check if user is logged in as a customer
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

include('config.php');

$user_id = $_SESSION["user_id"];
$name = $phone = $address = $city = "";
$success_msg = $error_msg = "";

// Fetch current user details
$sql = "SELECT name, phone, address, city FROM users WHERE user_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $name, $phone, $address, $city);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    $new_password = trim($_POST["password"]);

    // Update query
    if (!empty($new_password)) {
        // If password is entered, hash it
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET name = ?, phone = ?, address = ?, city = ?, password = ? WHERE user_id = ?";
    } else {
        // If no password update
        $sql = "UPDATE users SET name = ?, phone = ?, address = ?, city = ? WHERE user_id = ?";
    }

    if ($stmt = mysqli_prepare($conn, $sql)) {
        if (!empty($new_password)) {
            mysqli_stmt_bind_param($stmt, "sssssi", $name, $phone, $address, $city, $hashed_password, $user_id);
        } else {
            mysqli_stmt_bind_param($stmt, "ssssi", $name, $phone, $address, $city, $user_id);
        }

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Profile updated successfully.";
        } else {
            $error_msg = "Something went wrong. Please try again.";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Profile</title>
    <link rel="stylesheet" href="./CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div>
        <h2>Update Profile</h2>
        <?php if ($success_msg) echo "<p style='color: green;'>$success_msg</p>"; ?>
        <?php if ($error_msg) echo "<p style='color: red;'>$error_msg</p>"; ?>

        <form action="update_profile.php" method="post">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>

            <label>Address:</label>
            <textarea name="address" required><?php echo htmlspecialchars($address); ?></textarea>

            <label>City:</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>" required>

            <label>New Password (Leave blank to keep current password):</label>
            <input type="password" name="password">

            <input type="submit" value="Update Profile">
        </form>
    </div>
</body>
</html>
