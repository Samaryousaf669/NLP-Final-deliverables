<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

include('config.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_name = mysqli_real_escape_string($conn, $_POST["item_name"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $price = mysqli_real_escape_string($conn, $_POST["price"]);

    $image_url = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . time() . "_" . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
            $message = "Image upload failed.";
        }
    }

    $sql = "INSERT INTO menu (item_name, description, price, image_url, created_at)
            VALUES ('$item_name', '$description', '$price', '$image_url', NOW())";

    if (mysqli_query($conn, $sql)) {
        $message = "Menu item added successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Menu Item</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div>
        <h2>Add Menu Item</h2>
        <p style="color:green;"><?php echo $message; ?></p>
        <form action="" method="post" enctype="multipart/form-data">
            <label>Item Name:</label><br>
            <input type="text" name="item_name" required><br><br>

            <label>Description:</label><br>
            <textarea name="description" rows="4" required></textarea><br><br>

            <label>Price (e.g., 10.99):</label><br>
            <input type="number" name="price" step="0.01" required><br><br>

            <label>Image (optional):</label><br>
            <input type="file" name="image" accept="image/*"><br><br>

            <input type="submit" value="Add Menu Item">
            <a href="view_menu.php" class="button-link">View Menu</a>
        </form>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>
