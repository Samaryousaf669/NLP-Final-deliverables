<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    $role = 'customer'; 

    // Check if email already exists
    $sql_email_check = "SELECT * FROM users WHERE email = ?";
    $stmt_email_check = mysqli_prepare($conn, $sql_email_check);
    mysqli_stmt_bind_param($stmt_email_check, "s", $email);
    mysqli_stmt_execute($stmt_email_check);
    mysqli_stmt_store_result($stmt_email_check);
    $email_count = mysqli_stmt_num_rows($stmt_email_check);
    mysqli_stmt_close($stmt_email_check);

    if ($email_count > 0) {
        echo '<div>Email already exists. Please use a different email address.</div>';
    } else {
        // Hash the password before storing
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user into the database
        $sql_insert_user = "INSERT INTO users (name, email, password, phone, address, city, role, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
        mysqli_stmt_bind_param($stmt_insert_user, "sssssss", $name, $email, $hashed_password, $phone, $address, $city, $role);

        if (mysqli_stmt_execute($stmt_insert_user)) {
            echo '<div>Registration successful.</div>';
        } else {
            echo '<div>Registration failed. Please try again later.</div>';
        }

        mysqli_stmt_close($stmt_insert_user);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="./CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div>
        <h2>User Registration</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div>
                <label>Phone</label>
                <input type="text" name="phone" required>
            </div>
            <div>
                <label>Address</label>
                <textarea name="address" required></textarea>
            </div>
            <div>
                <label>City</label>
                <input type="text" name="city" required>
            </div>
            <div>
                <input type="submit" value="Register">
            </div>
        </form>
    </div>
</body>
</html>
