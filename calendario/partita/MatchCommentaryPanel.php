<?php

// TODO Incapsulare codice in una classe per aumentare la leggibilità
// TODO Usare javascript per poter condensare ed espandere questo pannello
// TODO Usare javascript per poter filtrare gli eventi e farli apparire a discrezione dell'utente

require_once "../../templates/DBConnection.php";
$query = "SELECT Minuti, /*0*/
                 Minuti_Recupero, /*1*/
                 Tipo, /*2*/
                 Sotto_Tipo, /*3*/
                 Giocatore_Attivo, /*4*/
                 Giocatore_Passivo, /*5*/
                 Squadra /*6*/
          FROM   Evento
          WHERE  ID_P = {$_GET['id_p']} AND Tipo <> 'Cross'
          ORDER BY Minuti, Minuti_Recupero ASC";
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

for($i = 0; $i < count($scorers); $i++) {
    foreach($scorers[$i] as $scorer)
        $goals_by_player[$scorer] = 0;
}
for($i = 0; $i < count($teams); $i++) {
    foreach($teams[$i] as $team)
        $goals_by_team[$team] = 0;
}

foreach($events as $event) {
    if($event[6] == $teams[0][0]) $side = "home";
    elseif($event[6] == $teams[0][1]) $side = "away";

    if($event[4] !== null) {
        $query = "SELECT Nome
                  FROM   Giocatore
                  WHERE  ID_G = {$event[4]}";
        $player_name = DBConnection::getInstance()->fetch($query);
    }
    else $player_name[0][0] = '?';

    if($event[5] !== null) {
        $query = "SELECT Nome
                  FROM   Giocatore
                  WHERE  ID_G = {$event[5]}";
        $passive_player_name = DBConnection::getInstance()->fetch($query);
    }

    if($event[2] == "goal" and isset($goals_by_player) and isset($goals_by_team)) {
        if($side == "home") $goals_by_team[$teams[0][0]]++;
        elseif($side == "away") $goals_by_team[$teams[0][1]]++;

        if(isset($event[4])) {
            $goals_by_player[$event[4]]++;

            if($goals_by_player[$event[4]] == 1) {
                $icon_name = "goal";
                $desc = "Goal";
            }
            elseif($goals_by_player[$event[4]] == 2) {
                $icon_name = "double_goal";
                $desc = "Doppietta";
            }
            elseif($goals_by_player[$event[4]] == 3) {
                $icon_name = "hattrick";
                $desc = "Tripletta";
            }
            elseif($goals_by_player[$event[4]] > 3) {
                $icon_name = "more_goals";
                $desc = "Goal";
            }
        }

        $commentary = "Goal! ";
        if($event[3] == "al_volo")
            $commentary .= "Grande tiro al volo di {$player_name[0][0]} che si insacca alle spalle del portiere.";
        elseif($event[3] == "autogol")
            $commentary .= "Imbarazzante errore di {$player_name[0][0]}, ha segnato nella porta sbagliata.";
        elseif($event[3] == "dalla_distanza")
            $commentary .= "{$player_name[0][0]} con un tiro potentissimo dalla distanza riesce a cogliere il 
            portiere impreparato, che asso!";
        elseif($event[3] == "deviazione")
            $commentary .= "Il portiere non può nulla contro il tiro molto forte di {$player_name[0][0]}, 
            nonostante la palla sia stata deviata entra ugualmente in porta.";
        elseif($event[3] == "di_tacco")
            $commentary .= "Meravigliosa finezza di {$player_name[0][0]} che segna un goal di gran classe 
            utilizzando il tacco del piede.";
        elseif($event[3] == "di_testa")
            $commentary .= "{$player_name[0][0]} segna di testa grazie alle sue abilità aeree.";
        elseif($event[3] == "goal_annullato") {
            if(isset($event[4]))
                $goals_by_player[$event[4]]--;

            if($side == "home") $goals_by_team[$teams[0][0]]--;
            elseif($side == "away") $goals_by_team[$teams[0][1]]--;

            $icon_name = "disallowed_goal";
            $desc = "Goal annullato";
            $commentary = "Ci avevano creduto! Ma il goal di {$player_name[0][0]} viene annullato dall'arbitro di 
            gioco, non ci voleva!";
        }
        elseif($event[3] == "palla_inattiva")
            $commentary .= "{$player_name[0][0]} segna a partire da una palla inattiva.";
        elseif($event[3] == "pallonetto")
            $commentary .= "{$player_name[0][0]} segna con un pallonetto di estrema precisione beffando il portiere 
            avversario, che non può far altro che osservare la palla insaccarsi lentamente in rete.";
        elseif($event[3] == "punizione_diretta")
            $commentary .= "Punizione insidiosa calciata da {$player_name[0][0]} che gonfia la rete, inerme il 
            portiere.";
        elseif($event[3] == "punizione_indiretta")
            $commentary .= "{$player_name[0][0]} segna su punizione indiretta.";
        elseif($event[3] == "rigore") {
            $desc = "Goal su rigore";
            $commentary .= "{$player_name[0][0]} trasforma il calcio di rigore!";
        }
        elseif($event[3] == "rovesciata")
            $commentary .= "Sono azioni come queste che ci fanno amare il cacio! {$player_name[0][0]} con le sue 
            doti atletiche riesce ad arrivare su un pallone altissimo segnando con una rovesciata spettacolare.";
        elseif($event[3] == "tap_in")
            $commentary .= "{$player_name[0][0]} segna su tap in.";
        elseif($event[3] == "tiro")
            $commentary .= "E' {$player_name[0][0]} a segnare per la sua squadra.";

        if($event[1] !== null and $event[4] !== null) {
            $query = "SELECT Nome
                      FROM   Evento AS E, Giocatore
                      WHERE  Minuti = {$event[0]} 
                      AND    Minuti_Recupero = {$event[1]}
                      AND    Tipo = 'assist'
                      AND    E.Squadra = {$event[6]}
                      AND    Giocatore_Attivo = ID_G";
        }
        if($event[3] != "goal_annullato")
            $commentary .= "<br />Il risultato è ora di {$goals_by_team[$teams[0][0]]}-{$goals_by_team[$teams[0][1]]}, 
            l'arbitro fischia e la palla è di nuovo al centro!";
    }
    elseif($event[2] == "assist") {
        $icon_name = "assist";
        $desc = "Assist";
        $commentary = "Che passaggio di fino! Assist da parte di {$player_name[0][0]}";
    }
    elseif($event[2] == "corner") {
        $icon_name = "corner";
        $desc = "Calcio d'angolo";
        if($event[3] == "alto_da_destra")
            $commentary = "Palla alta servita in area proveniente da un corner a destra, a calciare 
            è {$player_name[0][0]}.";
        elseif($event[3] == "alto_da_sinistra")
            $commentary = "Palla alta servita in area proveniente da un corner a sinistra, a calciare 
            è {$player_name[0][0]}.";
        elseif($event[3] == "basso_da_destra")
            $commentary = "Palla bassa tesa messa in mezzo a partire da un corner a destra, a calciare 
            è {$player_name[0][0]}.";
        elseif($event[3] == "basso_da_sinistra")
            $commentary = "Palla bassa tesa messa in mezzo a partire da un corner a sinistra, a calciare 
            è {$player_name[0][0]}.";
    }
    elseif($event[2] == "fallo") {
        $icon_name = "foul";
        $desc = "Fallo";
        if($event[3] == "da_dietro")
            $commentary = "Brutto fallo da dietro di {$player_name[0][0]}, ma nessun cartellino.";
        elseif($event[3] == "fallo_serio")
            $commentary = "Bruttissimo fallo di {$player_name[0][0]}, poteva fare veramente male! L'arbitro 
            redarguisce verbalmente {$player_name[0][0]}, ma non estrae nessun cartellino, gli è andata bene!";
        elseif($event[3] == "fallo_tattico")
            $commentary = "{$player_name[0][0]} commette un fallo tattico interrompendo l'azione a favore della sua 
            squadra.";
        elseif($event[3] == "giocata_pericolosa")
            $commentary = "Giocata pericolosa di {$player_name[0][0]}, nessuna sanzione.";
        elseif($event[3] == "mani")
            $commentary = "Fallo di mano per {$player_name[0][0]}.";
        elseif($event[3] == "mani_portiere")
            $commentary = "Fallo di mano per il portiere {$player_name[0][0]}! L'arbitro non lo punisce.";
        elseif($event[3] == "ostruzione")
            $commentary = "Fallo di ostruzione da parte di {$player_name[0][0]}.";
        elseif($event[3] == "rigore") {
            $icon_name = "penalty";
            $desc = "Calcio di rigore";
            $commentary = "Fallo di {$player_name[0][0]} all'interno dell'area! L'arbitro non ha dubbi: Calcio 
            di rigore!";
        }
        elseif($event[3] == "sgambetto")
            $commentary = "{$player_name[0][0]} si mette tra il suo avversario ed il pallone, facendolo cadere a 
            terra, l'arbitro è attento e fischia il fallo.";
        elseif($event[3] == "simulazione")
            $commentary = "{$player_name[0][0]} credeva di poter gabbare l'abitro simulando un contatto 
            inesistente, si sbagliava. Il fischio è arrivato puntale, grande esperienza della terna arbitrale.";
        elseif($event[3] == "spintone")
            $commentary = "{$player_name[0][0]} con una spinta scaraventa l'avversario a terra, facile chiamata 
            per l'arbitro.";
        elseif($event[3] == "strattone")
            $commentary = "Fallo di {$player_name[0][0]} che afferra vistosamente la maglia del suo avversario.";

        if(isset($event[5]) and isset($passive_player_name))
            $commentary .= "<br />A subire il fallo è stato {$passive_player_name[0][0]}.";
    }
    elseif($event[2] == "primo_cart_giallo") {
        $icon_name = "yellow_card";
        $desc = "Cartellino giallo";
        if($event[3] == null)
            $commentary = "Cartellino giallo per {$player_name[0][0]} secondo l'arbitro, dovrà stare attento alla 
            sua condotta durante il resto della partita.";
        elseif($event[3] == "abuso_verbale")
            $commentary = "{$player_name[0][0]} parla troppo, e l'arbitro lo ammonisce, dovrà darsi una calmata 
            o sarà anche il resto della squadra a subirne le conseguenze.";
        elseif($event[3] == "condotta_antisportiva")
            $commentary = "L'arbitro mostra il cartellino giallo a {$player_name[0][0]}, condotta antisportiva!";
        elseif($event[3] == "fallo_serio")
            $commentary = "Bruttissimo fallo di {$player_name[0][0]}, poteva fare veramente male! L'arbitro non può 
            far nulla se non mostrargli il cartellino giallo, se l'è cercata!";
        elseif($event[3] == "fallo_tattico")
            $commentary = "Fallo tattico di esperienza per {$player_name[0][0]}, l'arbitro decide che è arrivata 
            l'ora del cartellino giallo, staremo a vedere.";
        elseif($event[3] == "gesto_di_stizza")
            $commentary = "L'arbitro vuole ristabilire la calma e riprendere il controllo della partita, peccato 
            per {$player_name[0][0]} il cui gesto di stizza non rimarrà impunito, cartellino giallo!";
        elseif($event[3] == "maglia_tolta")
            $commentary = "{$player_name[0][0]} si toglie la maglia in maniera plateale, l'arbitro decide di 
            attenersi al regolamento e lo ammonisce.";
        elseif($event[3] == "mani")
            $commentary = "{$player_name[0][0]} vede mostrarsi il cartellino giallo dall'arbitro a seguito di un 
            fallo di mani.";
        elseif($event[3] == "perdita_di_tempo")
            $commentary = "{$player_name[0][0]} viene ammonito per perdita di tempo, che mancanza di fair play!";
        elseif($event[3] == "scivolata")
            $commentary = "{$player_name[0][0]} viene ammonito a seguito di un entrata in scivolata fuori tempo.";
        elseif($event[3] == "simulazione")
            $commentary = "{$player_name[0][0]} ammonito per simulazione! Questo è il verdetto dell'arbitro";
        elseif($event[3] == "spintone")
            $commentary = "{$player_name[0][0]} con una spinta scaraventa l'avversario a terra, facile chiamata 
            per l'arbitro che decide di ammonirlo, impeccabile.";
        elseif($event[3] == "strattone")
            $commentary = "Fallo di {$player_name[0][0]} che afferra vistosamente la maglia del suo avversario, 
            l'arbitro lo ammonisce senza pensarci due volte.";
        elseif($event[3] == "violenza")
            $commentary = "La tensione si sente nell'aria, {$player_name[0][0]} è ammonito per condotta violenta!";
    }
    elseif($event[2] == "secondo_cart_giallo") {
        $icon_name = "second_yellow";
        $desc = "Secondo cartellino giallo";
        if($event[3] == null)
            $commentary = "{$player_name[0][0]} sapeva di essere già ammonito, niente da fare, secondo cartellino 
            giallo e la sua partita termina qui.";
        elseif($event[3] == "abuso_verbale")
            $commentary = "Attenzione alla condotta verbale per {$player_name[0][0]}, era già ammonito e l'arbitro 
            lo butta fuori, che figura!";
        elseif($event[3] == "condotta_antisportiva")
            $commentary = "La condotta antisportiva di {$player_name[0][0]} non passa inosservata, secondo giallo 
            e doccia fredda anticipata!";
        elseif($event[3] == "fallo_serio")
            $commentary = "Pessimo fallo di {$player_name[0][0]}, giocata pericolosa con espulsione per somma di 
            ammonizioni.";
        elseif($event[3] == "gesto_di_stizza")
            $commentary = "Oggi all'arbitro non gliele manda a dire nessuno! {$player_name[0][0]} espulso per 
            doppia ammonizione a seguito di un gesto di stizza.";
        elseif($event[3] == "mani")
            $commentary = "Fallo di mano di {$player_name[0][0]}, ma era già stato ammonito, espulso!";
        elseif($event[3] == "perdita_di_tempo")
            $commentary = "L'arbitro decide di far rispettare il fair play, la perdita di tempo di 
            {$player_name[0][0]} non sarà tollerata, secondo cartellino giallo per lui.";
        elseif($event[3] == "scivolata")
            $commentary = "Attenzione all'entrata in scivolata di {$player_name[0][0]}! Era già ammonito, e 
            l'arbitro lo manda negli spogliatoi.";
        elseif($event[3] == "simulazione")
            $commentary = "{$player_name[0][0]} simula un contatto inesistente, l'arbitro lo punisce con un secondo 
            cartellino giallo, pessima idea di {$player_name[0][0]}.";
        elseif($event[3] == "violenza")
            $commentary = "L'arbitro non può tollerare la condotta violenta di {$player_name[0][0]}, e lo espelle 
            mostrandogli il suo secondo cartellino giallo.";
    }
    elseif($event[2] == "cart_rosso_diretto") {
        $icon_name = "red_card";
        $desc = "Cartellino rosso diretto";
        if($event[3] == null)
            $commentary = "Attenzione alla condotta di {$player_name[0][0]}, l'arbitro lo butta fuori senza un 
            istante di esitazione.";
        elseif($event[3] == "abuso_verbale")
            $commentary = "Cosa avrà detto {$player_name[0][0]}? L'arbitro lo espelle senza secondo appello.";
        elseif($event[3] == "condotta_antisportiva")
            $commentary = "Pessimo comportamento di {$player_name[0][0]}, buttato fuori per condotta antisportiva.";
        elseif($event[3] == "fallo_serio")
            $commentary = "Che fallaccio! {$player_name[0][0]} rischia grosso, l'abitro si avvicina e alza il 
            cartellino rosso, espulso!";
        elseif($event[3] == "mani")
            $commentary = "Fallo di mano di {$player_name[0][0]}, l'arbitro decide di punire questo gesto con il 
            rosso diretto!";
        elseif($event[3] == "scivolata")
            $commentary = "Intervento in scivolata scomposto di {$player_name[0][0]} che gli costa il cartellino 
            rosso diretto!";
        elseif($event[3] == "simulazione")
            $commentary = "Simulare non è mai una bella cosa, ma stavolta l'arbitro non vuole sentire ragioni. 
            {$player_name[0][0]} deve allontanarsi dal campo, causa cartellino rosso diretto!";
        elseif($event[3] == "strattone")
            $commentary = "{$player_name[0][0]} scaraventa a terra l'avversario con uno strattone fuori misura, 
            l'arbitro ha visto tutto e non c'è scampo. Rosso diretto!";
        elseif($event[3] == "violenza")
            $commentary = "{$player_name[0][0]} e il suo comportamento violento non passano inosservati, rosso 
            diretto per lui!";
    }
    elseif($event[2] == "tiro_fuori") {
        $icon_name = "missed_shot";
        $desc = "Tiro fuori";

        if($side == "home") {
            $query = "SELECT Nome
                      FROM   Formazione, Giocatore
                      WHERE  Giocatore_T_1 = ID_G
                      AND    ID_P = {$_GET['id_p']}";
        }
        elseif($side == "away") {
            $query = "SELECT Nome
                      FROM   Formazione, Giocatore
                      WHERE  Giocatore_C_1 = ID_G
                      AND    ID_P = {$_GET['id_p']}";
        }
        $keeper = DBConnection::getInstance()->fetch($query);

        if($event[3] == "pallonetto")
            $commentary = "Audace tentativo di {$player_name[0][0]}, che con un pallonetto cerca di beffare $keeper, 
            ma gli va male stavolta. Non riesce ad inquadrare la porta.";
        elseif($event[3] == "punizione_diretta")
            $commentary = "Calcio di punizione di {$player_name[0][0]} che opta per il tiro in porta, ma la spara 
            altissima senza nemmeno impegnare il portiere.";
        elseif($event[3] == "punizione_indiretta")
            $commentary = "Calcio di punizione indiretto battuto da {$player_name[0][0]}, pallone sprecato che non 
            inquadra la porta.";
        elseif($event[3] == "rovesciata")
            $commentary = "Acrobazia di {$player_name[0][0]}, colpisce con una girata aerea il pallone ma calcia 
            lontano dalla porta difesa da $keeper.";
        elseif($event[3] == "rovesciata_pericolosa")
            $commentary = "Pericolosissima rovesciata di {$player_name[0][0]}, il tiro esce per un pelo, sarebbe 
            potuto essere un gran goal, peccato!";
        elseif($event[3] == "tiro")
            $commentary = "{$player_name[0][0]} prova un tiro in porta, ma la sua precisione lascia a desiderare, 
            tentativo fuori dallo specchio della porta.";
        elseif($event[3] == "tiro_al_volo_pericoloso")
            $commentary = "Grande tiro al volo di prima intenzione di {$player_name[0][0]} che non riesce però ad 
            inquadrare la porta";
        elseif($event[3] == "tiro_al_volo")
            $commentary = "Tiro al volo di {$player_name[0][0]}, bella la giocata ma meno il risultato, pallone 
            fuori";
        elseif($event[3] == "tiro_dalla_distanza")
            $commentary = "{$player_name[0][0]} ci prova dalla distanza, ma dovrà aggiustare la mira se vuole fare 
            male al portiere al prossimo tentativo!";
        elseif($event[3] == "tiro_deviato")
            $commentary = "{$player_name[0][0]} tira in porta, ma il suo tiro viene deviato. $keeper può tirare un 
            sospiro di sollievo, c'è mancato poco!";
        elseif($event[3] == "tiro_di_testa")
            $commentary = "Tiro di testa in area da parte di {$player_name[0][0]} ma la palla va fuori!";
        elseif($event[3] == "tiro_di_testa_pericoloso")
            $commentary = "Attenzione al tiro di testa pericoloso di {$player_name[0][0]}, i difensori farebbero 
            meglio a marcarlo stretto, il tentativo finisce però fuori";
        elseif($event[3] == "tiro_lisciato")
            $commentary = "Liscio di {$player_name[0][0]}! Capita anche ai migliori, o forse no?";
        elseif($event[3] == "tiro_pericoloso")
            $commentary = "Insidioso tiro di {$player_name[0][0]}! Nulla da fare, il pallone esce lontano dalla 
            porta";
        elseif($event[3] == "tiro_pessimo")
            $commentary = "Pessimo tiro di {$player_name[0][0]} che si perde tra gli spalti, qualche fortunato si 
            porterà a casa felicemente il pallone, altri saranno meno grati per questo tentativo";
        elseif($event[3] == "tiro_sulla_traversa")
            $commentary = "Il tiro di {$player_name[0][0]} fa tremare la porta difesa da $keeper. La palla colpisce 
            la traversa, ed il pubblico si fa sentire dagli spalti per questa occasione mancata";
        elseif($event[3] == "tiro_sulla_traversa_pericoloso")
            $commentary = "Tiro precisissimo di {$player_name[0][0]} che rimbalza sulla traversa! Per un pelo!";
        elseif($event[3] == "tiro_sul_palo")
            $commentary = "$keeper è salvato dal palo su un tiro di {$player_name[0][0]}";
        elseif($event[3] == "tiro_sul_palo_pericoloso")
            $commentary = "Gran tiro di {$player_name[0][0]} ma la palla colpisce il palo! Brividi per il 
            portiere $keeper";
    }
    elseif($event[2] == "tiro_in_porta") {
        $icon_name = "shot_on_goal";
        $desc = "Tiro in porta";

        if($side == "home") {
            $query = "SELECT Nome
                      FROM   Formazione, Giocatore
                      WHERE  Giocatore_T_1 = ID_G
                      AND    ID_P = {$_GET['id_p']}";
        }
        elseif($side == "away") {
            $query = "SELECT Nome
                      FROM   Formazione, Giocatore
                      WHERE  Giocatore_C_1 = ID_G
                      AND    ID_P = {$_GET['id_p']}";
        }
        $keeper = DBConnection::getInstance()->fetch($query);

        if($event[3] == "pallonetto")
            $commentary = "{$player_name[0][0]} cerca di beffare il portiere con un cucchiaio, ma $keeper si fa 
            trovare sicuro e si avventa sul pallone";
        elseif($event[3] == "punizione_diretta")
            $commentary = "Calcio di punizione: {$player_name[0][0]} calcia direttamente in porta ma il portiere 
            dice di no";
        elseif($event[3] == "punizione_indiretta")
            $commentary = "Tentativo di {$player_name[0][0]} a seguito di un calcio di punizione, $keeper è una 
            sicurezza, grande salvataggio";
        elseif($event[3] == "rovesciata")
            $commentary = "{$player_name[0][0]} tenta di segnare in rovesciata! Ma il risultato non è dei migliori.. 
            nessuna difficoltà per $keeper";
        elseif($event[3] == "rovesciata_pericolosa")
            $commentary = "Magia di {$player_name[0][0]}! Rovesciata pericolosissima, il portiere $keeper deve 
            distendersi per raggiungere questo pallone velenoso";
        elseif($event[3] == "tiro")
            $commentary = "Tiro di {$player_name[0][0]}, nessun problema per $keeper, avanti il prossimo!";
        elseif($event[3] == "tiro_al_volo")
            $commentary = "Tiro al volo di {$player_name[0][0]} che finisce nelle braccia di $keeper, bel tentativo 
            ma il portiere non si fa prendere in contropiede!";
        elseif($event[3] == "tiro_al_volo_pericoloso")
            $commentary = "Tiro insidiosissimo al volo di {$player_name[0][0]}! $keeper deve dare il meglio di sé 
            per arrivare su questo pallone, che spettacolo!";
        elseif($event[3] == "tiro_dalla_distanza")
            $commentary = "{$player_name[0][0]} tira una bomba dalla distanza, ma $keeper con agilità felina riesce 
            a deviare il pallone diretto in porta";
        elseif($event[3] == "tiro_deviato")
            $commentary = "Grandissimi riflessi di $keeper che con un colpo di reni riesce a parare il tiro di 
            {$player_name[0][0]} che era stato deviato!";
        elseif($event[3] == "tiro_di_testa")
            $commentary = "Tiro di testa di {$player_name[0][0]}, $keeper si esibisce con una gran parata per la 
            gioia dei suoi fan";
        elseif($event[3] == "tiro_di_testa_pericoloso")
            $commentary = "Tiro di testa preciso da posizione pericolosa da parte di {$player_name[0][0]}, ma non 
            c'è niente da fare! Ancora una volta $keeper si oppone al tiro, le sta parando tutte!";
        elseif($event[3] == "tiro_lisciato")
            $commentary = "Papera di {$player_name[0][0]} che con un tiro un po' fiappo non riesce minimamente ad 
            impensierire $keeper, deve probabilmente aver impattato male il pallone";
        elseif($event[3] == "tiro_pericoloso")
            $commentary = "Cannonata di {$player_name[0][0]}, un tiro precisissimo che mette in estrema difficoltà 
            il portiere, ma $keeper si estende e con una parata plastica la va a prendere sotto il sette";
        elseif($event[3] == "tiro_pessimo")
            $commentary = "Brutto tiro di {$player_name[0][0]}, una passeggiata per $keeper";
        elseif($event[3] == "tiro_sulla_traversa")
            $commentary = "Tiro molto forte di {$player_name[0][0]}, la palla impatta sulla traversa e finisce in 
            mano a $keeper, fischi di paura dal pubblico";
        elseif($event[3] == "tiro_sulla_traversa_pericoloso")
            $commentary = "Tiro pericolosissimo di {$player_name[0][0]} che va a rimbalzare sulla traversa, rischio 
            goal sventato da $keeper che trattiene il pallone con qualche incertezza";
        elseif($event[3] == "tiro_sul_palo")
            $commentary = "{$player_name[0][0]} tira in porta, la palla prende una strana traiettorea e va a finire 
            contro il palo rimbalzando nuovamente in area, ci deve pensare $keeper che protegge il pallone";
        elseif($event[3] == "tiro_sul_palo_pericoloso")
            $commentary = "Grandissimo tiro di {$player_name[0][0]} che cozza sul palo con grande violenza, $keeper 
            va a recuperare la palla per toglierla dai piedi degli attaccanti, che rischio!";
        elseif($event[3] == "tiro_pericoloso_parato")
            $commentary = "Parata spettacolare di $keeper a seguito del tiro in porta di {$player_name[0][0]} 
            accompagnata da un boato del pubblico, che giocata sensazionale!";
        elseif($event[3] == "tiro_parato")
            $commentary = "$keeper si fa trovare pronto, con sicurezza sventa il tiro di {$player_name[0][0]}, un 
            muro!";
    }

    $time = $event[0] . "'";
    if($event[1] !== null) $time .= " + {$event[1]}'";

    echo
    "<div class='row-flex event $side'>
            <div class='icon'>
                <img src='../../img/events/$icon_name.png'>
            </div>
            <div class='text'>
                <p class='small'>$time</p>
                <p>$commentary</p>
                <p class='small'>$desc</p>
            </div>
    </div>";
}