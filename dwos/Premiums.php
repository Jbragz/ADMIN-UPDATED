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
$user_id = $_SESSION['user_id']; // This should now be set

// Fetch Admin Details from the database
$query = "SELECT * FROM users WHERE user_id = '$user_id' AND user_type = 'A'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result); // Get user details as an associative array
} else {
    echo "Error fetching admin details"; // Error handling
    exit();
}

// Fetch memberships from the database
$query = "SELECT * FROM memberships"; // Adjust the query as needed
$result = mysqli_query($conn, $query); // Use $conn instead of $connection

$memberships = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $memberships[] = $row;
    }
} else {
    // Handle query error
    echo "Error fetching memberships: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Premiums.css">
    <title>Memberships</title>
</head>

<?php include 'adminnavbar.php'; ?>

<body>

<div class="container">
    <h1>Premium Memberships</h1>

    <?php
    // Separate memberships for Owners and Customers
    $ownerMemberships = [];
    $customerMemberships = [];

    foreach ($memberships as $membership) {
        if ($membership['membership_for'] === 'O') {
            $ownerMemberships[] = $membership;
        } elseif ($membership['membership_for'] === 'C') {
            $customerMemberships[] = $membership;
        }
    }
    ?>

    <div class="child-container">
        <h2>Owners</h2>
        <div class="card-container">
            <?php if (empty($ownerMemberships)): ?>
                <p>No memberships available for Owners.</p>
            <?php else: ?>
                <?php foreach ($ownerMemberships as $membership): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($membership['membership_name']); ?></h3>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($membership['membership_type']); ?></p>
                        <p><strong>Price:</strong> <?php echo htmlspecialchars($membership['price']); ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($membership['duration_in_days']); ?> days</p>
                        <a href="edit_membership.php?id=<?php echo $membership['membership_id']; ?>">Edit</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="child-container">
        <h2>Customers</h2>
        <div class="card-container">
            <?php if (empty($customerMemberships)): ?>
                <p>No memberships available for Customers.</p>
            <?php else: ?>
                <?php foreach ($customerMemberships as $membership): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($membership['membership_name']); ?></h3>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($membership['membership_type']); ?></p>
                        <p><strong>Price:</strong> <?php echo htmlspecialchars($membership['price']); ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($membership['duration_in_days']); ?> days</p>
                        <a href="edit_membership.php?id=<?php echo $membership['membership_id']; ?>">Edit</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
