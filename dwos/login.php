<?php
include('dwos.php');

session_start();
$error = []; // Initialize the error array

if (isset($_POST['login'])) {
    $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check if user exists
    $query = "SELECT * FROM users WHERE user_name='$user_name' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Start the session
        $_SESSION['user_id'] = $user['user_id']; // Store user ID in session
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_type'] = $user['user_type']; // Store user type in session
        
        // Redirect based on user type
        if ($user['user_type'] === 'A') {
            header('Location: adminpage.php'); // Admin dashboard
        } elseif ($user['user_type'] === 'O') {
            header('Location: owner_landingpage.php'); // Owner dashboard
        } elseif ($user['user_type'] === 'C') {
            header('Location: customer_landingpage.php'); // Customer dashboard
        }
        exit;
    } else {
        $error[] = "Invalid username or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>

    <div class="form-container">
        <form action="login.php" method="post">
            <h3>LOGIN</h3>
            <?php
          if (!empty($error)) {
              foreach ($error as $error_msg) {
                  echo '<span class="error-msg">' . $error_msg . '</span>';
              }
          }
          ?>
            <input type="text" name="user_name" required placeholder="Username">
            <input type="password" name="password" required placeholder="Password">
            
            <div class="button-container">
                <button type="submit" name="login" class="form-btn">LOGIN</button>
            </div>
            <p>Don't have an account yet? <a href="usertypepage.php">Register here.</a></p>
        </form>
    </div>

</body>

</html>
