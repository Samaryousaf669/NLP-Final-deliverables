<?php
include('config.php');
session_start();

// Initialize variables
$email = $password = "";
$email_err = $password_err = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Validate email and password
    if (empty($email)) {
        $email_err = "Please enter your email.";
    }
    if (empty($password)) {
        $password_err = "Please enter your password.";
    }

    if (empty($email_err) && empty($password_err)) {
        // Check if user exists
        $sql = "SELECT user_id, name, password, role FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $user_id, $name, $hashed_password, $role);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Set session variables
                        $_SESSION["user_id"] = $user_id;
                        $_SESSION["name"] = $name;
                        $_SESSION["email"] = $email;
                        $_SESSION["role"] = $role;

                        // Redirect based on user role 
                        if ($role === 'admin') {
                            header("Location: admin/admin_dashboard.php");
                        } else {
                            header("Location: dashboard.php");
                        }
                        exit();
                    } else {
                        $password_err = "Incorrect password.";
                    }
                }
            } else {
                $email_err = "No account found with that email.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="./CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div>
        <div class="card-body">
            <h2>Login</h2>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <span class="error"><?php echo $email_err; ?></span>
                </div>

                <div>
                    <label>Password</label>
                    <input type="password" name="password" required>
                    <span class="error"><?php echo $password_err; ?></span>
                </div>

                <div>
                    <input type="submit" value="Login">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
