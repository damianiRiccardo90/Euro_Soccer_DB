<?php

class Calendar extends Table {
    /**
     * Numero di giorni nel mese corrente
     */
    private $days_in_month = 0;
    /**
     * Unix Timestamp del giorno selezionato
     */
    private $current_timestamp = 0;
    /**
     * Unix Timestamp del primo giorno del mese
     */
    private $first_timestamp = 0;
    /**
     * Unix Timestamp dell'ultimo giorno del mese
     */
    private $last_timestamp = 0;

    public function __construct() {
        parent::__construct();
        if(!isset($_GET["Data"])) $date = "2015-08-07";
        else $date = $_GET["Data"];
        $this->current_timestamp = strtotime($date);
        $current_year = date("Y", $this->current_timestamp);
        $current_month = date("m", $this->current_timestamp);
        $this->days_in_month = date("t", strtotime("$current_year-$current_month"));
        $this->first_timestamp = strtotime("$current_year-$current_month-01");
        $this->last_timestamp = strtotime("$current_year-$current_month-$this->days_in_month");
        $this->createDOM();
        $this->setStyle("table calendar shadow");
    }
    protected function createDOM() {
        $this->dom = new DOMDocument();
        $root = $this->dom->appendChild($this->dom->createElement("table"));
        $this->createTHEAD($root);
        $this->createTBODY($root);
    }
    private function createTHEAD($root) {
        $thead = $root->appendChild($this->dom->createElement("thead"));

        $thead_tr = $thead->appendChild($this->dom->createElement("tr"));

        $thead_tr_th = $thead_tr->appendChild($this->dom->createElement("th"));
        $thead_tr_th_a = $thead_tr_th->appendChild($this->dom->createElement("a", "<<"));
        $tmp = date("Y-m-d", $this->first_timestamp);
        //Seleziona la prima data disponibile del mese precedente
        $query = "SELECT MAX(`Data`) FROM Partita WHERE `Data` < '$tmp'";
        if($array = $this->queryDB($query)) {
            $att = $this->dom->createAttribute("href");
            $att->value = "index.php?Data=" . $array[0][0] . "#calendar";
            $thead_tr_th_a->appendChild($att);
        }

        $title_str = date("Y-m", $this->current_timestamp);
        $thead_tr_th = $thead_tr->appendChild($this->dom->createElement("th", $title_str));
        $att = $this->dom->createAttribute("colspan");
        $att->value = "5";
        $thead_tr_th->appendChild($att);

        $thead_tr_th = $thead_tr->appendChild($this->dom->createElement("th"));
        $thead_tr_th_a = $thead_tr_th->appendChild($this->dom->createElement("a", ">>"));
        $tmp = date("Y-m-d", $this->last_timestamp);
        //Seleziona la prima data disponibile del mese successivo
        $query = "SELECT MIN(`Data`) FROM Partita WHERE `Data` > '$tmp'";
        if($array = $this->queryDB($query)) {
            $att = $this->dom->createAttribute("href");
            $att->value = "index.php?Data=" . $array[0][0] . "#calendar";
            $thead_tr_th_a->appendChild($att);
        }

        $thead_tr = $thead->appendChild($this->dom->createElement("tr"));

        $fields = array("Lun", "Mar", "Mer", "Gio", "Ven", "Sab", "Dom");
        foreach($fields as $field)
            $thead_tr->appendChild($this->dom->createElement("th", $field));
    }
    private function createTBODY($root) {
        $tbody = $root->appendChild($this->dom->createElement("tbody"));

        $first_date = date("Y-m-d", $this->first_timestamp);
        $last_date = date("Y-m-d", $this->last_timestamp);
        $query = "SELECT DISTINCT `Data` FROM Partita WHERE `Data` BETWEEN '$first_date' AND '$last_date' ORDER BY `Data`";
        $result = $this->queryDB($query);
        $match_array = array();
        foreach($result as $i)
            foreach($i as $j)
                $match_array[] = date("d", strtotime($j));

        $offset = 1 - date("N", $this->first_timestamp);
        for($i = 0; $i < 42; $i++) {
            if(!($i % 7)) $tbody_tr = $tbody->appendChild($this->dom->createElement("tr"));

            $day = date("d", strtotime("$offset days", $this->first_timestamp));
            $tbody_tr_td = $tbody_tr->appendChild($this->dom->createElement("td", $day));

            $attr = $this->dom->createAttribute("class");
            if($offset < 0 or $offset >= $this->days_in_month) {
                $attr->value = "hidden";
                $tbody_tr_td->appendChild($attr);
            }
            elseif($day == date("d", $this->current_timestamp)) {
                $attr->value = "selected";
                $tbody_tr_td->appendChild($attr);
            }
            else {
                $found = false;
                foreach($match_array as $item)
                    if($day == $item) $found = true;
                if($found) {
                    $attr->value = "has-match";
                    $tbody_tr_td->appendChild($attr);
                    $tbody_tr_td->nodeValue = null;
                    $tbody_tr_td_a = $tbody_tr_td->appendChild($this->dom->createElement("a", $day));
                    $attr = $this->dom->createAttribute("href");
                    $date = date("Y-m", $this->current_timestamp) . "-$day";
                    $attr->value = "index.php?Data=$date#calendar";
                    $tbody_tr_td_a->appendChild($attr);
                }
            }
            $offset++;
        }
    }
    protected function queryDB($query) {
        if($result = $this->mysqli->query($query)) {
            $rows = $result->fetch_all();
            if(isset($rows[0][0])) {
                $result->free();
                return $rows;
            }
            else {
                $result->free();
                return null;
            }
        }
        else exit($this->mysqli->error);
    }
    protected function setStyle($class_name) {
        $root = $this->dom->getElementsByTagName("table")->item(0);
        $root->setAttribute("class", "$class_name");
        $root->setAttribute("id", "calendar");
    }
}