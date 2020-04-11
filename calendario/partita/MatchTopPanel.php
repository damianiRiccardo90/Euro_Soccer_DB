<?php

require_once "../../templates/DBConnection.php";
$query = "SELECT D.Campionato,
                 D.ID_P,
                 D.`Data`,
                 Sett,
                 D.Squadra_C,
                 D.Squadra_T,
                 P.Squadra_C,
                 P.Squadra_T,
                 CONCAT(GC, '-', GT) AS Ris
                 FROM   DettaglioPartita AS D, Partita AS P
                 WHERE  D.ID_P = {$_GET['id_p']} AND D.ID_P = P.ID_P";
$rows = DBConnection::getInstance()->fetch($query);

//TODO cambiare il colore del risultato da verde a nero
//TODO ridurre dimensioni nomi squadre causa overflow

echo
"<div class='col-flex left-panel'>
    <img src='../../img/teams/{$rows[0][6]}.png' />
</div>";

echo
"<div class='col-flex central-panel'>
    <p>{$rows[0][0]}, Settimana #{$rows[0][3]}, {$rows[0][2]}</p>
    <h2>
        <a href=''>{$rows[0][4]}</a> - <a href=''>{$rows[0][5]}</a>
    </h2>
    <h2>{$rows[0][8]}</h2>
</div>";

echo
"<div class='col-flex right-panel'>
    <img src='../../img/teams/{$rows[0][7]}.png'>
</div>";