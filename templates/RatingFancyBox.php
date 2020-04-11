<?php

require_once "FancyBox.php";

class RatingFancyBox extends FancyBox {
    public function __construct($value) {
        parent::__construct($value);
        if(!isset($value)) {
            $this->value = "?";
            $this->type = 5;
        }
        else {
            if($this->value < 1 or $this->value > 99)
                echo sprintf("Attention: %s value is incorrect for 'value' parameter", $value);
            elseif($this->value >= 90 and $this->value <= 99) $this->type = 1;
            elseif($this->value >= 80 and $this->value <= 89) $this->type = 2;
            elseif($this->value >= 66 and $this->value <= 79) $this->type = 3;
            elseif($this->value >= 50 and $this->value <= 65) $this->type = 4;
            elseif($this->value >= 1 and $this->value <= 49) $this->type = 5;
        }

        $this->createFBString();
        $this->createFBNode();
    }

    protected final function createFBString() {
        $this->formatted_str =
            "<span class='fancy-box rating$this->type'>$this->value</span>";
    }
    protected final function createFBNode() {
        $root = new DOMDocument();
        $this->DOMElement = $root->createElement("span");
        $this->DOMElement->appendChild(new DOMAttr("class","fancy-box rating$this->type"));
        $this->DOMElement->nodeValue = $this->value;
    }
    public function printFB() {
        return $this->formatted_str;
    }
    public function getFBNode() {
        return $this->DOMElement;
    }
}