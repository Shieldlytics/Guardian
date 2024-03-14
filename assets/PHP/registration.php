<?php
function getConnection() {
    $serverName = "tcp:guardian-dev-db.database.windows.net,1433";
    $database = "GUARDIAN-DEV";
    $username = "GUARDIAN";
    $password = "Sh13ldlyt1c";
    
    try {
        $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully";
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}

if(isset($_POST["method"])) {


    $userData = [
        "firstName" => $_POST["firstName"],
        "lastName" => $_POST["lastName"],
        "email" => $_POST["email"],
        "password" => password_hash($_POST["password"], PASSWORD_DEFAULT)
    ];
    
    $method = $_POST["method"];
    if($method=="registerUser") {registerUser($userData);};
}
    
    function registerUser($userData){
        $pdo = getConnection();
        $pdo->beginTransaction();
        try {
            $sql = "INSERT INTO DBO.USERS (FIRST_NAME, LAST_NAME, EMAIL, CREATE_DATE, CREATE_USER_ID) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userData['firstName'], $userData['lastName'], $userData['email'], $userData['createDate'], $userData['createUserId']]);
            $userId = $pdo->lastInsertId();
            $sql = "INSERT INTO DBO.USER_EXTENSIONS (USER_ID, JUMBLE) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $userData['hashedPassword']]);
            $pdo->commit();
            return json_encode(['status' => 'success', 'message' => 'User registered successfully']);
        } catch (Exception $e) {
            $pdo->rollBack();
            return json_encode(['status' => 'error', 'message' => 'An error occurred during registration: ' . $e->getMessage()]);
        }
    }
?>