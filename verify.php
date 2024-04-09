<?php
// Include your database connection code here

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // update database to set the verified field to true for the user with the given token
    // $stmt = $pdo->prepare("UPDATE users SET verified = true WHERE token = ?");
    // $stmt->execute([$token]);
    

    echo "Your email has been verified! You can now log in.";
} else {
    echo "Invalid verification link.";
}
?>
