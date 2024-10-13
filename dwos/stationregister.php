<?php
include('dwos.php');

session_start();
$error = []; // Initialize the error array

// Check if the user registration form was submitted
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];
    $station_name = mysqli_real_escape_string($conn, $_POST['station_name']);
    $address = mysqli_real_escape_string($conn, $_POST['station_address']);

    // Check if the user already exists
    $select = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $error[] = 'User already exists!';
    } else {
        if ($pass != $cpass) {
            $error[] = 'Passwords do not match!';
        } else {
            // Insert the user into the users table
            $insert_user = "INSERT INTO users (user_name, email, password, user_type) 
                            VALUES ('$name', '$email', '$pass', 'O')";

            if (mysqli_query($conn, $insert_user)) {
                // Get the newly inserted user's ID
                $owner_id = mysqli_insert_id($conn);

                // Store the station data in session to use later
                $_SESSION['user_data'] = [
                    'user_id' => $owner_id,
                    'user_name' => $name,
                    'email' => $email,
                    'user_type' => 'O',
                    'station_name' => $station_name,
                    'station_address' => $address
                ];

                // Redirect to the subscription page
                header('Location: subscription.php');
                exit();
            } else {
                $error[] = 'Error registering user: ' . mysqli_error($conn);
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Station Registration</title>
    <link rel="stylesheet" href="stationregister.css">
</head>
<body>
    <div class="form-container">
        <form action="" method="post">
            <h3>Owner and Station Registration</h3>
            <?php
            if (isset($error)) {
                foreach ($error as $msg) {
                    echo '<span class="error-msg">' . $msg . '</span>';
                }
            }
            ?>
            <input type="text" name="user_name" required placeholder="Name">
            <input type="email" name="email" required placeholder="E-mail Address">
            <input type="password" name="password" required placeholder="Password">
            <input type="password" name="cpassword" required placeholder="Repeat Password">
            <input type="text" name="station_name" required placeholder="Station Name">
            <input type="text" name="station_address" required placeholder="Station Address">
            <div class="button-container">
                <input type="submit" name="submit" value="Register" class="form-btn">
            </div>
            <p>Already have an account? <a href="login.php">Login here.</a></p>
        </form>
    </div>
</body>
</html>
