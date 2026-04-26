<?php
/**
 * Database Configuration
 * Transport Tracking System
 */

class Database {
    private $host = "localhost";
    private $db_name = "transport_db";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";
    
    public $conn;
    
    // Get database connection
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    // Execute query and return results
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $exception) {
            echo "Query error: " . $exception->getMessage();
            return false;
        }
    }
    
    // Insert record and return last insert ID
    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array_values($data));
            return $this->conn->lastInsertId();
        } catch(PDOException $exception) {
            echo "Insert error: " . $exception->getMessage();
            return false;
        }
    }
    
    // Update record
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach ($data as $column => $value) {
            $setClause[] = "$column = ?";
        }
        $setClause = implode(", ", $setClause);
        
        $sql = "UPDATE $table SET $setClause WHERE $where";
        $params = array_merge(array_values($data), $whereParams);
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $exception) {
            echo "Update error: " . $exception->getMessage();
            return false;
        }
    }
    
    // Delete record
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $exception) {
            echo "Delete error: " . $exception->getMessage();
            return false;
        }
    }
    
    // Find single record
    public function find($table, $where, $params = []) {
        $sql = "SELECT * FROM $table WHERE $where LIMIT 1";
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }
    
    // Find all records
    public function findAll($table, $where = "", $params = [], $orderBy = "", $limit = "") {
        $sql = "SELECT * FROM $table";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }
        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }
        
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : false;
    }
    
    // Count records
    public function count($table, $where = "", $params = []) {
        $sql = "SELECT COUNT(*) as count FROM $table";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch()['count'] : 0;
    }
}

// Create global database instance
$database = new Database();
$db = $database->getConnection();
?>
