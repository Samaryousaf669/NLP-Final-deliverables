<?php
include('config.php');

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
    <title>Restaurant Chatbot</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <style>


        .jumbotron {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/c.jpg');
            background-size: fill;
            text-align: center;
            padding: 150px 20px;
            color: whitesmoke;
        }

        .jumbotron h1 {
            font-size: 3em;
            margin-bottom: 20px;
            color:white;
        }

        .jumbotron p {
            font-size: 1.5em;
        }

        section {
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
        }


        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
        }

        .contact-form {
            display: grid;
            gap: 15px;
        }

        .contact-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .contact-form button {
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .contact-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>
<?php include('dialogue_flow.php'); ?>

<div class="jumbotron">
    <h1>Welcome to Our Restaurant</h1>
    <p>Explore our delicious menu, place orders, and make reservations with ease.</p>
</div>


<section>
    <h2>Our Menu</h2>

    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search menu..." value="<?php echo htmlspecialchars($search_query); ?>" style="padding:10px; width:300px;">
        <button type="submit" style="padding:10px;">Search</button>
    </form>

    <?php if (isset($_GET['search']) && empty(trim($_GET['search']))) : ?>
        <div style="color: red; text-align: center; margin-top: 20px;">
            <p>Please enter a valid search term.</p>
        </div>
    <?php elseif (mysqli_num_rows($menu_result) == 0) : ?>
        <div style="color: red; text-align: center; margin-top: 20px;">
            <p>No menu items found. Please try another search.</p>
        </div>
    <?php else : ?>
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

<section>
    <h2>Contact Us</h2>
    <p>Have questions? Reach out to us!</p>

    <form action="#" method="POST" class="contact-form">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" required>

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="5" required></textarea>

        <button type="submit">Submit</button>
    </form>
</section>

<footer>
    <p>&copy; 2025 Restaurant Chatbot. All rights reserved.</p>
</footer>

</body>
</html>