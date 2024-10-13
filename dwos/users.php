<?php
// Start the session only if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('dwos.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in admin's user_id
$user_id = $_SESSION['user_id'];

// Fetch Admin Details from the database using prepared statements
$stmt = $conn->prepare("SELECT user_name, image, password FROM users WHERE user_id = ? AND user_type = 'A'");
if (!$stmt) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    exit();
}
$stmt->bind_param("s", $user_id); // Changed to "s" assuming user_id is a string
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Error fetching admin details: " . $conn->error; 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="adminpage.css" />
    <title>Admin Page</title>
</head>
<body>
    <?php include 'adminnavbar.php'; ?>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="users.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get all modals
            const modals = document.querySelectorAll('.modal');

            // Open the modal when the button is clicked
            document.querySelectorAll('[data-modal]').forEach(button => {
                button.addEventListener('click', function () {
                    const modalId = this.getAttribute('data-modal');
                    document.getElementById(modalId).style.display = 'block';
                });
            });

            // Close the modal when the close button is clicked
            document.querySelectorAll('.close-button').forEach(button => {
                button.addEventListener('click', function () {
                    const modalId = this.getAttribute('data-close');
                    document.getElementById(modalId).style.display = 'none';
                });
            });

            // Close the modal when clicked outside the modal content
            modals.forEach(modal => {
                window.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="header">
        <h1>Users</h1>
    </div>

    <div class="users-section">
        <!-- Owners Container -->
        <div class="container" id="owners-container">
            <h2>Owner</h2>
            <?php
            // Database connection
            $servername = "localhost"; 
            $username = "root"; 
            $password = ""; 
            $dbname = "dwos";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch owners from the 'users' table where user_type is 'O' and status is 'A'
            $sql = "SELECT user_id, user_name, email FROM users WHERE user_type = 'O' AND status = 'A'";
            $result = $conn->query($sql);
            $ownerCount = 0;

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $user_name = !empty($row['user_name']) ? $row['user_name'] : $row['email'];
                    $ownerCount++;

                    // Show only the first 5 owners, hide the rest
                    $hiddenClass = $ownerCount > 5 ? 'hidden' : '';
                    echo "<div class='user {$hiddenClass}'><span class='user-id'>{$ownerCount}</span> {$user_name}</div>";
                }
            } else {
                echo "No owners found.";
            }
            ?>
            <div class="show-all">
                <button class="btn" data-modal="owners-modal">Show All</button>
            </div>
        </div>

        <!-- Modal for Owners -->
        <div id="owners-modal" class="modal">
            <div class="modal-content">
                <span class="close-button" data-close="owners-modal">&times;</span>
                <h2>ALL OWNERS</h2>
                <ul class="full-list">
                    <?php
                    // Fetch all owners from the 'users' table for the modal
                    $result = $conn->query($sql);  // Reusing the same SQL query to fetch owners
                    if ($result->num_rows > 0) {
                        $modalOwnerCount = 1;  // Resetting owner count for modal
                        while ($row = $result->fetch_assoc()) {
                            $user_name = !empty($row['user_name']) ? $row['user_name'] : $row['email'];
                            echo "<li><span class='home-id'>{$modalOwnerCount}.</span> {$user_name}</li>";
                            $modalOwnerCount++;
                        }
                    } else {
                        echo "<li>No owners found.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- Customers Container -->
        <div class="container" id="customers-container">
            <h2>Customer</h2>
            <?php
            // Fetch customers from the 'users' table where user_type is 'C' and status is 'A'
            $sql = "SELECT user_id, user_name, email FROM users WHERE user_type = 'C' AND status = 'A'";
            $result = $conn->query($sql);
            $customerCount = 0;

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $user_name = !empty($row['user_name']) ? $row['user_name'] : $row['email'];
                    $customerCount++;

                    // Show only the first 5 customers, hide the rest
                    $hiddenClass = $customerCount > 5 ? 'hidden' : '';
                    echo "<div class='user {$hiddenClass}'><span class='user-id'>{$customerCount}</span> {$user_name}</div>";
                }
            } else {
                echo "No customers found.";
            }

            $conn->close();
            ?>
            <div class="show-all">
                <button class="btn" data-modal="customers-modal">Show All</button>
            </div>
        </div>
    </div>

    <!-- Modal for Customers -->
    <div id="customers-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" data-close="customers-modal">&times;</span>
            <h2>ALL CUSTOMERS</h2>
            <ul class="full-list">
                <?php
                // Re-establish the connection to fetch all customers
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT user_id, user_name, email FROM users WHERE user_type = 'C' AND status = 'A'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $modalCustomerCount = 1;  // Resetting customer count for modal
                    while ($row = $result->fetch_assoc()) {
                        $user_name = !empty($row['user_name']) ? $row['user_name'] : $row['email'];
                        echo "<li><span class='home-id'>{$modalCustomerCount}.</span> {$user_name}</li>";
                        $modalCustomerCount++;
                    }
                } else {
                    echo "<li>No customers found.</li>";
                }

                // Close the connection after fetching
                $conn->close();
                ?>
            </ul>
        </div>
    </div>
</body>
</html>
