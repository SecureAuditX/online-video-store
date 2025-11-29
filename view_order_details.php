<?php
session_start();

// Check if the user is logged in and an order number is provided
if (!isset($_SESSION['customer_id']) || !isset($_GET['ono'])) {
    die("Invalid request. Please select an order from your history.");
}

$customer_id = $_SESSION['customer_id'];
$ono = (int)$_GET['ono'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "SECRET";
$dbname = "shoppingline";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order items with product info
$sql = "SELECT oi.pno, p.pname, oi.price, oi.quantity
        FROM order_items oi
        JOIN products p ON oi.pno = p.pno
        JOIN orders o ON oi.ono = o.ono
        WHERE oi.ono = ? AND o.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $ono, $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$total_cost = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Order #<?php echo htmlspecialchars($ono); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; }
        h1 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background-color: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #eaf6ff; }
    </style>
</head>
<body>
    <h1>Order #<?php echo htmlspecialchars($ono); ?> Details</h1>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Product #</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()):
                    $subtotal = $row['price'] * $row['quantity'];
                    $total_cost += $subtotal;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['pno']); ?></td>
                        <td><?php echo htmlspecialchars($row['pname']); ?></td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                <?php endwhile; ?>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Total:</td>
                    <td>$<?php echo number_format($total_cost, 2); ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>No details found for this order.</p>
    <?php endif; ?>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
