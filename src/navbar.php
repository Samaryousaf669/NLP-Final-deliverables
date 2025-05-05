<nav>
        <ul>
            <li><a href="dashboard.php">Restaurant Chatbot</a></li>
            <?php
            if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])) {
                echo '<li><a href="dashboard.php">Home</a></li>';
                echo '<li><a href="update_profile.php">Profile</a></li>';
                echo '<li><a href="my_orders.php">Orders</a></li>';
                echo '<li><a href="make_reservation.php">Reservation</a></li>';
                echo '<li><a href="chatbot.php">Chatbot</a></li>';
                echo '<li><a href="logout.php">Logout</a></li>';
            } else {
                echo '<li><a href="register.php">Register</a></li>';
                echo '<li><a href="login.php">Login</a></li>';
                echo '<li><a href="chatbot.php">Chatbot</a></li>';

            }
            ?>
        </ul>
    </nav>