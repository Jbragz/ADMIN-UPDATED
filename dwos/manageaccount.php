<?php
session_start();

include('dwos.php'); // Include your database connection

// Check if the admin is logged in by verifying user_id in the session
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header('Location: login.php');
    exit();
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

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs
    $user_name = $_POST['user_name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];

    // Handle file upload if a new profile image is provided
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "image/";
        $profile_image = time() . '_' . basename($_FILES['profile_image']['name']);
        $target_file = $target_dir . $profile_image;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            // If the upload is successful, set the profile_image variable
            $profile_image = $profile_image; 
        } else {
            // Handle upload error
            echo "Error uploading image.";
            $profile_image = $user['image'];
        }
    } else {
        // If no new image uploaded, keep the old one
        $profile_image = $user['image'];
    }

    // Update query to save changes to the database
    $update_query = "UPDATE users SET 
                        user_name = '$user_name', 
                        address = '$address', 
                        phone_number = '$phone_number', 
                        image = '$profile_image' 
                    WHERE user_id = '$user_id' AND user_type = 'A'";

    // Execute the update query
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Profile updated successfully');</script>";
        // Refresh the page to load updated details
        header("Refresh:0");
    } else {
        echo "Error updating profile: " . mysqli_error($conn); // Error handling
    }
}
?>


<!DOCTYPE html>
    <html lang="en">
    <?php include 'adminnavbar.php'; ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage Account</title>
        <link rel="stylesheet" href="manageaccount.css">
    </head>
    <body>
        <div class="profile-container">
            <h2>Manage Account</h2>
            <form action="" method="POST" enctype="multipart/form-data">
            <div class="profile-pic-container">
                <img src="image/<?php echo $user['image']; ?>?v=<?php echo time(); ?>" alt="Profile Pic" class="profile-pic">
                <label for="profile_image" class="custom-file-upload">
                    <span class="plus-sign">+</span>
                    <input type="file" name="profile_image" id="profile_image" onchange="updateFileName()" style="display: none;">
                </label>
                <span class="update-text">Update Profile Pic</span>
            </div>


                <div class="profile-info">
                    <label for="user_name">Name:</label>
                    <input type="text" name="user_name" value="<?php echo $user['user_name']; ?>" required>

                    <label for="address">Address:</label>
                    <input type="text" name="address" value="<?php echo $user['address']; ?>" required>

                    <label for="phone_number">Mobile Number:</label>
                    <input type="text" name="phone_number" value="<?php echo $user['phone_number']; ?>" required>
                </div>

                <button type="submit" class="update-btn">UPDATE</button>
            </form>
        </div>

        <script>
            function updateFileName() {
                const input = document.getElementById('profile_image');
                const fileName = document.getElementById('file_name');
                fileName.textContent = input.files.length > 0 ? input.files[0].name : 'No file chosen';
            }
        </script>
    </body>
    </html>