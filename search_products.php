<?php
session_start();

// Display status message from add_to_cart.php
$status_message = '';
if (isset($_GET['status'])) {
    $status_message = htmlspecialchars($_GET['status']);
    if ($status_message == 'success') {
        $status_message = 'Product added to cart!';
    } else if ($status_message == 'error') {
        $status_message = 'Failed to add product to cart. Please try again.';
    } else if ($status_message == 'Invalid_quantity') {
        $status_message = 'Invalid quantity selected. Please enter a number greater than 0.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galactic DVD Archives</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
       :root {
            --primary-bg: #e0e0e0ff;
            --secondary-bg: #f5f5f5;
            --accent-color: #e94560;
            --text-color: #333;
            --border-color: #ccc;
            --hover-glow: 0 0 10px #e94560, 0 0 20px #e94560;
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
            color: #4B0082;
            margin-bottom: 2rem;
            font-weight: 700;
            transition: text-shadow 0.3s ease-in-out;
        }

        h1:hover {
            text-shadow: #4B0082;
        }

        h2 {
            text-align: center;
            font-size: 1.8rem;
            color: var(--text-color);
            margin-bottom: 1rem;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
            width: 100%;
        }

        .search-form input[type="text"] {
            flex-grow: 1;
            padding: 12px;
            background-color: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: var(--text-color);
            transition: all 0.3s ease-in-out;
            max-width: 400px;
        }

        .search-form input[type="text"]:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: var(--hover-glow);
        }

        .search-form input[type="submit"] {
            padding: 12px 25px;
            background-color: var(--accent-color);
            color: #ffffffff; /* Changed text color to white for readability */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-transform: uppercase;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 0 5px var(--accent-color);
        }

        .search-form input[type="submit"]:hover {
            background-color: #ff6685;
            box-shadow: var(--hover-glow);
            transform: translateY(-2px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--secondary-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
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

        a {
            color: var(--accent-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        a:hover {
            color: #ff6685;
            text-decoration: underline;
        }

        .message-box {
            padding: 15px;
            margin: 20px auto;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }
        
        .message-success {
            background-color: #28a745;
            color: #d4edda;
            border: 1px solid #1e7e34;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }

        .message-error {
            background-color: #dc3545;
            color: #f8d7da;
            border: 1px solid #bd2130;
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
        }
        
        p {
            text-align: center;
            font-style: italic;
            color: #aaa;
        }

        /* Styling for the new add to cart form */
        .add-to-cart-form {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: flex-end; /* Aligns content to the right */
        }

        .add-to-cart-form input[type="number"] {
            width: 60px;
            padding: 8px;
            background-color: var(--primary-bg);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .add-to-cart-form input[type="number"]:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        .add-to-cart-form input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .add-to-cart-form input[type="submit"]:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Galactic DVD Archives</h1>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
    
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
    <p style="text-align:center; color: red; font-weight: bold;">
        Error: <?php echo htmlspecialchars($_GET['message']); ?>
    </p>
<?php endif; ?>

        
        <?php
        // Display the status message with the new styling
        if ($status_message) {
            $message_class = strpos(strtolower($status_message), 'error') !== false || strpos(strtolower($status_message), 'failed') !== false ? 'message-error' : 'message-success';
            echo "<div class='message-box {$message_class}'>{$status_message}</div>";
        }
        ?>
        
        <div class="search-form">
            <form action="search_products.php" method="get">
                <input type="text" name="keyword" placeholder="Search the galaxy..." value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" required>
                <input type="submit" value="Initiate Search">
            </form>
        </div>

        <?php
        if (isset($_GET['keyword'])) {
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
            
            $keyword = "%" . $conn->real_escape_string($_GET['keyword']) . "%";
            $sql = "SELECT dvd_id, title, price, stock FROM dvd WHERE LOWER(title) LIKE LOWER(?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $keyword);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
    echo "<h2>Search Results:</h2>";
    echo "<table>";
    echo "<thead><tr><th>DVD #</th><th>Title</th><th>Price</th><th>Stock</th><th>Add to Cart</th></tr></thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['dvd_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>$" . htmlspecialchars(number_format($row['price'], 2)) . "</td>";
        echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
        echo "<td>";
        echo "<form action='add_to_cart.php' method='post' class='add-to-cart-form'>";
        echo "<input type='hidden' name='dvd_id' value='" . htmlspecialchars($row['dvd_id']) . "'>";
        echo "<input type='number' name='quantity' value='1' min='1' max='" . htmlspecialchars($row['stock']) . "' required>";
        echo "<input type='submit' value='Add to Cart'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>No DVDs found in the archives that match your search query.</p>";
}


            $stmt->close();
            $conn->close();
        }
        ?>
    </div>
</body>
</html>