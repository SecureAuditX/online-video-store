<?php
session_start();
$customer_name = isset($_SESSION['customer_name']) ? $_SESSION['customer_name'] : "Guest";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Menu</title>
<style>
    /* Reset and body */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #d5b9f4ff, #2575fc);
        min-height: 100vh;
        color: #333;
        display: flex;
        flex-direction: column;
    }

    /* Header */
    .header {
        text-align: center;
        padding: 25px 20px;
        background: rgba(255,255,255,0.95);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
    }

    .header h1 {
        color: #4B0082;
        font-size: 2rem;
    }

    .header p {
        margin-top: 5px;
        font-size: 1.2rem;
        color: #555;
    }

    /* Main container */
    .main-container {
        display: flex;
        flex-grow: 1;
        margin: 20px;
        gap: 20px;
    }

    /* Menu frame */
    .menu-frame {
        width: 260px;
        background: #ffffffff;
        padding: 25px 20px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .menu-frame h3 {
        color: #4B0082;
        margin-bottom: 20px;
        font-size: 1.5rem;
        border-bottom: 2px solid #6a11cb;
        padding-bottom: 10px;
        width: 100%;
        text-align: center;
    }

    .menu-frame a {
        display: block;
        width: 100%;
        margin: 12px 0;
        padding: 12px 15px;
        text-decoration: none;
        color: #ffffffff;
        font-size: 1rem;
        text-align: center;
        border-radius: 10px;
        background: linear-gradient(90deg, #6a11cb, #2575fc);
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .menu-frame a:hover {
        transform: translateX(5px) scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        background: linear-gradient(90deg, #2575fc, #6a11cb);
    }

    /* Content frame */
    .content-frame {
        flex-grow: 1;
        background: #eaa4faff;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        overflow: hidden;
    }

    .content-frame iframe {
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 15px;
    }

    /* Responsive adjustments */
    @media (max-width: 900px) {
        .main-container {
            flex-direction: column;
            margin: 10px;
        }
        .menu-frame {
            width: 100%;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
        }
        .menu-frame a {
            width: 48%;
            margin: 6px 1%;
        }
        .content-frame {
            margin-top: 20px;
            height: 500px;
        }
    }
</style>
</head>
<body>
    <div class="header">
        <h1>Web Shopping Application System</h1>
        <p>Welcome, <span id="customer-name"><?php echo htmlspecialchars($customer_name); ?></span></p>
    </div>
    <div class="main-container">
        <div class="menu-frame">
            <h3>Customer Menu</h3>
            <a href="search_products.php" target="content-frame">Search by Keyword</a>
            <a href="view_cart.php" target="content-frame">View/Edit Cart</a>
            <a href="update_profile.html" target="content-frame">Update Profile</a>
            <a href="check_order_status.php" target="content-frame">Check Order Status</a>
            <a href="checkout.php" target="content-frame">Check Out</a>
            <a href="logout.php">Logout</a>
        </div>
        <div class="content-frame">
            <iframe name="content-frame" src="search_products.php"></iframe>
        </div>
    </div>
</body>
</html>
