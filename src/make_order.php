<?php
session_start();
include('config.php');

// Check if user is logged in as customer
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Validate item_id
if (!isset($_GET["item_id"])) {
    echo "Invalid access. No item selected.";
    exit();
}

$item_id = intval($_GET["item_id"]);
$menu_sql = "SELECT * FROM menu WHERE id = $item_id";
$menu_result = mysqli_query($conn, $menu_sql);
$menu_item = mysqli_fetch_assoc($menu_result);

if (!$menu_item) {
    echo "Menu item not found.";
    exit();
}

// Handle order submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quantity = intval($_POST["quantity"]);
    if ($quantity < 1) $quantity = 1;

    $price_each = $menu_item["price"];
    $total_amount = $price_each * $quantity;

    // Insert into orders table
    $order_sql = "INSERT INTO orders (user_id, total_amount, status, created_at) 
                  VALUES ($user_id, $total_amount, 'pending', NOW())";
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn);

        // Insert into order_items table
        $item_sql = "INSERT INTO order_items (order_id, menu_item_id, quantity, price_each)
                     VALUES ($order_id, $item_id, $quantity, $price_each)";
        mysqli_query($conn, $item_sql);

        echo "<script>alert('Order placed successfully!'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        echo "Failed to place order. Try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Place Order</title>
    <link rel="stylesheet" href="./CSS/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div style="max-width: 600px; margin: auto; padding: 20px;">
    <h2>Order: <?php echo htmlspecialchars($menu_item["item_name"]); ?></h2>

    <?php if (!empty($menu_item["image_url"])): ?>
        <img src="admin/<?php echo $menu_item["image_url"]; ?>" alt="Item Image" style="width:100%; max-height:300px; object-fit:cover; margin-bottom:15px;">
    <?php endif; ?>

    <p><strong>Description:</strong> <?php echo htmlspecialchars($menu_item["description"]); ?></p>
    <p><strong>Price:</strong> Pkr <?php echo number_format($menu_item["price"], 2); ?></p>

    <form method="POST">
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" min="1" value="1" required style="padding:10px; width:80px;">

        <br><br>
        <button type="submit" style="padding:10px 20px; background:green; color:white; border:none; border-radius:5px;">
            Confirm Order
        </button>
    </form>
</div>
</body>
</html>
