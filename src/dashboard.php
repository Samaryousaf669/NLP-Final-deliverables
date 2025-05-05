<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: index.php");
    exit();
}

include('config.php');

$user_id = $_SESSION["user_id"];

// Handle search
$search_query = "";
$menu_sql = "SELECT * FROM menu";
if (isset($_GET["search"]) && !empty(trim($_GET["search"]))) {
    $search_query = mysqli_real_escape_string($conn, $_GET["search"]);
    $menu_sql .= " WHERE item_name LIKE '%$search_query%' OR description LIKE '%$search_query%'";
}
$menu_result = mysqli_query($conn, $menu_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <style>
        section {
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
        }

        .no-results {
            text-align: center;
            color: red;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?> (Customer)</h2>
    </div>

    <section>
        <h2>Our Menu</h2>

        <form method="GET" style="margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search menu..." value="<?php echo htmlspecialchars($search_query); ?>" style="padding:10px; width:300px;">
            <button type="submit" style="padding:10px;">Search</button>
        </form>

        <?php if (mysqli_num_rows($menu_result) == 0) : ?>
            <div class="no-results">
                <p>No menu items found. Please try another search.</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <?php while ($menu = mysqli_fetch_assoc($menu_result)) { ?>
                    <div style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; background: #f9f9f9;">
                        <?php if (!empty($menu["image_url"])): ?>
                            <img src="admin/<?php echo $menu["image_url"]; ?>" alt="Item Image" style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 5px;">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($menu["item_name"]); ?></h3>
                        <p><?php echo htmlspecialchars($menu["description"]); ?></p>
                        <p><strong>Price:</strong> Pkr <?php echo number_format($menu["price"], 2); ?></p>
                        <a href="make_order.php?item_id=<?php echo $menu['id']; ?>" 
                           style="display: inline-block; padding: 10px; background: green; color: white; text-decoration: none; border-radius: 5px;">
                           Order Now
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php endif; ?>
    </section>
</body>

</html>
