<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];


session_unset();
session_destroy();

header("Location: index.html?msg=Your cart has been saved. You are now logged out.");
exit();
?>
