<?php

require_once "../../templates/DBConnection.php";
$query = "SELECT * FROM DettaglioPartita WHERE ID_P = {$_GET['id_p']}";
$rows = DBConnection::getInstance()->fetch($query);

function ratio($first, $second) { if(!$second) return 0; else return ($first * 100) / ($first + $second); }

//TODO Automatizzare con un ciclo la creazione degli stats
//TODO Riordinare le colonne degli stats dentro DettaglioPartita, controllare che non siano utilizzate in altri file (o creare una vista, meglio)
//TODO Risolvere problema dello spazio bianco tra i due div interni alla barra degli stats
//TODO Cambiare colore tipi di verde più distinti e visibili tra loro

$stats_names = array("Tiri in porta", "Tiri fuori", "Calci d'angolo", "Falli", "Cartellini gialli",
                     "Cartellini rossi", "Cross", "Possesso palla");
for($i = 0, $j = 8; $i < count($stats_names); $i++, $j += 2) {
    $home = $rows[0][$j]; $away = $rows[0][$j + 1];
    $home_ratio = ratio($home, $away); $away_ratio = ratio($away, $home);
    if($home_ratio === $away_ratio) $home_ratio = $away_ratio = 50;
    echo
    "<div class='stat-bar'>
        <p>$stats_names[$i]</p>
        <div style='width: $home_ratio%'><span>$home</span></div>
        <div style='width: $away_ratio%'><span>$away</span></div>
    </div>";
}