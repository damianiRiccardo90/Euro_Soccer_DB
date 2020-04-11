<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <title>ESDB: Giocatore</title>
</head>
<body>
    <?php require_once "../../templates/header.html"; ?>
    <div class="row-flex main">
        <div class="col-flex panel">
            <?php
                echo "<div class='row-flex player-top-panel shadow'>";
                require_once "PlayerTopPanel.php";
                echo "</div>";

                echo "<div class='row-flex player-ratings-panel shadow'>";
                require_once "RatingTable.php";
                (new RatingTable("TOT"))->printHtmlTable();
                (new RatingTable("VEL"))->printHtmlTable();
                (new RatingTable("DRI"))->printHtmlTable();
                (new RatingTable("TIR"))->printHtmlTable();
                (new RatingTable("DIF"))->printHtmlTable();
                (new RatingTable("PAS"))->printHtmlTable();
                (new RatingTable("FIS"))->printHtmlTable();
                (new RatingTable("POR"))->printHtmlTable();
                echo "</div>";

                echo "<div class='col-flex player-stats-panel shadow'>";
                require_once "PlayerGlobalStatsTable.php";
                require_once "PlayerStatsTable.php";
                (new PlayerGlobalStatsTable($_GET["id_g"]))->printHtmlTable();
                (new PlayerStatsTable($_GET["id_g"]))->printHtmlTable();
                echo "</div>";
            ?>
        </div>
    </div>
    <?php require_once "../../templates/footer.html"; ?>
</body>
<script type="text/javascript" src="/js/sticky-bar.js"></script>
</html>