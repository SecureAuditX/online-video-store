<?php
session_start();

// Database connection
$servername = "HOST";
$username = "USERNAME";
$password_db = "PASSWORD";
$dbname = "shoppingline";
$port = "PORT";

$conn = new mysqli($servername, $username, $password_db, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and trim form data
$customer_no = trim($_POST['customer_no']);
$password = trim($_POST['password']);

// Prepare query
$stmt = $conn->prepare("SELECT customer_id, name, password FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_no);
$stmt->execute();
$result = $stmt->get_result();

function showError($message, $img = 'error.jpg') {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Login Error</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f8d7da;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
            }
            .error-container {
                background: #fff;
                border: 2px solid #f5c6cb;
                border-radius: 12px;
                padding: 30px 40px;
                text-align: center;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                max-width: 600px;
            }
            .error-container img {
                width: 200px;  /* enlarged */
                height: auto;
                margin-bottom: 20px;
            }
            .error-container p {
                font-size: 1.1rem;
                color: #721c24;
                margin-bottom: 20px;
            }
            .error-container a {
                display: inline-block;
                text-decoration: none;
                color: #fff;
                background-color: #d9534f;
                padding: 10px 20px;
                border-radius: 5px;
                font-weight: bold;
                transition: background 0.3s ease;
            }
            .error-container a:hover {
                background-color: #c82333;
            }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <img src='$img' alt='Error'>
            <p>$message</p>
            <a href='index.html'>Try Again</a>
        </div>
    </body>
    </html>
    ";
    exit();
}

// Usage:
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $db_password = $row['password'];

    if (password_verify($password, $db_password)) {
        $_SESSION['customer_id'] = $row['customer_id'];
        $_SESSION['customer_name'] = $row['name'];
        header("Location: customer_menu.php");
        exit();
    } elseif ($password === $db_password) {
        $new_hashed = password_hash($password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE customers SET password=? WHERE customer_id=?");
        $update_stmt->bind_param("si", $new_hashed, $customer_no);
        $update_stmt->execute();
        $update_stmt->close();

        $_SESSION['customer_id'] = $row['customer_id'];
        $_SESSION['customer_name'] = $row['name'];
        header("Location: customer_menu.php");
        exit();
    } else {
        showError("Invalid password!", "error.jpg"); // password error
    }
} else {
    showError("Invalid customer number!", "user.png"); // customer number error
}

$stmt->close();
$conn->close();
?>
