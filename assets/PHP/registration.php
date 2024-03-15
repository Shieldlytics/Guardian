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
    $conn = getConnection();
    try {
        $conn->beginTransaction();

        $sql = "INSERT INTO USERS (FIRST_NAME, LAST_NAME, EMAIL) VALUES (:firstName, :lastName, :email)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':firstName' => $userData["firstName"], ':lastName' => $userData["lastName"], ':email' => $userData["email"]]);

        $userId = $conn->lastInsertId();  // If USER_ID is auto-incremented

        $sql = "INSERT INTO USER_EXTENSIONS (USER_ID, JUMBLE) VALUES (:userId, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':userId' => $userId, ':password' => $userData["password"]]);

        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'User registered successfully']);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()]);
    } finally {
        $conn = null;
    }
}


?>