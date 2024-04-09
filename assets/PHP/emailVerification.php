<?php
// Include your database connection code here

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Always hash passwords

    // Insert the new user into your database
    // Create a unique verification token
    $token = bin2hex(random_bytes(50));

    // Store the token with the user's data in your database

    // Send the verification email
    $to = $email;
    $subject = "Verify your email address";
    $message = "Please click on the following link to verify your email address: http://yourwebsite.com/verify.php?token=$token";
    $headers = "From: no-reply@yourwebsite.com";

    if (mail($to, $subject, $message, $headers)) {
        echo "Success";
    } else {
        echo "Error";
    }
}
?>
