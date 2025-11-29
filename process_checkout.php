<?php
// Start output buffering
ob_start(); 
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "SECRET";
$dbname = "shoppingline";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    header("Location: view_cart.php?status=error&message=DB_connection_failed");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$conn->begin_transaction();
$checkout_success = false;
$ono = null;

try {
    // Fetch cart items with price included
    $sql_cart = "SELECT c.dvd_id, c.quantity, d.price 
                 FROM cart c 
                 JOIN dvd d ON c.dvd_id = d.dvd_id 
                 WHERE c.customer_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("i", $customer_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();
    
    if ($result_cart->num_rows > 0) {
        // Calculate total amount
        $total_amount = 0;
        $cart_items = [];
        while ($row = $result_cart->fetch_assoc()) {
            $cart_items[] = $row;
            $total_amount += $row['price'] * $row['quantity'];
        }

        // Insert into orders
        $sql_order = "INSERT INTO orders (customer_id, order_date, shipping_status, total_amount) 
                      VALUES (?, CURDATE(), 'Shipped successfully', ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->bind_param("id", $customer_id, $total_amount);
        $stmt_order->execute();
        $ono = $conn->insert_id;
        $stmt_order->close();

        // Insert into odetails
        $sql_item = "INSERT INTO odetails (ono, dvd_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($sql_item);

        // Update stock
        $stmt_update_stock = $conn->prepare("UPDATE dvd SET stock = stock - ? WHERE dvd_id = ?");

        foreach ($cart_items as $cart_item) {
            // Insert order detail
            $stmt_item->bind_param("iiid", $ono, $cart_item['dvd_id'], $cart_item['quantity'], $cart_item['price']);
            $stmt_item->execute();

            // Update stock
            $stmt_update_stock->bind_param("ii", $cart_item['quantity'], $cart_item['dvd_id']);
            $stmt_update_stock->execute();
        }

        $stmt_item->close();
        $stmt_update_stock->close();

        // Clear cart
        $sql_clear_cart = "DELETE FROM cart WHERE customer_id = ?";
        $stmt_clear_cart = $conn->prepare($sql_clear_cart);
        $stmt_clear_cart->bind_param("i", $customer_id);
        $stmt_clear_cart->execute();
        $stmt_clear_cart->close();

        $conn->commit();
        $checkout_success = true;
    } else {
        $conn->rollback();
    }

    $stmt_cart->close();

} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    $checkout_success = false;
    error_log("Checkout failed: " . $e->getMessage());
}

if ($checkout_success) {
    // If user clicked "Check Out & Logout"
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        session_unset();
        session_destroy();
        header("Location: index.html?msg=Checkout complete. You have been logged out.");
        exit();
    } else {
        // Normal checkout → show receipt
        header("Location: receipt.php?ono=" . $ono);
        exit();
    }
} else {
    header("Location: view_cart.php?status=error&message=empty_cart");
    exit();
}

?>
