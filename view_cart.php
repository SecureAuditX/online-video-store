<?php
session_start();

// Check if customer is logged in. If not, redirect to login page.
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

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch customer address details for display
$sql_customer = "SELECT name, street, city, state, zip FROM customers WHERE customer_id = ?";
$stmt_customer = $conn->prepare($sql_customer);
$stmt_customer->bind_param("i", $customer_id);
$stmt_customer->execute();
$result_customer = $stmt_customer->get_result();
$customer_info = $result_customer->fetch_assoc();
$stmt_customer->close();

// Fetch cart items to display (join with dvd table)
$sql_cart = "SELECT d.dvd_id, d.title, d.price, c.quantity 
             FROM cart c 
             JOIN dvd d ON c.dvd_id = d.dvd_id 
             WHERE c.customer_id = ?";
$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param("i", $customer_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

$total_cost = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #e0e0e0;
            --secondary-bg: #f5f5f5;
            --accent-color: #e94560;
            --text-color: #333;
            --border-color: #ccc;
        }

        body {
            font-family: 'Roboto Mono', monospace;
            background-color: var(--primary-bg);
            color: var(--text-color);
            padding: 2rem;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 900px;
        }

        h1 {
            text-align: center;
            font-size: 3rem;
            letter-spacing: 5px;
            color: var(--accent-color);
            text-shadow: 0 0 15px rgba(233, 69, 96, 0.7), 0 0 30px rgba(233, 69, 96, 0.5);
            margin-bottom: 2rem;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--secondary-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid var(--border-color);
            padding: 15px;
            text-align: left;
            transition: background-color 0.3s ease;
        }

        th {
            background-color: #3f3f5a;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .total-row td {
            font-weight: bold;
            text-align: right;
            border-top: 2px solid #000;
        }
        
        .checkout-container {
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
        }

        .checkout-btn {
            padding: 15px 30px;
            font-size: 1.2rem;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-transform: uppercase;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .checkout-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .action-form {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quantity-input {
            width: 50px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            text-align: center;
        }
        
        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: bold;
        }

        .modify-btn {
            background-color: #007bff;
        }

        .modify-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .delete-btn {
            background-color: #dc3545;
        }

        .delete-btn:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .action-cell {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Shopping Cart</h1>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
    <p style="text-align:center; color: green; font-weight: bold;">
        Item successfully <?php echo htmlspecialchars($_GET['action']); ?>.
    </p>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
    <p style="text-align:center; color: red; font-weight: bold;">
        Error: <?php echo htmlspecialchars($_GET['message']); ?>
    </p>
<?php endif; ?>

        
        <?php if ($result_cart->num_rows > 0) : ?>
        <table>
            <thead>
                <tr>
                    <th>DVD #</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result_cart->fetch_assoc()) :
                $subtotal = $row['price'] * $row['quantity'];
                $total_cost += $subtotal;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['dvd_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td>$<?php echo htmlspecialchars(number_format($row['price'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td>$<?php echo htmlspecialchars(number_format($subtotal, 2)); ?></td>
                    <td class="action-cell">
                        <form class="action-form" action="manage_cart.php" method="post">
                            <input type="hidden" name="dvd_id" value="<?php echo htmlspecialchars($row['dvd_id']); ?>">
                            <input type="number" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" min="1" class="quantity-input">
                            <button type="submit" class="action-btn modify-btn">Modify</button>
                        </form>
                        <form class="action-form" action="manage_cart.php" method="post" style="margin-top: 5px;">
                            <input type="hidden" name="dvd_id" value="<?php echo htmlspecialchars($row['dvd_id']); ?>">
                            <input type="hidden" name="quantity" value="0">
                            <button type="submit" class="action-btn delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            <tr class="total-row">
                <td colspan="5">Total Cost:</td>
                <td>$<?php echo htmlspecialchars(number_format($total_cost, 2)); ?></td>
            </tr>
            </tbody>
        </table>
        
        <div class="checkout-container">
            <h3>Shipping To:</h3>
            <p><?php echo htmlspecialchars($customer_info['name']); ?></p>
            <p><?php echo htmlspecialchars($customer_info['street']); ?><br><?php echo htmlspecialchars($customer_info['city']) . ', ' . htmlspecialchars($customer_info['state']) . ' ' . htmlspecialchars($customer_info['zip']); ?></p>

            <form action="checkout.php" method="post">
                <input type="hidden" name="total_cost" value="<?php echo htmlspecialchars($total_cost); ?>">
                <button type="submit" class="checkout-btn">Proceed to Checkout</button>
            </form>
        </div>
        
        <?php else : ?>
            <p style="text-align:center;">Your shopping cart is currently empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>
