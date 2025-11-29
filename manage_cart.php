<?php
session_start();

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view_cart.php");
    exit();
}

// Check for required data
if (!isset($_POST['dvd_id']) || !isset($_SESSION['customer_id'])) {
    header("Location: view_cart.php?status=error&message=Missing_data");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$dvd_id = (int)$_POST['dvd_id'];

// Database connection details
$servername = "localhost";
$username = "root";
$password = "SECRET";
$dbname = "shoppingline";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a new quantity was submitted
if (isset($_POST['quantity']) && (int)$_POST['quantity'] > 0) {
    // UPDATE item's quantity
    $quantity = (int)$_POST['quantity'];
    $sql = "UPDATE cart SET quantity = ? WHERE customer_id = ? AND dvd_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $customer_id, $dvd_id);
    $action = 'updated';
} else {
    // DELETE item from cart
    $sql = "DELETE FROM cart WHERE customer_id = ? AND dvd_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customer_id, $dvd_id);
    $action = 'deleted';
}

$stmt->execute();
$stmt->close();
$conn->close();

// Redirect back to the cart page to show the changes
header("Location: view_cart.php?status=success&action=$action");
exit();
?>
