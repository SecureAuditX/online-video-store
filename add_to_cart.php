<?php
session_start();

// Ensure POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: search_products.php");
    exit();
}

// Ensure logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Validate inputs
if (!isset($_POST['dvd_id']) || !isset($_POST['quantity'])) {
    header("Location: search_products.php?status=error&message=Invalid_request");
    exit();
}

$dvd_id = (int)$_POST['dvd_id'];
$quantity = (int)$_POST['quantity'];
$customer_id = $_SESSION['customer_id'];

if ($quantity <= 0) {
    header("Location: search_products.php?status=error&message=Invalid_quantity");
    exit();
}

// DB connection
$conn = new mysqli("HOST", "USERNAME", "PASSWORD", "shoppingline", "PORT");
if ($conn->connect_error) {
    header("Location: search_products.php?status=error&message=Database_connection_failed");
    exit();
}

//  Check stock
$sql_stock = "SELECT stock FROM dvd WHERE dvd_id = ?";
$stmt_stock = $conn->prepare($sql_stock);
$stmt_stock->bind_param("i", $dvd_id);
$stmt_stock->execute();
$result_stock = $stmt_stock->get_result();
if ($result_stock->num_rows === 0) {
    header("Location: search_products.php?status=error&message=DVD_not_found");
    exit();
}
$dvd = $result_stock->fetch_assoc();
if ($quantity > $dvd['stock']) {
    header("Location: search_products.php?status=error&message=Not_enough_stock");
    exit();
}
$stmt_stock->close();

//  Check if already in cart
$sql_check = "SELECT quantity FROM cart WHERE customer_id = ? AND dvd_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $customer_id, $dvd_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;

    $sql_update = "UPDATE cart SET quantity = ? WHERE customer_id = ? AND dvd_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iii", $new_quantity, $customer_id, $dvd_id);
    $stmt_update->execute();
    $stmt_update->close();
} else {
    $sql_insert = "INSERT INTO cart (customer_id, dvd_id, quantity) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iii", $customer_id, $dvd_id, $quantity);
    $stmt_insert->execute();
    $stmt_insert->close();
}

$stmt_check->close();
$conn->close();

//  Redirect with success
header("Location: search_products.php?status=success");
exit();
?>
