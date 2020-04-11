<?php

require_once "DBConnection.php";

abstract class DynamicSVG {
    protected $mysqli;
    protected $dom;

    public function __construct() {
        $this->DBConnect();
    }
    protected function DBConnect() {
        $this->mysqli = DBConnection::getInstance()->getHandle();
    }
    public function printSVG() {
        echo $this->dom->saveXML();
    }
    abstract protected function extractInfoFromDB();
    abstract protected function createDOM();
}