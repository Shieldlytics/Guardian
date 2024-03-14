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
    $sql = "INSERT INTO USERS (firstName, lastName, email, password) VALUES (:firstName, :lastName, :email)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":firstName", $userData["firstName"]);
    $stmt->bindParam(":lastName", $userData["lastName"]);
    $stmt->bindParam(":email", $userData["email"]);
    $stmt->execute();
    $stmt = null;
    //get new user id
    $sql = "SELECT USER_ID FROM USERS WHERE EMAIL = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":email", $userData["email"]);
    $stmt->execute();
    $result = $stmt->fetch();
    $stmt = null;
    //add password to password table
    $sql = "INSERT INTO USER_EXTENSIONS (USER_ID, JUMBLE) VALUES (:userId, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":userId", $result["id"]);
    $stmt->bindParam(":password", $userData["password"]);
    $stmt->execute();

    $conn = null;
    echo "User registered successfully";
    
}

?>