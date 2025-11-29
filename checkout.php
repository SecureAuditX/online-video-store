<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$servername = "localhost";
$username = "root";
$password = "SECRET";
$dbname = "shoppingline";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch customer details
$sql_customer = "SELECT name, street, city, state, zip FROM customers WHERE customer_id = ?";
$stmt_customer = $conn->prepare($sql_customer);
$stmt_customer->bind_param("i", $customer_id);
$stmt_customer->execute();
$result_customer = $stmt_customer->get_result();
$customer_info = $result_customer->fetch_assoc();
$stmt_customer->close();

// Fetch cart items
$sql_cart = "SELECT d.title, d.price, c.quantity 
             FROM cart c 
             JOIN dvd d ON c.dvd_id = d.dvd_id 
             WHERE c.customer_id = ?";
$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param("i", $customer_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();
$cart_items = $result_cart->fetch_all(MYSQLI_ASSOC);
$stmt_cart->close();

// Calculate total cost
$total_cost = 0;
foreach ($cart_items as $item) {
    $total_cost += ($item['price'] * $item['quantity']);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>
<style>
/* General Reset */
* { box-sizing: border-box; margin: 0; padding: 0; }

/* Body & Background */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #ede4f7ff, #2575fc);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

/* Container */
.container {
    background: #fff;
    border-radius: 20px;
    padding: 40px;
    width: 100%;
    max-width: 650px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    transition: transform 0.3s, box-shadow 0.3s;
}

.container:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.25);
}

/* Heading */
h1 {
    text-align: center;
    color: #4B0082;
    font-size: 2rem;
    margin-bottom: 30px;
}

/* Sections */
.section {
    background: #f7f9fc;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: #2575fc;
    border-bottom: 2px solid #6a11cb;
    padding-bottom: 5px;
}

/* Order Items */
.item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #ddd;
    transition: background 0.2s;
}

.item:last-child {
    border-bottom: none;
}

.item:hover {
    background: #eaf6ff;
    border-radius: 8px;
    padding-left: 10px;
}

/* Total */
.total {
    font-size: 1.7rem;
    font-weight: bold;
    text-align: right;
    color: #28a745;
    margin-top: 20px;
}

/* Pay Button */
.pay-btn-container {
    text-align: center;
    margin-top: 30px;
}

.pay-btn {
    background: linear-gradient(90deg, #28a745, #218838);
    color: #fff;
    padding: 15px 35px;
    font-size: 1.2rem;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 700;
    transition: all 0.3s ease;
}

.pay-btn:hover {
    background: linear-gradient(90deg, #218838, #28a745);
    transform: scale(1.05);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

/* Responsive */
@media (max-width: 700px) {
    .container {
        padding: 30px 20px;
    }
    .total {
        font-size: 1.4rem;
    }
    .pay-btn {
        padding: 12px 25px;
        font-size: 1rem;
    }
}
</style>
</head>
<body>
<div class="container">
    <h1>Confirm Your Order</h1>
    
    <div class="section">
        <div class="section-title">Shipping Address</div>
        <p><?php echo htmlspecialchars($customer_info['name']); ?></p>
        <p><?php echo htmlspecialchars($customer_info['street']); ?></p>
        <p><?php echo htmlspecialchars($customer_info['city']) . ', ' . htmlspecialchars($customer_info['state']) . ' ' . htmlspecialchars($customer_info['zip']); ?></p>
    </div>

    <div class="section">
        <div class="section-title">Order Summary</div>
        <?php foreach ($cart_items as $item) : ?>
            <div class="item">
                <span><?php echo htmlspecialchars($item['title']); ?> (x<?php echo htmlspecialchars($item['quantity']); ?>)</span>
                <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="total">
        Total: $<?php echo number_format($total_cost, 2); ?>
    </div>
    
    <div class="pay-btn-container">
        <form action="process_checkout.php" method="post">
            <button type="submit" class="pay-btn">Pay & Checkout</button>
        </form>
    </div>
</div>
</body>
</html>
