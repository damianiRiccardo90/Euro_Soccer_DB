<?php

require_once "../../templates/DynamicSVG.php";

//TODO specchiare sull'asse verticale le coordinate dei giocatori nel DB
//TODO allargare la griglia del campo da 12x9 a 12x11
class FormationBoard extends DynamicSVG {
    private $img_grid;
    private $match_id;
    private $team_id;
    private $home_team;
    private $player_info;

    public function __construct($match_id, $team_id) {
        parent::__construct();
        $this->createGrid();
        $this->match_id = $match_id;
        $this->team_id = $team_id;
        $this->extractInfoFromDB();
        $this->createDOM();
    }
    private function createGrid() {
        $this->img_grid = array();
        $this->img_grid[] = array(array(49,19), array(49,104.2), array(49,189.4), array(49,274.6),
            array(49,359.8), array(49,445), array(49,530.2), array(49,615.4), array(49,701));
        $this->img_grid[] = array(array(144.6,19), array(144.6,104.2), array(144.6,189.4), array(144.6,274.6),
            array(144.6,359.8), array(144.6,445), array(144.6,530.2), array(144.6,615.4), array(144.6,701));
        $this->img_grid[] = array(array(240.2,19), array(240.2,104.2), array(240.2,189.4), array(240.2,274.6),
            array(240.2,359.8), array(240.2,445), array(240.2,530.2), array(240.2,615.4), array(240.2,701));
        $this->img_grid[] = array(array(335.8,19), array(335.8,104.2), array(335.8,189.4), array(335.8,274.6),
            array(335.8,359.8), array(335.8,445), array(335.8,530.2), array(335.8,615.4), array(335.8,701));
        $this->img_grid[] = array(array(431.4,19), array(431.4,104.2), array(431.4,189.4), array(431.4,274.6),
            array(431.4,359.8), array(431.4,445), array(431.4,530.2), array(431.4,615.4), array(431.4,701));
        $this->img_grid[] = array(array(527,19), array(527,104.2), array(527,189.4), array(527,274.6),
            array(527,359.8), array(527,445), array(527,530.2), array(527,615.4), array(527,701));
        $this->img_grid[] = array(array(622.6,19), array(622.6,104.2), array(622.6,189.4), array(622.6,274.6),
            array(622.6,359.8), array(622.6,445), array(622.6,530.2), array(622.6,615.4), array(622.6,701));
        $this->img_grid[] = array(array(718.2,19), array(718.2,104.2), array(718.2,189.4), array(718.2,274.6),
            array(718.2,359.8), array(718.2,445), array(718.2,530.2), array(718.2,615.4), array(718.2,701));
        $this->img_grid[] = array(array(813.8,19), array(813.8,104.2), array(813.8,189.4), array(813.8,274.6),
            array(813.8,359.8), array(813.8,445), array(813.8,530.2), array(813.8,615.4), array(813.8,701));
        $this->img_grid[] = array(array(909.4,19), array(909.4,104.2), array(909.4,189.4), array(909.4,274.6),
            array(909.4,359.8), array(909.4,445), array(909.4,530.2), array(909.4,615.4), array(909.4,701));
        $this->img_grid[] = array(array(1005,19), array(1005,104.2), array(1005,189.4), array(1005,274.6),
            array(1005,359.8), array(1005,445), array(1005,530.2), array(1005,615.4), array(1005,701));
        $this->img_grid[] = array(array(1100.6,19), array(1100.6,104.2), array(1100.6,189.4), array(1100.6,274.6),
            array(1100.6,359.8), array(1100.6,445), array(1100.6,530.2), array(1100.6,615.4), array(1100.6,701));
    }
    protected function extractInfoFromDB() {
        $query = "SELECT * FROM Formazione WHERE ID_P = $this->match_id";
        if($result = $this->mysqli->query($query)) $array = $result->fetch_all();
        else exit($this->mysqli->error);
        $this->home_team = $this->team_id == $array[0][1] ? true : false;

        $this->player_info = array();
        if($this->home_team) {$pl_offset = 3; $pl_x_offset = 25; $pl_y_offset = 47;} //Squadra in casa
        else {$pl_offset = 14; $pl_x_offset = 36; $pl_y_offset = 58;} //Squadra in trasferta
        for($i = 0; $i < 11; $i++) {
            if($array[0][$pl_offset+$i]) {
                $query = "SELECT Numero FROM Giocatore WHERE ID_G = {$array[0][$pl_offset+$i]}";
                if($result = $this->mysqli->query($query)) $num_array = $result->fetch_all();
                else exit($this->mysqli->error);

                $this->player_info[] = array("num" => $num_array[0][0], "x" => $array[0][$pl_x_offset+$i], "y" => $array[0][$pl_y_offset+$i]);
            }
            else
                $this->player_info[] = array("num" => "?", "x" => $array[0][$pl_x_offset+$i], "y" => $array[0][$pl_y_offset+$i]);
        }
    }
    protected function createDOM() {
        $this->dom = new DOMDocument();
        $this->dom->load("../../img/soccer_field.svg");
        for($i = 0; $i < count($this->player_info); $i++) {
            $use = $this->dom->createElement("use");
            $img_grid_x = $this->img_grid[$this->player_info[$i]["y"]-1][$this->player_info[$i]["x"]-1][0];
            $img_grid_y = $this->img_grid[$this->player_info[$i]["y"]-1][$this->player_info[$i]["x"]-1][1];
            $attr = $this->dom->createAttribute("x");
            $attr->value = $img_grid_x - 50;
            $use->appendChild($attr);
            $attr = $this->dom->createAttribute("y");
            $attr->value = $img_grid_y - 50;
            $use->appendChild($attr);
            $attr = $this->dom->createAttribute("height");
            $attr->value = 100;
            $use->appendChild($attr);
            $attr = $this->dom->createAttribute("width");
            $attr->value = 100;
            $use->appendChild($attr);
            $attr = $this->dom->createAttribute("href");
            $this->home_team ? $attr->value = "#white_jersey" : $attr->value = "#black_jersey";
            $use->appendChild($attr);
            $attr = $this->dom->createAttribute("transform");
            $attr->value = "rotate(90, $img_grid_x, $img_grid_y)";
            $use->appendChild($attr);
            $this->dom->documentElement->getElementsByTagName("g")->item(0)->appendChild($use);


            $text = $this->dom->createElement("text");
            $attr = $this->dom->createAttribute("x");
            $this->player_info[$i]["num"] < 10 ? $attr->value = $img_grid_x - 13 : $attr->value = $img_grid_x - 25;
            $text->appendChild($attr);
            $attr = $this->dom->createAttribute("y");
            $attr->value = $img_grid_y + 16;
            $text->appendChild($attr);
            $attr = $this->dom->createAttribute("href");
            $this->home_team ? $attr->value = "#white_jersey" : $attr->value = "#black_jersey";
            $text->appendChild($attr);
            $attr = $this->dom->createAttribute("fill");
            $this->home_team ? $attr->value = "black" : $attr->value = "white";
            $text->appendChild($attr);
            if($this->home_team) {
                $attr = $this->dom->createAttribute("stroke");
                $attr->value = "black";
                $text->appendChild($attr);
            }
            $attr = $this->dom->createAttribute("font-size");
            $attr->value = 40;
            $text->appendChild($attr);
            $attr = $this->dom->createAttribute("transform");
            $attr->value = "rotate(90, $img_grid_x, $img_grid_y)";
            $text->appendChild($attr);
            $text->nodeValue = $this->player_info[$i]["num"];
            $this->dom->documentElement->getElementsByTagName("g")->item(0)->appendChild($text);
        }
    }
}