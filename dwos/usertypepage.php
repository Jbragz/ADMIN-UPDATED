<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Type Page</title>
   <link rel="stylesheet" href="usertypepage.css">
</head>
<body>

   <div class="form-container">
      <form action="" method="post">
         <h3>SPECIFY WHAT TYPE OF USER ARE YOU</h3>
         <div class="button-container">
            <button type="button" onclick="window.location.href='stationregister.php'" class="form-btn">Register as a Station Owner</button>
            <button type="button" onclick="window.location.href='customerregister.php'" class="form-btn">Register as a Customer</button>
         </div>
      </form>
   </div>

   <?php
   session_start();
   if (isset($_POST['user_type'])) {
       $_SESSION['user_type'] = $_POST['user_type']; // Set the user type based on button clicked
       if ($_POST['user_type'] == 'O') {
           header('Location: stationregister.php'); // Redirect to station owner registration
       } else {
           header('Location: customerregister.php'); // Redirect to customer registration
       }
       exit();
   }
   ?>

</body>
</html>
