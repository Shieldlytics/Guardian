<?php
function getConnection() {
    $serverName = "tcp:guardian-dev-db.database.windows.net,1433";
    $database = "GUARDIAN-DEV";
    $username = "GUARDIAN";
    $password = "Sh13ldlyt1c$";
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

if(isset($_POST["method"])) {
    if(isset($_POST["registerNewUser"])) {
        $userData = [
            "firstName" => $_POST["firstName"],
            "lastName" => $_POST["lastName"],
            "email" => $_POST["email"],
            "password" => password_hash($_POST["password"], PASSWORD_DEFAULT)
        ];
    }
    $method = $_POST["method"];
    if($method=="registerUser") {registration($userData);};    
}
    
    function registration($userData){
        $pdo = getConnection();
        // $pdo->beginTransaction();

        try {
            echo 'Start inserting user <br>';
            $sql = "INSERT INTO DBO.USERS (FIRST_NAME, LAST_NAME, EMAIL) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userData['firstName'], $userData['lastName'], $userData['email']]);
            echo 'user inserted successfully, now getting user id <br>';
            $userId = $pdo->lastInsertId();
            echo 'user id: ' . $userId . '<br>';
            echo 'inserting user extensions <br>';
            $sql = "INSERT INTO DBO.USER_EXTENSIONS (USER_ID, JUMBLE) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $userData['password']]);
            $pdo->commit();
            return json_encode(['status' => 'success', 'message' => 'User registered successfully']);
        } catch (Exception $e) {
            $pdo->rollBack();
            return json_encode(['status' => 'error', 'message' => 'An error occurred during registration: ' . $e->getMessage()]);
        }
    }
?>