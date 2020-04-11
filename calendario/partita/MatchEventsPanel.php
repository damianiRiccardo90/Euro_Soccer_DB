<?php

// TODO Incapsulare codice in una classe per aumentare la leggibilità

require_once "../../templates/DBConnection.php";
$query = "SELECT Minuti, /*0*/
                 Minuti_Recupero, /*1*/
                 Tipo, /*2*/
                 Giocatore_Attivo, /*3*/
                 Squadra /*4*/
          FROM   Evento
          WHERE  ID_P = {$_GET['id_p']}
          AND    (Tipo = 'goal' OR Tipo = 'primo_cart_giallo' 
                 OR Tipo = 'secondo_cart_giallo' OR Tipo = 'cart_rosso_diretto')
          ORDER BY Minuti, Minuti_Recupero DESC";
$events = DBConnection::getInstance()->fetch($query);
$query = "SELECT Squadra_C,
                 Squadra_T
          FROM   Partita
          WHERE  ID_P = {$_GET['id_p']}";
$teams = DBConnection::getInstance()->fetch($query);
$query = "SELECT DISTINCT Giocatore_Attivo
          FROM            Evento
          WHERE           ID_P = {$_GET['id_p']} AND Tipo = 'goal'";
$scorers = DBConnection::getInstance()->fetch($query);

foreach($scorers as $scorer)
    $goals[$scorer[0]] = 0;

foreach($events as $event) {
    if($event[4] == $teams[0][0]) $side = "home";
    elseif($event[4] == $teams[0][1]) $side = "away";

    if($event[2] == "goal" and isset($goals)) {
        $goals[$event[3]]++;
        if($goals[$event[3]] == 1) {
            $icon_name = "goal";
            $desc = "Goal";
        }
        elseif($goals[$event[3]] == 2) {
            $icon_name = "double_goal";
            $desc = "Doppietta";
        }
        elseif($goals[$event[3]] == 3) {
            $icon_name = "hattrick";
            $desc = "Tripletta";
        }
        elseif($goals[$event[3]] > 3) {
            $icon_name = "more_goals";
            $desc = "Goal";
        }
    }
    elseif($event[2] == "primo_cart_giallo") {
        $icon_name = "yellow_card";
        $desc = "Cartellino giallo";
    }
    elseif($event[2] == "secondo_cart_giallo") {
        $icon_name = "second_yellow";
        $desc = "Secondo cartellino giallo";
    }
    elseif($event[2] == "cart_rosso_diretto") {
        $icon_name = "red_card";
        $desc = "Cartellino rosso diretto";
    }

    $time = $event[0] . "'";
    if($event[1] !== null) $time .= " + {$event[1]}'";

    if($event[3] !== null) {
        $query = "SELECT Nome
                  FROM   Giocatore
                  WHERE  ID_G = {$event[3]}";
        $player_name = DBConnection::getInstance()->fetch($query);
    }
    else $player_name[0][0] = '?';

    echo
    "<div class='row-flex event $side'>
            <div class='icon'>
                <img src='../../img/events/$icon_name.png'>
            </div>
            <div class='text'>
                <p class='small'>$time</p>
                <p>
                    <a href='/giocatori/giocatore/index.php?id_g={$event[3]}'>
                        {$player_name[0][0]}
                    </a>
                </p>
                <p class='small'>$desc</p>
            </div>
    </div>";
}