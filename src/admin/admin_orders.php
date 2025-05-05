<?php
session_start();
include('config.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}

// Fetch all orders with their items
$sql = "
SELECT 
    o.id AS order_id,
    o.user_id,
    o.total_amount,
    o.status,
    o.created_at,
    oi.quantity,
    oi.price_each,
    m.item_name,
    u.name AS customer_name
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN menu m ON oi.menu_item_id = m.id
JOIN users u ON o.user_id = u.user_id
ORDER BY o.created_at DESC";

$result = mysqli_query($conn, $sql);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[$row['order_id']]['customer_name'] = $row['customer_name'];
    $orders[$row['order_id']]['total_amount'] = $row['total_amount'];
    $orders[$row['order_id']]['status'] = $row['status'];
    $orders[$row['order_id']]['created_at'] = $row['created_at'];
    $orders[$row['order_id']]['items'][] = [
        'item_name' => $row['item_name'],
        'quantity' => $row['quantity'],
        'price_each' => $row['price_each']
    ];
}

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Update the order status in the database
    $update_sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Order status updated successfully'); window.location.href = 'admin_orders.php';</script>";
    } else {
        echo "<script>alert('Error updating order status');</script>";
    }
}

// Status color logic
function getStatusColor($status) {
    switch ($status) {
        case 'pending':
            return 'orange';  // Pending (awaiting)
        case 'preparing':
            return 'blue';  // Preparing (being prepared)
        case 'completed':
            return 'green';  // Completed (order finished)
        case 'cancelled':
            return 'red';  // Cancelled (order was canceled)
        default:
            return 'gray';  // Unknown status
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - All Orders</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        .order-box {
            border: 1px solid #ccc;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .order-box h3 {
            margin-bottom: 10px;
        }

        .order-box ul {
            padding-left: 20px;
        }

        .order-box ul li {
            margin-bottom: 5px;
        }

        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
        }

        .status-select {
            margin-top: 10px;
            padding: 5px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div style="max-width: 1000px; margin: auto; padding: 20px;">
    <h2>All Orders</h2>

    <?php if (empty($orders)) : ?>
        <p>No orders have been placed yet.</p>
    <?php else : ?>
        <?php foreach ($orders as $order_id => $order) : ?>
            <div class="order-box">
                <h3>Order #<?php echo $order_id; ?> - 
                    <span class="status" style="background-color: <?php echo getStatusColor($order['status']); ?>;">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </h3>
                <p><strong>Customer:</strong> <?php echo $order['customer_name']; ?></p>
                <p><strong>Placed On:</strong> <?php echo $order['created_at']; ?></p>
                <p><strong>Total Amount:</strong> Pkr <?php echo number_format($order['total_amount'], 2); ?></p>
                <ul>
                    <?php foreach ($order['items'] as $item) : ?>
                        <li>
                            <?php echo $item['item_name']; ?> - 
                            <?php echo $item['quantity']; ?> × 
                            Pkr <?php echo number_format($item['price_each'], 2); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <form action="admin_orders.php" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
                    <select name="status" class="status-select" required>
                        <option value="pending" <?php if ($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="preparing" <?php if ($order['status'] == 'preparing') echo 'selected'; ?>>Preparing</option>
                        <option value="completed" <?php if ($order['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                        <option value="cancelled" <?php if ($order['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                    <button type="submit" style="padding: 10px; background: green; color: white; border: none; border-radius: 5px;">Update Status</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
