<?php

require_once "../../templates/DBTable.php";
require_once "../../templates/RatingFancyBox.php";

final class RatingTable extends DBTable {
    private $rating_type;
    private $id_g;
    private $values = [];

    public function __construct($rating_type) {
        $this->rating_type = $rating_type;
        $this->id_g = $_GET["id_g"];

        $query = $this->getQueryStr();
        if(isset($query)) parent::__construct($query);

        for($i = 0; $i < count($this->rows[0]); $i++)
            $this->values[$i] = $this->rows[0][$i];
        $this->invertHeader();

        parent::setStyle("table table-striped shadow");

        $this->addFancyBoxes();
    }
    private function getQueryStr() {
        $query = null;

        if($this->rating_type == "TOT") {
            $query = "SELECT TOT,
                             VEL,
                             DRI,
                             TIR,
                             DIF,
                             PAS,
                             FIS,
                             POR
                      FROM   Giocatore
                      WHERE  ID_G = $this->id_g";
        }
        elseif($this->rating_type == "VEL") {
            $query = "SELECT VEL,
                             ACC,
                             VEL_S
                      FROM   Giocatore
                      WHERE  ID_G = $this->id_g";
        }
        elseif($this->rating_type == "DRI") {
            $query = "SELECT DRI,
                             AGI,
                             EQU,
                             RIF,
                             CON,
                             DRIB,
                             FRE
                      FROM   Giocatore
                      WHERE  ID_G = $this->id_g";
        }
        elseif($this->rating_type == "TIR") {
            $query = "SELECT TIR,
                             PIA,
                             FIN,
                             POT_T,
                             TIR_D,
                             VOL,
                             RIG
                      FROM   Giocatore
                      WHERE  ID_G = $this->id_g";
        }
        elseif($this->rating_type == "DIF") {
            $query = "SELECT DIF,
                             `INT`,
                             PT,
                             MAR,
                             CON_P,
                             SCI
                      FROM   Giocatore
                      WHERE  ID_G = $this->id_g";
        }
        elseif($this->rating_type == "PAS") {
            $query = "SELECT PAS,
                             VIS,
                             CROS,
                             PCP,
                             PAS_C,
                             PAS_L,
                             EFF
                      FROM   Giocatore
                      WHERE  ID_G = $this->id_g";
        }
        elseif($this->rating_type == "FIS") {
            $query = "SELECT FIS,
                             ELE,
                             RES,
                             `FOR`,
                             AGG
                      FROM   Giocatore
                      WHERE  ID_G = $this->id_g";
        }
        elseif($this->rating_type == "POR") {
            $query = "SELECT POR,
                             POR_TUF,
                             POR_RIF,
                             POR_PRE,
                             POR_RIN,
                             POR_PIA
                      FROM   Giocatore
                      WHERE  ID_G = $this->id_g";
        }
        else
            echo sprintf("Attention: %s value is incorrect for 'rating_type' parameter", $this->rating_type);

        return $query;
    }
    private function invertHeader() {
        if($this->rating_type == "TOT") {
            $new_field = "Totale ";

            $new_rows = [];
            $new_rows[0][0] = "Velocità ";
            $new_rows[1][0] = "Dribbling ";
            $new_rows[2][0] = "Tiri ";
            $new_rows[3][0] = "Difesa ";
            $new_rows[4][0] = "Passaggi ";
            $new_rows[5][0] = "Fisico ";
            $new_rows[6][0] = "Portiere ";

            $deletable_columns = 7;
        }
        elseif($this->rating_type == "VEL") {
            $new_field = "Velocità ";

            $new_rows = [];
            $new_rows[0][0] = "Accelerazione ";
            $new_rows[1][0] = "Vel. Scatto ";

            $deletable_columns = 2;
        }
        elseif($this->rating_type == "DRI") {
            $new_field = "Dribbling ";

            $new_rows = [];
            $new_rows[0][0] = "Agilità ";
            $new_rows[1][0] = "Equilibrio ";
            $new_rows[2][0] = "Riflessi ";
            $new_rows[3][0] = "Controllo ";
            $new_rows[4][0] = "Dribbling ";
            $new_rows[5][0] = "Freddezza ";

            $deletable_columns = 6;
        }
        elseif($this->rating_type == "TIR") {
            $new_field = "Tiri ";

            $new_rows = [];
            $new_rows[0][0] = "Piazzamento ";
            $new_rows[1][0] = "Finalizzazione ";
            $new_rows[2][0] = "Potenza tiro ";
            $new_rows[3][0] = "Tiro dalla dist. ";
            $new_rows[4][0] = "Tiro al volo ";
            $new_rows[5][0] = "Rigori ";

            $deletable_columns = 6;
        }
        elseif($this->rating_type == "DIF") {
            $new_field = "Difesa ";

            $new_rows = [];
            $new_rows[0][0] = "Intercettazione ";
            $new_rows[1][0] = "Tiro di testa ";
            $new_rows[2][0] = "Marcatura ";
            $new_rows[3][0] = "Contrasto ";
            $new_rows[4][0] = "Scivolata ";

            $deletable_columns = 5;
        }
        elseif($this->rating_type == "PAS") {
            $new_field = "Passaggi ";

            $new_rows = [];
            $new_rows[0][0] = "Visione ";
            $new_rows[1][0] = "Cross ";
            $new_rows[2][0] = "Punizioni ";
            $new_rows[3][0] = "Passaggi corti ";
            $new_rows[4][0] = "Passaggi lunghi ";
            $new_rows[5][0] = "Passaggi lunghi ";

            $deletable_columns = 6;
        }
        elseif($this->rating_type == "FIS") {
            $new_field = "Fisico ";

            $new_rows = [];
            $new_rows[0][0] = "Elevazione ";
            $new_rows[1][0] = "Resistenza ";
            $new_rows[2][0] = "Forza ";
            $new_rows[3][0] = "Aggressività ";

            $deletable_columns = 4;
        }
        elseif($this->rating_type == "POR") {
            $new_field = "Portiere ";

            $new_rows = [];
            $new_rows[0][0] = "Tuffo ";
            $new_rows[1][0] = "Riflessi ";
            $new_rows[2][0] = "Presa ";
            $new_rows[3][0] = "Rinvio ";
            $new_rows[4][0] = "Piazzamento ";

            $deletable_columns = 5;
        }

        if(isset($new_field)) $this->fields[0]->name = $new_field;
        if(isset($new_rows)) $this->rows = $new_rows;
        for($i = 0; isset($deletable_columns) and $i < $deletable_columns; $i++)
            $this->removeColumn(1);

        $this->createDOM();
    }
    private function addFancyBoxes() {
        $header_row = $this->dom->getElementsByTagName("th")->item(0);
        $RatingBox = $this->dom->importNode((new RatingFancyBox($this->values[0]))->getFBNode(), true);
        $header_row->appendChild($RatingBox);
        for($i = 1; $i < count($this->values); $i++) {
            $row = $this->dom->getElementsByTagName("td")->item($i - 1);
            $RatingBox = $this->dom->importNode((new RatingFancyBox($this->values[$i]))->getFBNode(), true);
            $row->appendChild($RatingBox);
        }
    }
}