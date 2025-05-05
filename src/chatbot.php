<?php
session_start();



include('config.php');

// Function to fetch menu from the database
function getMenu() {
    global $conn;
    $query = "SELECT item_name, description, price FROM menu";
    $result = mysqli_query($conn, $query);

    $menuItems = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $menuItems[] = $row;
    }

    return $menuItems;
}

// Fetch Order Status
function getOrderStatus($order_id) {
    global $conn;
    $query = "SELECT status FROM orders WHERE id = '$order_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        return $order['status'];
    } else {
        return "No order found with that ID.";
    }
}

// Fetch Reservation Status
function getReservationStatus($reservation_id) {
    global $conn;
    $query = "SELECT status FROM reservations WHERE id = '$reservation_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $reservation = mysqli_fetch_assoc($result);
        return $reservation['status'];
    } else {
        return "No reservation found with that ID.";
    }
}

// Handle AJAX POST requests for order status or reservation status
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["order_id"])) {
        $order_id = mysqli_real_escape_string($conn, $_POST["order_id"]);
        $order_status = getOrderStatus($order_id);
        echo json_encode(['status' => $order_status]);
        exit();
    }
    if (isset($_POST["reservation_id"])) {
        $reservation_id = mysqli_real_escape_string($conn, $_POST["reservation_id"]);
        $reservation_status = getReservationStatus($reservation_id);
        echo json_encode(['status' => $reservation_status]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chatbot - Customer Support</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <style>
        section {
            padding: 40px 20px;
            max-width: 800px;
            margin: auto;
        }

        .chat-container {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            background: #f9f9f9;
            height: 500px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .chat-message {
            margin: 10px 0;
        }

        .chat-message.customer {
            text-align: right;
            color: blue;
        }

        .chat-message.bot {
            text-align: left;
            color: green;
        }

        .chat-form {
            margin-top: 20px;
            display: flex;
        }

        .chat-form input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            font-size: 16px;
        }

        .chat-form button {
            padding: 10px 20px;
            font-size: 16px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <section>
        <h2>Chat with our Bot</h2>

        <div class="chat-container" id="chatbox">
            <div class="chat-message bot">
            Hello! 👋 How can I assist you today? You can ask about the menu, your orders, or reservations.
            </div>
        </div>

        <form class="chat-form" id="chatForm" onsubmit="sendMessage(event)">
            <input type="text" id="userInput" placeholder="Type your message..." required>
            <button type="submit">Send</button>
        </form>
    </section>

    <script>
        let conversationHistory = [];

        function sendMessage(event) {
            event.preventDefault();
            const inputField = document.getElementById('userInput');
            const message = inputField.value.trim();
            if (message === '') return;

            const chatbox = document.getElementById('chatbox');

            // Show user's message
            const userMsg = document.createElement('div');
            userMsg.className = 'chat-message customer';
            userMsg.textContent = message;
            chatbox.appendChild(userMsg);

            // Bot response based on user message
            const botMsg = document.createElement('div');
            botMsg.className = 'chat-message bot';

            if (message.toLowerCase().includes("menu")) {
                // Fetch menu from PHP (you can adjust this part to show it better)
                const menu = <?php echo json_encode(getMenu()); ?>;
                let menuText = "Here is our menu:\n";
                menu.forEach(item => {
                    menuText += `- ${item.item_name} (${item.price} PKR)\n${item.description}\n\n`;
                });
                botMsg.textContent = menuText;
                chatbox.appendChild(botMsg);
            }


                else if (message.toLowerCase().includes("hello") || message.toLowerCase().includes("hi") || message.toLowerCase().includes("hey")) {
                botMsg.textContent = "Hello! 👋 How can I assist you today? You can ask about the menu, your orders, or reservations.";
            }
             else if (message.toLowerCase().includes("order status")) {
                botMsg.textContent = "Sure! Please provide your Order ID or the email associated with your order.";
                chatbox.appendChild(botMsg);
                requestOrderDetails();
            } else if (message.toLowerCase().includes("product")) {
                botMsg.textContent = "Which product would you like to know more about?";
            } else if (message.toLowerCase().includes("technical") || message.toLowerCase().includes("trouble") || message.toLowerCase().includes("support")) {
                botMsg.textContent = "Can you please describe the issue you're facing?";
            } else if (message.toLowerCase().includes("bye")) {
                botMsg.textContent = "Thank you for chatting with us. Have a great day!";
                chatbox.appendChild(botMsg);
                return;
            } else {
                botMsg.textContent = "I'm sorry, I didn't quite understand that. Could you please rephrase your question?";
            }

            chatbox.appendChild(botMsg);
            inputField.value = '';
            chatbox.scrollTop = chatbox.scrollHeight;
        }

        // Request Order ID or Email
        function requestOrderDetails() {
            const orderId = prompt("Please provide your Order ID or email.");
            if (orderId) {
                fetchOrderStatus(orderId);
            }
        }

        // Fetch Order Status via AJAX
        function fetchOrderStatus(orderId) {
            const chatbox = document.getElementById('chatbox');
            const botMsg = document.createElement('div');
            botMsg.className = 'chat-message bot';
            botMsg.textContent = "Checking your order status...";
            chatbox.appendChild(botMsg);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'chatbot.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    botMsg.textContent = "Order Status: " + response.status;
                    chatbox.appendChild(botMsg);
                    chatbox.scrollTop = chatbox.scrollHeight;
                }
            };
            xhr.send("order_id=" + orderId);
        }
    </script>

</body>

</html>
