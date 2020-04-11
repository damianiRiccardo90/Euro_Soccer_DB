<?php

require_once "DBConnection.php";

abstract class Table {
    protected $mysqli;
    protected $dom;

    public function __construct() {
        $this->DBConnect();
    }
    public function printHtmlTable() {
        echo $this->dom->saveHTML();
    }
    private function DBConnect() {
        $this->mysqli = DBConnection::getInstance()->getHandle();
    }
    abstract protected function queryDB($query);
    abstract protected function createDOM();
    abstract protected function setStyle($class_name);
}