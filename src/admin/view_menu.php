<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

include('config.php');

// Handle deletion
if (isset($_GET["delete_id"])) {
    $delete_id = (int)$_GET["delete_id"];

    // Optional: Delete image file
    $img_sql = "SELECT image_url FROM menu WHERE id = $delete_id";
    $img_result = mysqli_query($conn, $img_sql);
    if ($img_row = mysqli_fetch_assoc($img_result)) {
        if (!empty($img_row["image_url"]) && file_exists($img_row["image_url"])) {
            unlink($img_row["image_url"]);
        }
    }

    // Delete from DB
    $sql = "DELETE FROM menu WHERE id = $delete_id";
    mysqli_query($conn, $sql);
    header("Location: view_menu.php");
    exit();
}

// Fetch all menu items
$sql = "SELECT * FROM menu ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Menu</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div>
    <h2>All Menu Items</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Description</th>
            <th>Price</th>
            <th>Image</th>
            <th>Added On</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo htmlspecialchars($row["item_name"]); ?></td>
                <td><?php echo htmlspecialchars($row["description"]); ?></td>
                <td>$<?php echo number_format($row["price"], 2); ?></td>
                <td>
                    <?php if (!empty($row["image_url"])): ?>
                        <img src="<?php echo $row["image_url"]; ?>" width="80">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?php echo $row["created_at"]; ?></td>
                <td>
                    <a href="edit_menu.php?id=<?php echo $row["id"]; ?>">Edit</a> |
                    <a href="view_menu.php?delete_id=<?php echo $row["id"]; ?>" onclick="return confirm('Delete this item?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>

<?php mysqli_close($conn); ?>
