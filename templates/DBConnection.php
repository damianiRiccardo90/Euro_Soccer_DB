<?php

/**
 * Classe rappresentante la connessione al database, fa uso della libreria mysqli,
 * aderisce al design pattern Singleton per ottimizzare l'accesso al database
 */
final class DBConnection {
    private $handle;
    //TODO Mettere le credenziali d'accesso in un file config
    private $host = "eurosoccerdb.heliohost.org";
    private $user = "damianir_admin";
    private $pass = "Candy_Suxxx_6969";
    private $db_name = "damianir_eurosoccerdb";

    public static function getInstance() {
        static $instance = null;
        if(!isset($instance)) $instance = new DBConnection();
        return $instance;
    }
    /**
     * Funzione statica di utilità per il debugging che permette di stampare sulla console del browser
     *
     * @param mixed $data The value being encoded. Can be any type except a resource.
     *        All string data must be UTF-8 encoded.
     *        PHP implements a superset of JSON - it will also encode and decode scalar types and NULL.
     *        The JSON standard only supports these values when they are nested inside an array or an object.
     */
    public static function console_log($data) {
        echo "<script>";
        echo "console.log(" . json_encode($data) . ")";
        echo "</script>";
    }
    private function __construct() {
        $this->connect();
    }
    private function __clone() {}
    private function connect() {
        $this->handle = new mysqli($this->host, $this->user, $this->pass, $this->db_name);
        if($this->handle->connect_errno) {
            printf("Tentativo di connessione a MySQL fallito: %s\n",
                $this->handle->connect_error);
            exit();
        }
    }
    private function disconnect() {
        $this->handle->close();
    }
    public function getHandle() {
        return $this->handle;
    }
    public function fetch($query) {
        if($result = $this->getHandle()->query($query)) return $result->fetch_all();
        else exit($this->getHandle()->error);
    }
    public function __destruct() {
        $this->disconnect();
    }
}