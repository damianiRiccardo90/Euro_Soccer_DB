<?php

require_once "../../templates/DBConnection.php";
require_once "FormationBoard.php";

function printFormation($side) {
    $query = "SELECT Giocatore_{$side}_1,
                     Giocatore_{$side}_2,
                     Giocatore_{$side}_3,
                     Giocatore_{$side}_4,
                     Giocatore_{$side}_5,
                     Giocatore_{$side}_6,
                     Giocatore_{$side}_7,
                     Giocatore_{$side}_8,
                     Giocatore_{$side}_9,
                     Giocatore_{$side}_10,
                     Giocatore_{$side}_11
              FROM   Formazione 
              WHERE  ID_P = {$_GET['id_p']}";
    $players = DBConnection::getInstance()->fetch($query);

    foreach($players[0] as $player) {
        if($player) {
            $query = "SELECT Numero, /*0*/ 
                             Nazione, /*1*/
                             Nome, /*2*/
                             ID_G /*3*/
                      FROM   Giocatore
                      WHERE  ID_G = $player";
            $player_info = DBConnection::getInstance()->fetch($query);

            $query = "SELECT count(Tipo)
                      FROM   Evento
                      WHERE  Giocatore_Attivo = $player
                      AND    ID_P = {$_GET['id_p']}
                      AND    Tipo = 'goal' AND Sotto_Tipo <> 'goal_annullato'";
            $num_goal = DBConnection::getInstance()->fetch($query);
            $goal_icon_formatted_str = "";
            if($num_goal[0][0] > 0)
                $goal_icon_formatted_str = "<div class='icon'>
                                                <img src='../../img/events/goal.png'>
                                            </div>";

            echo
            "<div class='row-flex player'>
                <p>
                    <strong>{$player_info[0][0]}</strong>
                    <div class='flag'>
                        <img src='../../img/flags/{$player_info[0][1]}.png' />
                    </div>
                    <a href='/giocatori/giocatore/index.php?id_g={$player_info[0][3]}'>
                        {$player_info[0][2]}
                    </a>
                    $goal_icon_formatted_str
                </p>
            </div>";
        } else {
            echo
            "<div class='row-flex player'>
                <p>?</p>
            </div>";
        }
    }
}

$query = "SELECT ID_P, Squadra_C, Squadra_T 
          FROM   Formazione
          WHERE  ID_P = {$_GET['id_p']}";
$rows = DBConnection::getInstance()->fetch($query);

echo "<div class='row-flex upper-panel'>";
(new FormationBoard($rows[0][0], $rows[0][1]))->printSVG();
(new FormationBoard($rows[0][0], $rows[0][2]))->printSVG();
echo "</div>";

echo "<div class='row-flex lower-panel'>";

echo "<div class='col-flex left-panel'>";
printFormation("C");
echo "</div>";

echo "<div class='col-flex right-panel'>";
printFormation("T");
echo "</div>";

echo "</div>";