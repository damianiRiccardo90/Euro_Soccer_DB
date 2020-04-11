<?php

require_once "FancyBox.php";

class PositionFancyBox extends FancyBox {
    public function __construct($value) {
        parent::__construct($value);
        if($this->value == "POR") $this->type = 1;
        elseif($this->value == "ASA" or $this->value == "TS" or $this->value == "DC"
            or $this->value == "TD" or $this->value == "ADA" or $this->value == "LIB")
            $this->type = 2;
        elseif($this->value == "CCM" or $this->value == "CDC" or $this->value == "COC"
            or $this->value == "COS" or $this->value == "COD" or $this->value == "CCS"
            or $this->value == "CCD" or $this->value == "ED" or $this->value == "ES"
            or $this->value == "CC")
            $this->type = 3;
        elseif($this->value == "AT" or $this->value == "ATT" or $this->value == "ATD"
            or $this->value == "ATS" or $this->value == "AD" or $this->value == "AS"
            or $this->value == "PD" or $this->value == "PS")
            $this->type = 4;
        elseif($this->value == "RIS" or $this->value == "TRI")
            $this->type = 5;
        else
            echo sprintf("Attention: %s value is incorrect for 'value' parameter", $value);

        $this->createFBString();
        $this->createFBNode();
    }

    protected final function createFBString() {
        $this->formatted_str =
            "<span class='fancy-box position$this->type'>$this->value</span>";
    }
    protected final function createFBNode() {
        $root = new DOMDocument();
        $this->DOMElement = $root->createElement("span");
        $this->DOMElement->appendChild(new DOMAttr("class","fancy-box position$this->type"));
        $this->DOMElement->nodeValue = $this->value;

    }
    public function printFB() {
        return $this->formatted_str;
    }
    public function getFBNode() {
        return $this->DOMElement;
    }
}