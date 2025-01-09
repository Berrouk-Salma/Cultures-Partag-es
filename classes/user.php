<?php
require_once '../config/db.php';

class User {
    protected Database $database;
    protected int $id;
    protected string $name;
    protected string $lastname;
  
    protected string $email;
    protected string $password;
    protected string $role;
    
    private const TABLE_NAME = 'users';
    private const ID_FIELD = 'id_user';

    public function __construct() {
        $this->database = new Database();
    }

    // GETTERS avec return types
    public function getId(): int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->name;
    }
    
    public function getPrenom(): string {
        return $this->lastname;
    }
    
  
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function getPassword(): string {
        return $this->password;
    }

    public function getRole(): string {
        return $this->role;
    }

    // SETTERS avec void return type
    public function setNom(string $nom): void {
        $this->name = $nom;
    }

    public function setPrenom(string $prenom): void {
        $this->lastname = $prenom;
    }

    

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setPassword(string $password): void {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    // Authentication method
    public function login(string $email, string $password): self|false|string {
        try {
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE email = :email LIMIT 1";
            $stmt = $this->database->getConnection()->prepare($sql);
            $stmt->execute(['email' => $email]);
            
            if ($stmt->rowCount() === 0) {
                return false;
            }

            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!password_verify($password, $userData['password'])) {
                return false;
            }

            // Set object properties
            $this->id = $userData['id'];
            $this->name = $userData['name'];
            $this->lastname = $userData['lastname'];
            $this->email = $userData['email'];
            $this->role = $userData['role'];

            return $this;
            
        } catch (PDOException $e) {
            $this->logError('login', $e->getMessage());
            return "Erreur d'authentification: " . $e->getMessage();
        }
    }

    // Get user profile
    public function profile(int $id): array|string|false {
        try {
            $sql = "SELECT id_user, prenom, nom, email, telephone, role, created_at 
                   FROM " . self::TABLE_NAME . " 
                   WHERE " . self::ID_FIELD . " = :id 
                   LIMIT 1";
                   
            $stmt = $this->database->getConnection()->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            if ($stmt->rowCount() === 0) {
                return false;
            }

            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->logError('profile', $e->getMessage());
            return "Erreur de récupération du profil: " . $e->getMessage();
        }
    }

    // Error logging helper
    protected function logError(string $method, string $message): void {
        error_log("User::{$method} Error: {$message}");
    }

    public function register(string $nom, string $prenom,string $email,string $password,string $role){
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->database->getConnection()->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            // header("location: ../auth/login.php");
            print_r($stmt);
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $test = $this->database->getConnection();
            $stmt = $test->prepare("INSERT INTO users (lastname, name, email, password, role) VALUES (:prenom, :nom, :email, :pw , :role)");
            $stmt->bindParam(":prenom", $prenom, PDO::PARAM_STR);
            $stmt->bindParam(":nom", $nom, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":pw", $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(":role", $role, PDO::PARAM_STR);
    
            if($stmt->execute()) {
                // User created successfully
                $_SESSION['success'] = "Compte créé avec succès! Vous pouvez maintenant vous connecter.";
                header("Location: ../auth/login.php");
                exit();
            } else {
                // Failed to create user
                $_SESSION['error'] = "Erreur lors de la création du compte.";
                // header("Location: ../auth/register.php");
            }
    
        } catch (PDOException $e) {
            return "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}