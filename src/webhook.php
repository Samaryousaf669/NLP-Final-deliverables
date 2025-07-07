<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// DB connection
$conn = new mysqli("localhost", "root", "", "restaurant_chatbot-sameer");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the request from Dialogflow
$request = json_decode(file_get_contents("php://input"), true);
$intent = $request['queryResult']['intent']['displayName'];

$responseText = "Sorry, I didn't understand.";

// === VIEW MENU INTENT ===
if ($intent === "view.menu") {
    $sql = "SELECT item_name, description, price FROM menu";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $responseText = "Here's our menu:\n";
        while ($row = $result->fetch_assoc()) {
            $responseText .= "🍽 " . $row['item_name'] . " - " . $row['description'] . " (Rs. " . $row['price'] . ")\n\n";
        }
    } else {
        $responseText = "Sorry, the menu is currently empty.";
    }
}

// === CHECK ORDER STATUS INTENT ===
else if ($intent === "CheckOrderStatus") {
    $orderId = $request['queryResult']['parameters']['order-id'];

    $sql = "SELECT status FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $status = ucfirst($row['status']);
        $responseText = "Your order #$orderId is currently *$status*.";
    } else {
        $responseText = "Sorry, I couldn't find an order with ID $orderId.";
    }
}

// === PLACE ORDER INTENT ===
if ($intent === "place.order") {
    $params = $request['queryResult']['parameters'];

    // Safety check for required params
    if (isset($params['user_id'], $params['menu_id'], $params['quantity'])) {
        $userId = (int)$params['user_id'];
        $menuId = (int)$params['menu_id'];
        $quantity = (int)$params['quantity'];

        if ($quantity <= 0) {
            $responseText = "❌ Quantity must be greater than zero.";
        } else {
            // Get menu item price
            $stmt = $conn->prepare("SELECT price FROM menu WHERE id = ?");
            $stmt->bind_param("i", $menuId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $priceEach = $row['price'];
                $totalAmount = $priceEach * $quantity;

                // Insert into orders table
                $stmtOrder = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
                $stmtOrder->bind_param("id", $userId, $totalAmount);
                $stmtOrder->execute();
                $orderId = $stmtOrder->insert_id;

                // Insert into order_items table
                $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price_each) VALUES (?, ?, ?, ?)");
                $stmtItem->bind_param("iiid", $orderId, $menuId, $quantity, $priceEach);
                $stmtItem->execute();

                $responseText = "✅ Order placed! Order ID: $orderId\nItem ID: $menuId\nQuantity: $quantity\nTotal: Rs. $totalAmount";
            } else {
                $responseText = "❌ Menu item with ID $menuId not found.";
            }
        }
    } else {
        $responseText = "❌ Missing required parameters (user_id, menu_id, quantity).";
    }
}

// === CHECK RESERVATION STATUS INTENT ===
else if ($intent === "check.reservation.status") {
    $params = $request['queryResult']['parameters'];

    if (isset($params['reservation_id'])) {
        $reservationId = (int)$params['reservation_id'];

        $stmt = $conn->prepare("SELECT status, reservation_date, reservation_time, people_count FROM reservations WHERE id = ?");
        $stmt->bind_param("i", $reservationId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $status = ucfirst($row['status']);
            $date = $row['reservation_date'];
            $time = $row['reservation_time'];
            $people = $row['people_count'];

            $responseText = "📅 Reservation #$reservationId:\n- Date: $date\n- Time: $time\n- People: $people\n- Status: *$status*.";
        } else {
            $responseText = "❌ No reservation found with ID $reservationId.";
        }
    } else {
        $responseText = "❌ Reservation ID is missing from the request.";
    }
}

// === MAKE RESERVATION INTENT ===
else if ($intent === "make.reservation") {
    $params = $request['queryResult']['parameters'];

    $userId = (int)$params['user_id'];
    $dateTimeRaw = $params['reservation_date']; // e.g., "2025-06-03T12:00:00+05:00"
    $timeRaw = $params['reservation_time'];     // e.g., "2025-06-02T18:00:00+05:00"
    $guests = (int)$params['guests'];

    // Extract DATE (Y-m-d) from reservation_date
    $date = date('Y-m-d', strtotime($dateTimeRaw));

    // Extract TIME (H:i:s) from reservation_time
    $time = date('H:i:s', strtotime($timeRaw));

    // Prepare and insert
    $stmt = $conn->prepare("INSERT INTO reservations (user_id, reservation_date, reservation_time, people_count) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $userId, $date, $time, $guests);

    if ($stmt->execute()) {
        $reservationId = $stmt->insert_id;
        $responseText = "✅ Reservation confirmed! Your reservation ID is *$reservationId* for $guests guests on $date at $time.";
    } else {
        $responseText = "❌ Sorry, something went wrong. Could not complete the reservation.";
    }
}



// Final response
echo json_encode([
    "fulfillmentText" => $responseText
]);
?>
