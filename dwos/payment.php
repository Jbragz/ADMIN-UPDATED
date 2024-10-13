<?php
session_start();

// Check if the user has selected a plan
if (!isset($_SESSION['user_data']['plan'])) {
    header('Location: subscription.php');
    exit();
}

// Handle form submission for payment
if (isset($_POST['pay'])) {
    // Store selected payment method in session
    $_SESSION['user_data']['payment_method'] = $_POST['payment_method'];
    header('Location: paymentconfirmation.php');
    exit();
}

$selected_plan = $_SESSION['user_data']['plan']; // Retrieve the selected plan
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
</head>
<body>
    <div class="payment-container">
        <h3>Choose Your Payment Method</h3>
        
        <!-- Display the selected plan details -->
        <p>You have selected: 
            <?php echo "{$selected_plan['duration_in_days']} days for ₱{$selected_plan['price']}"; ?>
        </p>

        <form action="" method="post">
            <div class="plan">
                <input type="radio" id="gcash" name="payment_method" value="Gcash" required>
                <label for="gcash">Gcash</label>
            </div>
            <div class="plan">
                <input type="radio" id="bank_transfer" name="payment_method" value="Bank Transfer">
                <label for="bank_transfer">Bank Transfer</label>
            </div>
            <div class="button-container">
                <input type="submit" name="pay" value="Proceed to Payment" class="form-btn">
            </div>
        </form>
    </div>
</body>
</html>
