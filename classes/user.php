<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function register($name, $email, $password) {
        try {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            
            $stmt->execute();
            return $this->db->lastInsertId();
            
        } catch(PDOException $e) {
            error_log("Error in register: " . $e->getMessage());
            return false;
        }
    }

    public function login($email, $password) {
        try {
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            
            return false;
            
        } catch(PDOException $e) {
            error_log("Error in login: " . $e->getMessage());
            return false;
        }
    }

    public function emailExists($email) {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
            
        } catch(PDOException $e) {
            error_log("Error checking email: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById($id) {
        try {
            $sql = "SELECT id, name, email, role FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            error_log("Error getting user: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($id, $data) {
        try {
            $sets = [];
            $params = [];
            
            foreach($data as $key => $value) {
                if($key !== 'id' && $key !== 'password') {
                    $sets[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if(!empty($data['password'])) {
                $sets[] = "password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if(empty($sets)) {
                return false;
            }
            
            $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE id = :id";
            $params[':id'] = $id;
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
            
        } catch(PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUser($id) {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
            
        } catch(PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
}