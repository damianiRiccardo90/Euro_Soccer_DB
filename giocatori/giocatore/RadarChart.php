<?php

require_once "../../templates/DynamicSVG.php";

class RadarChart extends DynamicSVG {
    protected $hexagon_vertex;
    protected $text_pos;
    protected $player_id;
    protected $is_keeper;
    protected $stats;

    public function __construct($player_id) {
        parent::__construct();
        $this->createArrays();
        $this->player_id = $player_id;
        $this->extractInfoFromDB();
        $this->createDOM();
    }
    private function createArrays() {
        $this->hexagon_vertex = array(
            array(0, 100),
            array(50, 200),
            array(150, 200),
            array(200, 100),
            array(150, 0),
            array(50, 0)
        );
        $this->text_pos = array(
            array(20, 130),
            array(75, 240),
            array(200, 240),
            array(255, 130),
            array(200, 20),
            array(70, 20)
        );
    }
    protected function extractInfoFromDB() {
        $query = "SELECT Ruolo_Preferito FROM Giocatore WHERE ID_G = '$this->player_id'";
        $position = DBConnection::getInstance()->fetch($query);
        if($position[0][0] == "POR") $this->is_keeper = true;
        else $this->is_keeper = false;
        if($this->is_keeper) {
            $query = "SELECT POR_TUF,
                             POR_RIF,
                             POR_PRE,
                             POR_RIN,
                             POR_PIA,
                             ACC
                      FROM   Giocatore 
                      WHERE ID_G = '$this->player_id'";
            $result = DBConnection::getInstance()->fetch($query);
            $this->stats = array(
                "TUF"=>$result[0][0],
                "RIF"=>$result[0][1],
                "PRE"=>$result[0][2],
                "RIN"=>$result[0][3],
                "PIA"=>$result[0][4],
                "VEL"=>$result[0][5]
            );
        }
        else {
            $query = "SELECT VEL,
                             DRI,
                             TIR,
                             DIF,
                             PAS,
                             FIS
                      FROM   Giocatore
                      WHERE ID_G = '$this->player_id'";
            $result = DBConnection::getInstance()->fetch($query);
            $this->stats = array(
                "VEL"=>$result[0][0],
                "DRI"=>$result[0][1],
                "TIR"=>$result[0][2],
                "DIF"=>$result[0][3],
                "PAS"=>$result[0][4],
                "FIS"=>$result[0][5]
            );
        }
    }
    public static function convexCombination($first_point, $second_point, $lambda) {
        if($lambda < 0 or $lambda > 1) printf("Lambda = %s\nThe value should range between 0 and 1");
        $result = [];
        for($i = 0; $i < count($first_point); $i++)
            $result[$i] = $first_point[$i]*$lambda + $second_point[$i]*(1-$lambda);
        return $result;
    }
    protected function createDOM() {
        $this->dom = new DOMDocument();
        $this->dom->load("../../img/radar_chart.svg");
        for($i = 0; $i < 6; $i++) {
            $text = $this->dom->createElement("text");
            $attr = $this->dom->createAttribute("x");
            $attr->value = $this->text_pos[$i][0];
            $text->appendChild($attr);
            $attr = $this->dom->createAttribute("y");
            $attr->value = $this->text_pos[$i][1];
            $text->appendChild($attr);
            $text->nodeValue = array_keys($this->stats)[$i];
            $this->dom->documentElement->appendChild($text);
        }
        $group = $this->dom->createElement("g");
        $attr = $this->dom->createAttribute("transform");
        $attr->value = "translate(50 25)";
        $group->appendChild($attr);
        $attr = $this->dom->createAttribute("stroke");
        $attr->value = "#4caf50";
        $group->appendChild($attr);
        $attr = $this->dom->createAttribute("fill");
        $attr->value = "none";
        $group->appendChild($attr);
        $points = [];
        for($i = 0; $i < 6; $i++) {
            /*
            $circle = $this->dom->createElement("circle");
            */

            $point = RadarChart::convexCombination($this->hexagon_vertex[$i], array(100, 100),
                                                   $this->stats[array_keys($this->stats)[$i]] / 100);
            $points[$i] = $point;
            /*
            $attr = $this->dom->createAttribute("cx");
            $attr->value = $point[0];
            $circle->appendChild($attr);
            $attr = $this->dom->createAttribute("cy");
            $attr->value = $point[1];
            $circle->appendChild($attr);
            $attr = $this->dom->createAttribute("r");
            $attr->value = 5;
            $circle->appendChild($attr);
            $group->appendChild($circle);
            */
        }
        $path = $this->dom->createElement("path");
        $attr = $this->dom->createAttribute("d");
        $path_string = "";
        for($i = 0; $i < 6; $i++) {
            if($i == 0) $path_string .= "M";
            else $path_string .= "L";
            $path_string .= $points[$i][0] . " " . $points[$i][1] . " ";
        }
        $path_string .= "Z";
        $attr->value = $path_string;
        $path->appendChild($attr);
        $attr = $this->dom->createAttribute("fill");
        $attr->value = "#4caf50";
        $path->appendChild($attr);
        $group->appendChild($path);
        $this->dom->documentElement->appendChild($group);
    }
}