<?php
// PHP script to process new customer sign-up
session_start();

// Database connection parameters
$servername = "HOST";
$username = "USERNAME"; 
$password = "PASSWORD"; 
$dbname = "shoppingline";
$port = "PORT";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$street = $_POST['street'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = $_POST['password'];

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare and bind SQL statement
$stmt = $conn->prepare("INSERT INTO customers (name, street, city, state, zip, phone, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $name, $street, $city, $state, $zip, $phone, $email, $hashed_password);

if ($stmt->execute()) {
    echo "<h1>Registration Successful!</h1>";
    echo "<p>Your customer number is: " . $stmt->insert_id . "</p>";
    echo "<a href='index.html'>Go back to login page</a>";
} else {
    echo "Error: Invalid credential, try again" . $stmt->error;
}

$stmt->close();
$conn->close();
?>
