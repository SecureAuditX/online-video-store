<?php
session_start();

if (!isset($_GET['ono'])) {
    die("No order number specified.");
}



$ono = (int)$_GET['ono'];

$servername = "HOST";
$username = "USERNAME";
$password = "PASSWORD";
$dbname = "shoppingline";
$port = "PORT";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//  Fetch order details including customer info and total_amount
$sql_order = "SELECT o.order_date, o.shipping_status, o.total_amount, 
                     c.name, c.street, c.city, c.state, c.zip
              FROM orders o
              JOIN customers c ON o.customer_id = c.customer_id
              WHERE o.ono = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $ono);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
$order_details = $result_order->fetch_assoc();
$stmt_order->close();

if (!$order_details) {
    die("Order not found.");
}

// ✅ Fetch order items for the receipt table
$sql_items = "SELECT d.dvd_id, d.title, od.price, od.quantity
              FROM odetails od
              JOIN dvd d ON od.dvd_id = d.dvd_id
              WHERE od.ono = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $ono);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
$stmt_items->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Order #<?php echo htmlspecialchars($ono); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* General body styling */
/* General body styling */
body {
    font-family: 'Roboto Mono', monospace;
    color: #333;
    background-color: #f5f6fa;
    margin: 0;
    padding: 20px;
}

/* Container for the receipt */
.container {
    max-width: 950px;
    margin: 2rem auto;
    background-color: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

/* Header styling */
.header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #3498db;
}

.header h1 {
    font-size: 2.2rem;
    margin: 0;
    color: #2c3e50;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Address + Order details container */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 2rem;
}

/* Address box styling */
.address-box {
    background-color: #f1f3f6;
    padding: 15px 20px;
    border-radius: 8px;
    border-left: 5px solid #3498db;
}

.address-box h3 {
    margin-top: 0;
    margin-bottom: 8px;
    font-size: 1rem;
    color: #2c3e50;
}

.address-box p {
    margin: 4px 0;
    color: #555;
    font-size: 0.95rem;
}

/* Order details styling */
.order-details {
    background: #fdfdfd;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px 20px;
}

.order-details p {
    margin: 6px 0;
    font-weight: 500;
    color: #34495e;
}

/* Table styling */
.invoice-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
    border-radius: 8px;
    overflow: hidden;
}

.invoice-table th, .invoice-table td {
    border: 1px solid #ddd;
    padding: 12px 14px;
    text-align: left;
}

.invoice-table th {
    background-color: #3498db;
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.invoice-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.invoice-table tr:hover {
    background-color: #eef6fb;
}

/* Total box styling */
.total-cost-box {
    text-align: right;
    font-weight: bold;
    margin-top: 1rem;
    font-size: 1.4rem;
    color: #fff;
    background: #27ae60;
    padding: 15px 20px;
    border-radius: 8px;
}

/* Print button styling */
.print-btn {
    display: block;
    margin: 2rem auto;
    padding: 14px 28px;
    font-size: 1rem;
    cursor: pointer;
    border: none;
    background: linear-gradient(135deg, #27ae60, #219150);
    color: #fff;
    border-radius: 30px;
    font-weight: 600;
    transition: 0.3s ease-in-out;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.print-btn:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #219150, #1e874b);
}

/* Print message styling */
.print-message {
    text-align: center;
    font-style: italic;
    color: #7f8c8d;
    margin-top: 2rem;
}

/* Print media query: hide button and simplify layout */
@media print {
    .print-btn, .print-message {
        display: none;
    }
    body {
        background-color: #fff;
        margin: 0;
        padding: 0;
    }
    .container {
        box-shadow: none;
        border-radius: 0;
        border: none;
        padding: 0;
    }
}


    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice for <?php echo htmlspecialchars($order_details['name']); ?></h1>
    </div>

    <div class="address-box">
    <h3>Shipping Address:</h3>
    <p><?php echo htmlspecialchars($order_details['street']); ?></p>
    <p><?php echo htmlspecialchars($order_details['city']) . ', ' . htmlspecialchars($order_details['state']) . ' ' . htmlspecialchars($order_details['zip']); ?></p>
</div>

<p class="order-details">Order Number: <?php echo htmlspecialchars($ono); ?></p>
<p class="order-details">Order Date: <?php echo htmlspecialchars($order_details['order_date']); ?></p>
<p class="order-details">Shipping Status: <?php echo htmlspecialchars($order_details['shipping_status']); ?></p>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>DVD ID</th>
                <th>Title</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $result_items->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['dvd_id']); ?></td>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td>$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>$<?php echo htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <div class="total-cost-box">
        Total Cost: $<?php echo htmlspecialchars(number_format($order_details['total_amount'], 2)); ?>
    </div>

    <button class="print-btn" onclick="window.print()">Print Invoice</button>

    <p class="print-message">Please print a copy of the invoice for your records.</p>

<script>
// Wait for the page to fully load
window.addEventListener('load', function() {
    // Open the print dialog
    window.print();

    // After printing, redirect to logout.php to destroy session
    setTimeout(function() {
        window.location.href = "logout.php";
    }, 1000); // 1 second delay to ensure print dialog completes
});
</script>


</body>
</html>