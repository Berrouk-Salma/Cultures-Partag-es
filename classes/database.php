<?php
class Database extends PDO {
    private $host = "localhost"; // Typically localhost
    private $dbname = "art_culture_db"; // Nom de ta base de données
    private $user = "root";     // Ton nom d'utilisateur MySQL (par défaut root)
    private $pass = "";         // Ton mot de passe MySQL (vide par défaut en local)

    public function __construct() {
        try {
            parent::__construct(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8",
                $this->user,
                $this->pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}