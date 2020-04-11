<?php

require_once "../../templates/DBTable.php";

class PlayerGlobalStatsTable extends DBTable {
    public function __construct($id_g) {
        $query = "SELECT  GS,
                          GSR,
                          GAU,
                          GSCP,
                          CPB,
                          ASS,
                          FC,
                          FS,
                          CAG,
                          CAR,
                          TP,
                          TF,
                          CRO,
                          COR
                   FROM   PrestazioneGiocatoreComplessiva
                   WHERE  Giocatore = $id_g";
        parent::__construct($query);
        $tooltips_array = array(
            "Goal segnati",
            "Goal segnati su rigore",
            "Autogol",
            "Goal su punizione",
            "Calci di punizione battuti",
            "Assist",
            "Falli commessi",
            "Falli subiti",
            "Cartellini gialli",
            "Cartellini rossi",
            "Tiri in porta",
            "Tiri fuori",
            "Cross",
            "Calci d'angolo"
        );
        $this->createTooltips($tooltips_array);
        $this->setStyle("table table-striped shadow");
        $this->addHeader();
    }
    private function addHeader() {
        $table = $this->dom->getElementsByTagName("table")->item(0);
        $thead = $this->dom->createElement("thead");
        $tr = $this->dom->createElement("tr");
        $th = $this->dom->createElement("th");
        $th->textContent = "Statistiche complessive";
        $th->setAttribute("colspan", count($this->fields));
        $tr->appendChild($th);
        $thead->appendChild($tr);
        $table->insertBefore($thead, $this->dom->getElementsByTagName("thead")->item(0));
    }
}