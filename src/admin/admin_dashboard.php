<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

include('config.php');

// Fetch total menu items
$menuQuery = "SELECT COUNT(*) AS total_items FROM menu";
$menuResult = mysqli_query($conn, $menuQuery);
$menuData = mysqli_fetch_assoc($menuResult);
$totalMenuItems = $menuData['total_items'];

// Fetch total orders
$orderQuery = "SELECT COUNT(*) AS total_orders, SUM(total_amount) AS total_sales FROM orders WHERE status != 'cancelled'";
$orderResult = mysqli_query($conn, $orderQuery);
$orderData = mysqli_fetch_assoc($orderResult);
$totalOrders = $orderData['total_orders'];
$totalSales = $orderData['total_sales'];

// Fetch total reservations
$reservationQuery = "SELECT COUNT(*) AS total_reservations FROM reservations WHERE status != 'cancelled'";
$reservationResult = mysqli_query($conn, $reservationQuery);
$reservationData = mysqli_fetch_assoc($reservationResult);
$totalReservations = $reservationData['total_reservations'];

// Fetch latest order
$latestOrderQuery = "SELECT orders.id AS order_id, orders.created_at, users.name AS customer_name, orders.total_amount, orders.status 
                     FROM orders 
                     JOIN users ON orders.user_id = users.user_id 
                     ORDER BY orders.created_at DESC LIMIT 1";
$latestOrderResult = mysqli_query($conn, $latestOrderQuery);
$latestOrder = mysqli_fetch_assoc($latestOrderResult);

// Fetch latest reservation
$latestReservationQuery = "SELECT reservations.id AS reservation_id, reservations.reservation_date, reservations.reservation_time, users.name AS customer_name, reservations.status 
                           FROM reservations 
                           JOIN users ON reservations.user_id = users.user_id 
                           ORDER BY reservations.created_at DESC LIMIT 1";
$latestReservationResult = mysqli_query($conn, $latestReservationQuery);
$latestReservation = mysqli_fetch_assoc($latestReservationResult);

// Fetch sales over time (monthly)
$salesQuery = "SELECT MONTH(created_at) AS month, SUM(total_amount) AS sales 
               FROM orders 
               WHERE status != 'cancelled' 
               GROUP BY MONTH(created_at)";
$salesResult = mysqli_query($conn, $salesQuery);
$salesData = [];
while ($row = mysqli_fetch_assoc($salesResult)) {
    $salesData[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            background-color: #f9f9f9;
            padding: 40px 20px;
            max-width: 1000px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .dashboard-header h2 {
            font-size: 28px;
            color: #333;
        }

        .dashboard-overview {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .overview-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 30%;
            text-align: center;
        }

        .overview-card h3 {
            color: #333;
        }

        .overview-card p {
            font-size: 20px;
            color: #007bff;
            font-weight: bold;
        }

        .latest-orders, .latest-reservations {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .latest-orders h3, .latest-reservations h3 {
            margin-bottom: 20px;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?> (Admin)</h2>
            <p>Your comprehensive dashboard to manage menu items, orders, and reservations.</p>
        </div>

        <!-- Dashboard Overview Section -->
        <div class="dashboard-overview">
            <div class="overview-card">
                <h3>Total Menu Items</h3>
                <p><?php echo $totalMenuItems; ?></p>
            </div>

            <div class="overview-card">
                <h3>Total Orders</h3>
                <p><?php echo $totalOrders; ?></p>
            </div>

            <div class="overview-card">
                <h3>Total Reservations</h3>
                <p><?php echo $totalReservations; ?></p>
            </div>
        </div>

        <!-- Latest Orders Section -->
        <div class="latest-orders">
            <h3>Latest Order</h3>
            <p><strong>Order ID:</strong> <?php echo $latestOrder['order_id']; ?></p>
            <p><strong>Customer:</strong> <?php echo $latestOrder['customer_name']; ?></p>
            <p><strong>Total Amount:</strong> <?php echo $latestOrder['total_amount']; ?> PKR</p>
            <p><strong>Status:</strong> <?php echo ucfirst($latestOrder['status']); ?></p>
            <p><strong>Created At:</strong> <?php echo $latestOrder['created_at']; ?></p>
        </div>

        <!-- Latest Reservations Section -->
        <div class="latest-reservations">
            <h3>Latest Reservation</h3>
            <p><strong>Reservation ID:</strong> <?php echo $latestReservation['reservation_id']; ?></p>
            <p><strong>Customer:</strong> <?php echo $latestReservation['customer_name']; ?></p>
            <p><strong>Date:</strong> <?php echo $latestReservation['reservation_date']; ?></p>
            <p><strong>Time:</strong> <?php echo $latestReservation['reservation_time']; ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($latestReservation['status']); ?></p>
        </div>

        <!-- Sales Over Time Chart -->
        <div class="sales-chart">
            <canvas id="salesChart"></canvas>
        </div>


    </div>

    <script>
        // Prepare data for the sales chart
        const salesData = <?php echo json_encode($salesData); ?>;
        const months = salesData.map(data => data.month);
        const sales = salesData.map(data => data.sales);

        // Create the sales chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Sales Over Time',
                    data: sales,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
