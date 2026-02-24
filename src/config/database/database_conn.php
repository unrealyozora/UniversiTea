<?php
class Database{
    private $host = "localhost";
    private $user = "tceron";
    private $pass = "faiPeemaiphi6fei";
    private $charset = "utf8mb4";
    public  $conn;

    public function  getConnection(){
        $this->conn = null;
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->user;charset=$this->charset";
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET CHARACTER SET utf8");
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e){
            echo "Errore di connessione: " . $e->getMessage();
        }

        return $this->conn;
    }
}