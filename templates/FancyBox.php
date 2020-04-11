<?php

abstract class FancyBox {
    protected $value;
    protected $type;
    protected $formatted_str;
    protected $DOMElement;

    public function __construct($value) {
        $this->value = $value;
    }
    protected abstract function createFBString();
    protected abstract function createFBNode();
    public abstract function printFB();
    public abstract function getFBNode();
}