<?php

require_once "../../templates/DBTable.php";

final class PlayerStatsTable extends DBTable {
    public function __construct($id_g) {
        $query = "SELECT  Partita.ID_P,
                          `Data`,
                          CONCAT(SC.Nome, ' - ', ST.Nome) AS Squadre,
                          GS,
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
                   FROM   Partita, PrestazioneGiocatorePartita, Squadra SC, Squadra ST
                   WHERE  Giocatore = $id_g
                   AND    Partita.ID_P = PrestazioneGiocatorePartita.ID_P
                   AND    Squadra_C = SC.ID_S
                   AND    Squadra_T = ST.ID_S";
        parent::__construct($query);
        $this->setAnchors("calendario/partita/",2,0,"id_p");
        $this->removeColumn(0);
        $tooltips_array = array(
            "Data",
            "Squadre",
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
        $th->textContent = "Statistiche partita";
        $th->setAttribute("colspan", count($this->fields));
        $tr->appendChild($th);
        $thead->appendChild($tr);
        $table->insertBefore($thead, $this->dom->getElementsByTagName("thead")->item(0));
    }
}