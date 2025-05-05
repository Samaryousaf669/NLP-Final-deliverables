<?php
session_start();
include('config.php');

// Check if user is logged in and is a customer
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch orders with their items
$sql = "
SELECT 
    o.id AS order_id,
    o.total_amount,
    o.status,
    o.created_at,
    oi.quantity,
    oi.price_each,
    m.item_name
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN menu m ON oi.menu_item_id = m.id
WHERE o.user_id = $user_id
ORDER BY o.created_at DESC";

$result = mysqli_query($conn, $sql);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[$row['order_id']]['total_amount'] = $row['total_amount'];
    $orders[$row['order_id']]['status'] = $row['status'];
    $orders[$row['order_id']]['created_at'] = $row['created_at'];
    $orders[$row['order_id']]['items'][] = [
        'item_name' => $row['item_name'],
        'quantity' => $row['quantity'],
        'price_each' => $row['price_each']
    ];
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
    <title>My Orders</title>
    <link rel="stylesheet" href="./CSS/style.css">
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
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div style="max-width: 800px; margin: auto; padding: 20px;">
    <h2>My Orders</h2>

    <?php if (empty($orders)) : ?>
        <p>You haven't placed any orders yet.</p>
    <?php else : ?>
        <?php foreach ($orders as $order_id => $order) : ?>
            <div class="order-box">
                <h3>Order #<?php echo $order_id; ?> - 
                    <span class="status" style="background-color: <?php echo getStatusColor($order['status']); ?>;">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </h3>
                <p><strong>Placed On:</strong> <?php echo $order['created_at']; ?></p>
                <p><strong>Total:</strong> Pkr <?php echo number_format($order['total_amount'], 2); ?></p>
                <ul>
                    <?php foreach ($order['items'] as $item) : ?>
                        <li>
                            <?php echo $item['item_name']; ?> - 
                            <?php echo $item['quantity']; ?> × 
                            Pkr <?php echo number_format($item['price_each'], 2); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
