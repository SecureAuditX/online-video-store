<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    die("You must be logged in to view your order status.");
}

$servername = "localhost";
$username = "root";
$password = "SECRET";
$dbname = "shoppingline";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$customer_id = $_SESSION['customer_id'];

// Get all orders for the current customer, ordered by date.
$sql = "SELECT ono, order_date, shipping_status, total_amount FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Order Status</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #3e81d7;
            --accent-green: #2ecc71;
            --accent-red: #e74c3c;
            --light-bg: #f5f7fa;
            --dark-text: #333;
            --border-color: #dcdfe6;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
            padding: 2rem;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .order-container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            max-width: 900px;
            width: 100%;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.3s;
        }

        .status-shipped {
            color: var(--accent-green);
            font-weight: bold;
        }
        
        .status-pending {
            color: var(--accent-red);
            font-weight: bold;
        }

        a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Your Order History</h1>
    <div class="order-container">
        <?php if ($result->num_rows > 0): ?>
            <p>Click on an order number to see details.</p>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><a href="receipt.php?ono=<?php echo htmlspecialchars($row['ono']); ?>"><?php echo htmlspecialchars($row['ono']); ?></a></td>
                            <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td class="<?php echo ($row['shipping_status'] == 'Shipped') ? 'status-shipped' : 'status-pending'; ?>">
                                <?php echo htmlspecialchars($row['shipping_status']); ?>
                            </td>
                            <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no previous orders.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$result->free();
$stmt->close();
$conn->close();
?>
