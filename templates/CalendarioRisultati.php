<?php

class CalendarioRisultati extends DBTable {
    public function __construct($campionato) {
        parent::__construct("SELECT DA,
                                          SA,	
                                          RA,
                                          Squadre,
                                          RR,
                                          SR,
                                          DR
                                   FROM   CalendarioRisultati
                                   WHERE  Campionato = '$campionato'");
        parent::setStyle("table table-striped");
        #parent::setAnchors("matches", 0, "Squadra");
    }
}