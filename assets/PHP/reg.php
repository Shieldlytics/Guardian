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

    
?>