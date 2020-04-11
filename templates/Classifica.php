<?php

class Classifica extends DBTable {
    public function __construct($campionato) {
        parent::__construct("SELECT Squadra,
                                          Punti,	
                                          P_Vinte,
                                          P_Pareggiate,
                                          P_Perse,
                                          G_Fatti,
                                          G_Subiti,
                                          Diff_Reti
                                   FROM   Classifica
                                   WHERE  Campionato = '$campionato'");
        parent::setStyle("table table-striped");
        //parent::setAnchors("teams", 0, "squadra");
    }
}