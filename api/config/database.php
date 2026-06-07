<?php
class Database {
    private $host     = "sql309.infinityfree.com"; 
    private $db_name  = "xxxx";        
    private $username = "xxxx";        
    private $password = "xxxx";        
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
        }
        return $this->conn;
    }
}
?>