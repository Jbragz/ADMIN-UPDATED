<?php
session_start();

if (!isset($_SESSION['user_data']['payment_method'])) {
    header('Location: payment.php');
    exit();
}

include('dwos.php');

// Ensure the plan is set in session
if (!isset($_SESSION['user_data']['plan'])) {
    header('Location: subscription.php'); // Redirect if no plan is set
    exit();
}

// Access the plan and payment method from session
$plan = $_SESSION['user_data']['plan'];
$payment_method = $_SESSION['user_data']['payment_method'];

// Handle payment confirmation
if (isset($_POST['confirm_payment'])) {
    // Retrieve user and plan data from session
    $name = $_SESSION['user_data']['user_name'] ?? '';
    $email = $_SESSION['user_data']['email'] ?? '';
    $user_type = $_SESSION['user_data']['user_type'] ?? ''; // 'O' for owner, 'C' for customer
    $station_name = $_SESSION['user_data']['station_name'] ?? '';
    $station_address = $_SESSION['user_data']['station_address'] ?? '';
    $station_owner_id = $_SESSION['user_data']['user_id'] ?? '';

    // Validate station details (for owner)
    if ($user_type === 'O' && (empty($station_name) || empty($station_address))) {
        echo 'Station details are incomplete.';
        exit();
    }

    // Determine the subscription_type based on the user type
    $subscription_type = ($user_type === 'O') ? 'O' : 'C';

    // Use prepared statements for security
    $stmt_subscription = $conn->prepare("INSERT INTO subscriptions (user_id, membership_id, subscription_type, start_date, end_date, payment_method, membership_for) 
                                          VALUES ((SELECT user_id FROM users WHERE email = ?), ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), ?, 'O')");
    $stmt_subscription->bind_param("sisss", $email, $plan['membership_id'], $subscription_type, $plan['duration_in_days'], $payment_method);

    // Insert into the stations table only if the user is an owner
    if ($user_type === 'O') {
        $stmt_station = $conn->prepare("INSERT INTO stations (owner_id, station_name, address, subscription_status, membership_id) 
                                         VALUES (?, ?, ?, 'A', ?)");
        $stmt_station->bind_param("issi", $station_owner_id, $station_name, $station_address, $plan['membership_id']);
    }

    // Execute both inserts and check success
    $subscription_success = $stmt_subscription->execute();
    $station_success = ($user_type === 'O') ? $stmt_station->execute() : true;

    if ($subscription_success && $station_success) {
        // Destroy session after successful insertion
        session_destroy();
        header('Location: login.php');
        exit();
    } else {
        echo 'Error: ' . $stmt_subscription->error;
        if ($user_type === 'O') {
            echo ' ' . $stmt_station->error;
        }
    }

    // Close the prepared statements
    $stmt_subscription->close();
    if ($user_type === 'O') {
        $stmt_station->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Payment</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
</head>
<body>
    <div class="confirmation-container">
        <h3>Confirm Your Payment</h3>
        <p>You have selected the <?php echo htmlspecialchars($plan['duration_in_days']); ?> days plan for ₱<?php echo htmlspecialchars($plan['price']); ?>.</p>
        <p>Payment Method: <?php echo htmlspecialchars($payment_method); ?></p>
        <form action="" method="post">
            <div class="button-container">
                <input type="submit" name="confirm_payment" value="Confirm Payment" class="form-btn">
            </div>
        </form>
    </div>
</body>
</html>
