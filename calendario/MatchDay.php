<?php

require_once "../templates/DBTable.php";

class MatchDay extends DBTable {
    private $current_date;

    public function __construct() {
        if(!isset($_GET["Data"])) $this->current_date = "2015-08-07";
        else $this->current_date = $_GET["Data"];
        $query = "SELECT Campionato,
                         ID_P,
                         `Data`,
                         Sett,
                         CONCAT(Squadra_C, '-', Squadra_T) AS Squadre,
                         CONCAT(GC, '-', GT) AS Ris
                   FROM   DettaglioPartita
                   WHERE  `Data` = '$this->current_date'
                   ORDER BY Campionato";
        parent::__construct($query);
        parent::setAnchors("/calendario/partita/", 4, 1, "id_p");
        $this->createSeparators();
        parent::removeColumn(0);
        parent::removeColumn(0);
        parent::removeColumn(0);
        parent::setStyle("table table-striped shadow");
    }
    private function createSeparators() {
        $query = "SELECT DISTINCT Campionato FROM (
                  SELECT Campionato, `Data` FROM DettaglioPartita WHERE `Data` = '$this->current_date') AS A
                  ORDER BY Campionato";
        if($result = $this->mysqli->query($query)) $leagues = $result->fetch_all();
        else exit($this->mysqli->error);

        $xpath = new DOMXPath($this->dom);
        $tbody = $xpath->query("//tbody");
        $rows = $tbody->item(0)->childNodes;
        $root = $rows->item(0)->parentNode->parentNode;
        for($i = 0; $i < count($leagues); $i++) {
            $thead = $root->appendChild($this->dom->createElement("thead"));
            $league = $leagues[$i][0];
            $thead_th = $thead->appendChild($this->dom->createElement("th", "$league"));
            $attr = $this->dom->createAttribute("colspan");
            $attr->value = "6";
            $thead_th->appendChild($attr);
            $tbody = $root->appendChild($this->dom->createElement("tbody"));
            for($j = 0; $j < $rows->length; $j++) {
                if($leagues[$i][0] == $rows->item($j)->firstChild->textContent)
                    $tbody->appendChild($rows->item($j));
            }
        }
        $array = $xpath->query("//table/tbody[position()<2]");
        $root->removeChild($array->item(0));
    }
}