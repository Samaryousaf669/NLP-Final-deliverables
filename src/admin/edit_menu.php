<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

include('config.php');

if (!isset($_GET["id"])) {
    header("Location: view_menu.php");
    exit();
}

$id = (int)$_GET["id"];
$message = "";

// Fetch existing data
$sql = "SELECT * FROM menu WHERE id = $id";
$result = mysqli_query($conn, $sql);
$menu = mysqli_fetch_assoc($result);

if (!$menu) {
    die("Menu item not found.");
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_name = mysqli_real_escape_string($conn, $_POST["item_name"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $price = mysqli_real_escape_string($conn, $_POST["price"]);
    $image_url = $menu["image_url"]; // default to current

    // Image upload check
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            if (!empty($image_url) && file_exists($image_url)) {
                unlink($image_url); // delete old
            }
            $image_url = $target_file;
        }
    }

    $update_sql = "UPDATE menu SET 
                    item_name='$item_name',
                    description='$description',
                    price='$price',
                    image_url='$image_url'
                   WHERE id=$id";

    if (mysqli_query($conn, $update_sql)) {
        $message = "Updated successfully!";
        // Refresh data
        $menu["item_name"] = $item_name;
        $menu["description"] = $description;
        $menu["price"] = $price;
        $menu["image_url"] = $image_url;
    } else {
        $message = "Update failed: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Menu Item</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div>
    <h2>Edit Menu Item</h2>
    <p style="color:green;"><?php echo $message; ?></p>
    <form method="post" enctype="multipart/form-data">
        <label>Item Name:</label><br>
        <input type="text" name="item_name" value="<?php echo htmlspecialchars($menu["item_name"]); ?>" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="4" required><?php echo htmlspecialchars($menu["description"]); ?></textarea><br><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" value="<?php echo $menu["price"]; ?>" required><br><br>

        <label>Image:</label><br>
        <?php if (!empty($menu["image_url"])): ?>
            <img src="<?php echo $menu["image_url"]; ?>" width="100"><br>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*"><br><br>

        <input type="submit" value="Update Menu Item">
    </form>
</div>
</body>
</html>

<?php mysqli_close($conn); ?>
