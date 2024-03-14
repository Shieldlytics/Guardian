<?php
function getConnection() {
    $DB_DNS = "jdbc:sqlserver://;serverName=guardian-dev-db.database.windows.net;databaseName=GUARDIAN-DEV;encrypt=true;trustServerCertificate=false;hostNameInCertificate=*.database.windows.net;loginTimeout=30;";
    $DB_USER = "GUARDIAN";
    $DB_PASSWORD = "Sh13ldlyt1c$";
    $conn = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

if(isset($_POST["method"])) {

    if(isset($_POST["registerNewUser"])) {
        $userData = [
            "firstName" => $_POST["firstName"],
            "lastName" => $_POST["lastName"],
            "email" => $_POST["email"],
            "password" => $_POST["password"]
        ];
        $userData["hashedPassword"] = password_hash($userData["password"], PASSWORD_DEFAULT);
    }
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