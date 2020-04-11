<?php

require_once "../../templates/RatingFancyBox.php";
require_once "../../templates/PositionFancyBox.php";
require_once "../../templates/DBConnection.php";

$query = "SELECT G.Nome, /*0*/
                 Nazione, /*1*/
                 Nascita, /*2*/
                 Altezza, /*3*/
                 Peso, /*4*/
                 S.Nome, /*5*/
                 S.ID_S, /*6*/
                 Numero, /*7*/
                 Ruolo_Squadra, /*8*/
                 Ruolo_Preferito, /*9*/
                 Piede_Preferito, /*10*/
                 TOT /*11*/
                 FROM   Giocatore AS G, Squadra AS S
                 WHERE  G.ID_G = {$_GET['id_g']} AND G.Squadra = S.ID_S";
$rows = DBConnection::getInstance()->fetch($query);

$id_g = $_GET["id_g"];
echo
"<div class='col-flex panel1'>
    <img src='../../img/players/$id_g.png'>
</div>";

if($rows[0][11] !== null)
    $TOT_box = (new RatingFancyBox($rows[0][11]))->printFB();
else
    $TOT_box = "?";
echo
"<div class='col-flex panel2'>
    <p><strong>Nome: </strong>{$rows[0][0]} $TOT_box</p>
    <p>
        <strong>Nazione: </strong>
        {$rows[0][1]}
        <img src='../../img/flags/{$rows[0][1]}.png' />
    </p>
    <p><strong>Nato il: </strong>{$rows[0][2]}</p>
    <p><strong>Altezza: </strong>{$rows[0][3]}</p>
    <p><strong>Peso: </strong>{$rows[0][4]}</p>
</div>";

if($rows[0][8] != null) {
    $team_positions = explode(" ", $rows[0][8]);
    foreach($team_positions as $team_position)
        $team_position_box .= (new PositionFancyBox($team_position))->printFB();
}
else
    $team_position_box = "?";
if($rows[0][9] != null) {
    $pref_positions = explode("-", $rows[0][9]);
    foreach($pref_positions as $pref_position)
        $pref_position_box .= (new PositionFancyBox($pref_position))->printFB();
}
else
    $pref_position_box = "?";
echo
"<div class='col-flex panel3'>
    <p>
        <strong>Squadra: </strong>
        <a href='/squadre/squadra/index.php?id_s={$rows[0][6]}'>{$rows[0][5]}</a>
        <img src='../../img/teams/{$rows[0][6]}.png' />
    </p>
    <p><strong>Numero: </strong>{$rows[0][7]}</p>
    <p><strong>Ruolo in squadra: </strong>$team_position_box</p>
    <p><strong>Ruolo preferito: </strong>$pref_position_box</p>
    <p><strong>Piede dominante: </strong>{$rows[0][10]}</p>
</div>";

echo "<div class='col-flex panel4'>";
    require_once "RadarChart.php";
    (new RadarChart($id_g))->printSVG();
echo "</div>";
