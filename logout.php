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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if cart has items
$sql = "SELECT COUNT(*) as count FROM cart WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$cart_has_items = ($row['count'] > 0);

$stmt->close();
$conn->close();

// Auto logout after checkout/receipt
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.html?msg=Checkout complete. You have been logged out.");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logout Options</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container {
        background: #fff;
        padding: 40px 30px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        text-align: center;
        width: 100%;
        max-width: 500px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .container:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    h1 {
        color: #333;
        font-size: 2rem;
        margin-bottom: 10px;
    }

    p {
        color: #555;
        font-size: 1.1rem;
        margin-bottom: 30px;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    li {
        margin: 15px 0;
    }

    a.logout-btn {
        display: inline-block;
        text-decoration: none;
        color: #fff;
        background: #2575fc;
        padding: 12px 25px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    a.logout-btn:hover {
        background: #6a11cb;
        transform: scale(1.05);
    }

    @media (max-width: 480px) {
        .container {
            padding: 30px 20px;
        }
        h1 {
            font-size: 1.5rem;
        }
        a.logout-btn {
            padding: 10px 20px;
            font-size: 1rem;
        }
    }
</style>
</head>
<body>
<div class="container">
    <?php if ($cart_has_items): ?>
        <h1>Logout Options</h1>
        <p>Your cart is not empty. What would you like to do?</p>
        <ul>
            <li><a class="logout-btn" href="checkout.php?action=logout">Check Out & Logout</a></li>
            <li><a class="logout-btn" href="save_and_logout.php">Save Cart & Logout</a></li>
            <li><a class="logout-btn" href="empty_and_logout.php">Empty Cart & Logout</a></li>
        </ul>
    <?php else: 
        // Cart empty, auto logout
        session_unset();
        session_destroy();
        header("Location: index.html?msg=You have been logged out.");
        exit();
    endif; ?>
</div>
</body>
</html>
