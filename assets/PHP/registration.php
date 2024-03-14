<?php
function getConnection() {
    $serverName = "tcp:guardian-dev-db.database.windows.net,1433";
    $database = "GUARDIAN-DEV";
    $username = "GUARDIAN";
    $password = "Sh13ldlyt1c$";
    try {
        $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// if(isset($_POST["method"])) {
//     if(isset($_POST["registerNewUser"])) {
//         $userData = [
//             "firstName" => $_POST["firstName"],
//             "lastName" => $_POST["lastName"],
//             "email" => $_POST["email"],
//             "password" => password_hash($_POST["password"], PASSWORD_DEFAULT)
//         ];
       
//     }
//     $method = $_POST["method"];
//     if($method=="registerUser") {registration($userData);};    
// }
    
//     function registration($userData){
//         $pdo = getConnection();
//         // $pdo->beginTransaction();

//         try {
            
//             $sql = "INSERT INTO dbo.USERS (FIRST_NAME, LAST_NAME, EMAIL) VALUES (?, ?, ?)";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([$userData['firstName'], $userData['lastName'], $userData['email']]);
//             echo 'User inserted <br>';

//             $sql = "SELECT USER_ID FROM dbo.USERS WHERE EMAIL = ?";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([$userData['email']]);
//             $userId = $stmt->fetch();
//             echo 'User ID: ' . $userId['USER_ID'] . '<br>';

//             $sql = "INSERT INTO dbo.USER_PASSWORDS (USER_ID, PASSWORD) VALUES (?, ?)";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([$userId['USER_ID'], $userData['password']]);
//             echo 'Password inserted <br>';
            
//             $pdo->commit();
//             return json_encode(['status' => 'success', 'message' => 'User registered successfully']);
            

//             } catch (Exception $e) {
//             $pdo->rollBack();
//             return json_encode(['status' => 'error', 'message' => 'An error occurred during registration: ' . $e->getMessage()]);
//         }
//     }
?>