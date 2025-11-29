<?php
session_start();

// Initialize status message variables
$status_message = '';
$message_type = '';

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    die("You must be logged in to update your profile.");
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "SECRET";
$dbname = "shoppingline";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the customer ID from the session
$customer_id = $_SESSION['customer_id'];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $name = $_POST['name'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Update query with a prepared statement to prevent SQL injection
    $sql = "UPDATE customers SET name=?, street=?, city=?, state=?, zip=?, phone=?, email=?";
    $params = "sssssss";
    $bind_array = array($name, $street, $city, $state, $zip, $phone, $email);

    // If a new password is provided, update it as well
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password=?";
        $params .= "s";
        $bind_array[] = $hashed_password;
    }

    $sql .= " WHERE customer_id = ?";
    $params .= "i";
    $bind_array[] = $customer_id;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($params, ...$bind_array);

    if ($stmt->execute()) {
        $status_message = "Profile Updated Successfully!";
        $message_type = "success";
    } else {
        $status_message = "Error updating profile: " . $stmt->error;
        $message_type = "error";
    }

    $stmt->close();
}

// Fetch current user data to pre-fill the form
$sql_fetch = "SELECT name, street, city, state, zip, phone, email FROM customers WHERE customer_id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("i", $customer_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
$row = $result->fetch_assoc();
$stmt_fetch->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Your Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #3e81d7;
            --accent-green: #2ecc71;
            --accent-red: #e74c3c;
            --light-bg: #f5f7fa;
            --dark-text: #333;
            --border-color: #dcdfe6;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
            padding: 2rem;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .profile-container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            max-width: 600px;
            width: 100%;
        }

        .profile-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
        }

        .form-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .form-buttons input[type="submit"],
        .form-buttons button {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .form-buttons input[type="submit"] {
            background-color: var(--accent-green);
            color: white;
        }

        .form-buttons button {
            background-color: var(--accent-red);
            color: white;
        }

        .form-buttons input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(46, 204, 113, 0.3);
        }

        .form-buttons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
        }

        .status-message {
            text-align: center;
            margin-bottom: 1.5rem;
            font-style: italic;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
        }

        .status-message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .status-message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Responsive adjustments */
        @media (min-width: 480px) {
            .profile-form {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <h1>Update Your Profile</h1>
    <div class="profile-container">
        <?php if ($status_message): ?>
            <p class="status-message <?php echo htmlspecialchars($message_type); ?>">
                <?php echo htmlspecialchars($status_message); ?>
            </p>
        <?php endif; ?>

        <?php if ($row): ?>
        <form action="update_profile.php" method="post">
            <div class="profile-form">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="street">Street:</label>
                    <input type="text" id="street" name="street" value="<?php echo htmlspecialchars($row['street']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($row['city']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="state">State:</label>
                    <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($row['state']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="zip">Zip:</label>
                    <input type="text" id="zip" name="zip" value="<?php echo htmlspecialchars($row['zip']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">New Password:</label>
                    <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                </div>
            </div>
            <div class="form-buttons">
                <input type="submit" value="Update Profile">
                <button type="button" id="reset-button">Reset</button>
            </div>
        </form>
        <?php else: ?>
            <p class="status-message error">User not found.</p>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const resetButton = document.getElementById('reset-button');
            const formInputs = document.querySelectorAll('.profile-form input');

            if (resetButton) {
                resetButton.addEventListener('click', () => {
                    formInputs.forEach(input => {
                        if (input.type === 'text' || input.type === 'password') {
                            input.value = '';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>