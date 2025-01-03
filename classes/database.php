<?php
class Database extends PDO {
    private $host = "localhost";
    private $dbname = "artculture";
    private $user = "root";
    private $pass = "";

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