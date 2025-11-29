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

//  Delete all cart items for this user
$sql = "DELETE FROM cart WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();

$stmt->close();
$conn->close();

//  Logout after emptying cart
session_unset();
session_destroy();

header("Location: index.html?msg=Your cart was emptied. You are now logged out.");
exit();
?>
