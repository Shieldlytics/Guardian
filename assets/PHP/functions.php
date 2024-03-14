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
    // Check if the method is set
    if(isset($_POST["method"])) {

        if(isset($_POST["registerNewUser"])) {
            $userData = [
                "firstName" => $_POST["firstName"],
                "lastName" => $_POST["lastName"],
                "email" => $_POST["email"],
                "password" => $_POST["password"], // Note: Storing plain text password is not secure
                "createDate" => $_POST["createDate"],
                "createUserId" => $_POST["createUserId"]
            ];
            $userData["hashedPassword"] = password_hash($userData["password"], PASSWORD_DEFAULT);
        }


        $method = $_POST["method"];
        if($method=="registerUser") {registerUser($userData);};
        if($method=="verifyUser") {verifyUser($email,$password);};
        if($method=="addNewCompany") {verifyUser($_POST["email"], $_POST["password"]);};
        if($method=="getUsers") {getUsers();};
        if($method=="addUser") {addUser($_POST["userData"]);};
        if($method=="editUser") {editUser($_POST["userId"], $_POST["userData"]);};
        if($method=="authenticate") {authenticate($email,$password);};
        if($method=="deleteUser") {deleteUser($_POST["userId"]);};
        if($method=="getUserRoles") {getUserRoles($_POST["userId"]);};
        if($method=="addUserRole") {addUserRole($_POST["userId"], $_POST["roleId"]);};
        if($method=="manageUserRoles") {manageUserRoles($_POST["userId"], $_POST["roleId"], $_POST["action"]);};
        if($method=="getRoles") {getRoles();};
        if($method=="manageRoles") {manageRoles($_POST["roleId"], $_POST["roleData"], $_POST["action"]);};
    }
    //function to register a new user
    function registerUser($userData){
        $pdo = getConnection();
        
        // Start transaction
        $pdo->beginTransaction();
        
        try {
            // Insert user details into DBO.USERS
            $sql = "INSERT INTO DBO.USERS (FIRST_NAME, LAST_NAME, EMAIL, CREATE_DATE, CREATE_USER_ID) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userData['firstName'], $userData['lastName'], $userData['email'], $userData['createDate'], $userData['createUserId']]);
            
            // Get the USER_ID of the newly inserted user
            $userId = $pdo->lastInsertId();
    
            // Insert password into DBO.USER_EXTENSIONS
            $sql = "INSERT INTO DBO.USER_EXTENSIONS (USER_ID, JUMBLE) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $userData['hashedPassword']]);
    
            // Commit the transaction
            $pdo->commit();
    
            // Return success message
            return json_encode(['status' => 'success', 'message' => 'User registered successfully']);
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $pdo->rollBack();

            // Return error message
            return json_encode(['status' => 'error', 'message' => 'An error occurred during registration: ' . $e->getMessage()]);
        }
    }


    //function search users
    function verifyUser($email, $password) {
        // Path to the JSON file
        $jsonFilePath = '../json/userLogin.json';
    
        // Read JSON file
        $jsonData = file_get_contents($jsonFilePath);
        $data = json_decode($jsonData, true);
    
        // Initialize result as user not found
        $result = ['status' => 'error', 'message' => 'User does not exist'];
    
        // Search for the user by email
        foreach ($data['users'] as $user) {
            if (strtolower($user['email']) == strtolower($email)) {
                // Email found, now check password
                if ($user['password'] == $password) {
                    $result = ['status' => 'success', 'message' => 'User verified', 'user' => $user];
                    break; // Exit the loop as user is found
                } else {
                    $result = ['status' => 'error', 'message' => 'Password is incorrect'];
                    break; // Exit the loop as user is found but password is wrong
                }
            }
        }
    
        // Encode the result as JSON and output it
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    //function to add new company
    function addNewCompany($companyData) {
        $pdo = getConnection();
        $sql = "INSERT INTO DBO.COMPANIES (COMPANY_ID, NAME, ADDRESS_LINE_1, ADDRESS_LINE_2, ADDRESS_LINE_3, CITY, STATE, POSTAL_CODE, COUNTRY, PHONE, EMAIL, CREATE_DATE, CREATE_USER_ID, ACTIVE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$companyData['COMPANY_ID'], $companyData['NAME'], $companyData['ADDRESS_LINE_1'], $companyData['ADDRESS_LINE_2'], $companyData['ADDRESS_LINE_3'], $companyData['CITY'], $companyData['STATE'], $companyData['POSTAL_CODE'], $companyData['COUNTRY'], $companyData['PHONE'], $companyData['EMAIL'], $companyData['CREATE_USER_ID'], $companyData['ACTIVE']]);
    }
    //function to get company
    function getCompany($companyId) {
        $pdo = getConnection();
        $sql = "SELECT COMPANY_ID, NAME, ADDRESS_LINE_1, ADDRESS_LINE_2, ADDRESS_LINE_3, CITY, STATE, POSTAL_CODE, COUNTRY, PHONE, EMAIL, CREATE_DATE, CREATE_USER_ID, ACTIVE FROM DBO.COMPANIES WHERE COMPANY_ID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$companyId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        $json = json_encode(array('items' => $results));
        echo $json;
    }
    //function to edit company
    function editCompany($companyId, $companyData) {
        $pdo = getConnection();
        $sql = "UPDATE DBO.COMPANIES SET NAME = ?, ADDRESS_LINE_1 = ?, ADDRESS_LINE_2 = ?, ADDRESS_LINE_3 = ?, CITY = ?, STATE = ?, POSTAL_CODE = ?, COUNTRY = ?, PHONE = ?, EMAIL = ?, UPDATE_DATE = NOW(), UPDATE_USER_ID = ?, ACTIVE = ? WHERE COMPANY_ID = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$companyData['NAME'], $companyData['ADDRESS_LINE_1'], $companyData['ADDRESS_LINE_2'], $companyData['ADDRESS_LINE_3'], $companyData['CITY'], $companyData['STATE'], $companyData['POSTAL_CODE'], $companyData['COUNTRY'], $companyData['PHONE'], $companyData['EMAIL'], $companyData['UPDATE_USER_ID'], $companyData['ACTIVE'], $companyId]);
    }
    //function to delete company
    function deleteCompany($companyId) {
        $pdo = getConnection();
        $sql = "UPDATE DBO.COMPANIES SET ACTIVE = 'D' WHERE COMPANY_ID = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$companyId]);
    }
    //function to get all companies
    function getCompanies() {
        $pdo = getConnection();
        $sql = "SELECT COMPANY_ID, NAME, ADDRESS_LINE_1, ADDRESS_LINE_2, ADDRESS_LINE_3, CITY, STATE, POSTAL_CODE, COUNTRY, PHONE, EMAIL, CREATE_DATE, CREATE_USER_ID, ACTIVE FROM DBO.COMPANIES";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        $json = json_encode(array('items' => $results));
        echo $json;
    }
    
    //function to get all users
    function getUsers() {
        $pdo = getConnection();
        $sql = "SELECT u.USER_ID,u.FIRST_NAME,u.MIDDLE_NAME,u.LAST_NAME,u.ADDRESS_LINE_1,u.ADDRESS_LINE_2,u.ADDRESS_LINE_3,u.CITY,u.STATE,u.POSTAL_CODE,u.COUNTRY, u.PHONE,u.EMAIL,u.CREATE_DATE,u.UPDATE_DATE,u.ACTIVE, r.NAME FROM DBO.USERS u, DBO.USER_ROLES ur, DBO.ROLES r WHERE ur.USER_ID = u.USER_ID AND ur.ROLE_ID = r.ROLE_ID AND u.ACTIVE ='A'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        $json = json_encode(array('items' => $results));
        echo $json;
    }
    // Function to add a user
    function addUser($userData) {
        $pdo = getConnection();
        $sql = "INSERT INTO DBO.users (USER_ID,FIRST_NAME,MIDDLE_NAME,LAST_NAME,ADDRESS_LINE_1,ADDRESS_LINE_2,ADDRESS_LINE_3,CITY,STATE,POSTAL_CODE,COUNTRYzPHONE,EMAIL,CREATE_DATE,UPDATE_DATE,ACTIVE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$userData['USER_ID'], $userData['FIRST_NAME'], $userData['MIDDLE_NAME'], $userData['LAST_NAME'], $userData['ADDRESS_LINE_1'], $userData['ADDRESS_LINE_2'], $userData['ADDRESS_LINE_3'], $userData['CITY'], $userData['STATE'], $userData['POSTAL_CODE'], $userData['COUNTRY'], $userData['PHONE'], $userData['EMAIL'], $userData['ACTIVE']]);
    }
    // Function to delete a user
    function deleteUser($userId) {
        $pdo = getConnection();
        $sql = "UPDATE DBO.users SET ACTIVE = 'D' WHERE USER_ID = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$userId]);
    }
    // Function to edit a user
    function editUser($userId, $userData) {
        $pdo = getConnection();
        $sql = "UPDATE DBO.users SET FIRST_NAME = ?, MIDDLE_NAME = ?, LAST_NAME = ?, ADDRESS_LINE_1 = ?, ADDRESS_LINE_2 = ?, ADDRESS_LINE_3 = ?, CITY = ?, STATE = ?, POSTAL_CODE = ?, COUNTRY = ?, PHONE = ?, EMAIL = ?, UPDATE_DATE = NOW(), ACTIVE = ? WHERE USER_ID = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$userData['FIRST_NAME'], $userData['MIDDLE_NAME'], $userData['LAST_NAME'], $userData['ADDRESS_LINE_1'], $userData['ADDRESS_LINE_2'], $userData['ADDRESS_LINE_3'], $userData['CITY'], $userData['STATE'], $userData['POSTAL_CODE'], $userData['COUNTRY'], $userData['PHONE'], $userData['EMAIL'], $userData['ACTIVE'], $userId]);
    }
    // function to add user role
    function addUserRole($userId, $roleId) {
        $pdo = getConnection();
        $sql = "INSERT INTO DBO.user_roles (USER_ID, ROLE_ID) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$userId, $roleId]);
    }
    //function to manage user roles and return json response
    function manageUserRoles($userId, $roleId, $action) {
        $pdo = getConnection();
        $sql = "";
        if($action == "add") {
            $sql = "INSERT INTO DBO.user_roles (USER_ID, ROLE_ID) VALUES (?, ?)";
        } else if($action == "delete") {
            $sql = "DELETE FROM DBO.user_roles WHERE USER_ID = ? AND ROLE_ID = ?";
        }
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$userId, $roleId]);
    }
    //function to get user roles
    function getUserRoles($userId) {
        $pdo = getConnection();
        $sql = "SELECT r.ROLE_ID, r.NAME, r.DISPLAY_NAME, r.DESCRIPTION FROM DBO.user_roles ur, DBO.roles r WHERE ur.ROLE_ID = r.ROLE_ID AND ur.USER_ID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        $json = json_encode(array('items' => $results));
        echo $json;
    }
    //function to get all roles and return json response
    function getRoles() {
        $pdo = getConnection();
        $sql = "SELECT ROLE_ID, NAME, DISPLAY_NAME, DESCRIPTION, ACTIVE FROM DBO.roles";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        $json = json_encode(array('items' => $results));
        echo $json;
    }
    //manage roles
    function manageRoles($roleId, $roleData, $action) {
        $pdo = getConnection();
        $sql = "";
        if($action == "add") {
            $sql = "INSERT INTO DBO.roles (ROLE_ID, NAME, DISPLAY_NAME, DESCRIPTION, ACTIVE) VALUES (?, ?, ?, ?, ?)";
        } else if($action == "delete") {
            $sql = "DELETE FROM DBO.roles WHERE ROLE_ID = ?";
        } else if($action == "edit") {
            $sql = "UPDATE DBO.roles SET NAME = ?, DISPLAY_NAME = ?, DESCRIPTION = ?, ACTIVE = ? WHERE ROLE_ID = ?";
        }
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$roleData['ROLE_ID'], $roleData['NAME'], $roleData['DISPLAY_NAME'], $roleData['DESCRIPTION'], $roleData['ACTIVE'], $roleId]);
    }

    //save password in database table USER_EXTENSIONS
    function savePassword($userId, $password, $salt) {
        $pdo = getConnection();
        $sql = "INSERT INTO DBO.USER_EXTENSIONS (USER_ID, SEA, JUMBLE) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$userId, $password]);
    }

    //authentication
    function authenticate($username, $password) {
        $pdo = getConnection();
        $sql = "SELECT u.USER_ID,u.FIRST_NAME,u.MIDDLE_NAME,u.LAST_NAME,u.ADDRESS_LINE_1,u.ADDRESS_LINE_2,u.ADDRESS_LINE_3,u.CITY,u.STATE,u.POSTAL_CODE,u.COUNTRY, u.PHONE,u.EMAIL,u.CREATE_DATE,u.UPDATE_DATE,u.ACTIVE, r.NAME FROM DBO.USERS u, DBO.USER_ROLES ur, DBO.ROLES r WHERE ur.USER_ID = u.USER_ID AND ur.ROLE_ID = r.ROLE_ID AND u.ACTIVE ='A' AND u.EMAIL = ? AND PASSWORD = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $password]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        if(count($results) > 0){
            $response['userid'] = $results[0]['USER_ID'];
            $response['role'] = $results[0]['NAME'];
            $response['updateDate'] = $results[0]['UPDATE_DATE'];
            $response['state'] = $results[0]['STATE'];
            $response['postalCode'] = $results[0]['POSTAL_CODE'];
            $response['phone'] = $results[0]['PHONE'];
            $response['middleName'] = $results[0]['MIDDLE_NAME'];
            $response['lastName'] = $results[0]['LAST_NAME'];
            $response['firstName'] = $results[0]['FIRST_NAME'];
            $response['email'] = $results[0]['EMAIL'];
            $response['createDate'] = $results[0]['CREATE_DATE'];
            $response['country'] = $results[0]['COUNTRY'];
            $response['city'] = $results[0]['CITY'];
            $response['addressLine3'] = $results[0]['ADDRESS_LINE_3'];
            $response['addressLine2'] = $results[0]['ADDRESS_LINE_2'];
            $response['addressLine1'] = $results[0]['ADDRESS_LINE_1'];
            $response['active'] = $results[0]['ACTIVE'];
            $response['status'] = "Success";
            $response['statusID'] = "bg-success";
        }else{
            $response['status'] = "Please check your email and password, or request access if you do not have access.";
            $response['statusID'] = "bg-warning";
            }
            $json = json_encode(array('items' => $response));
            echo $json;
        }





?>