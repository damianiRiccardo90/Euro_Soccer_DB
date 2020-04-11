<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <title>ESDB: Partita</title>
</head>
<body>
    <?php require_once "../../templates/header.html"; ?>
    <div class="row-flex main">
        <div class="col-flex panel">
            <?php
                echo "<div class='row-flex match-top-panel shadow'>";
                require_once "MatchTopPanel.php";
                echo "</div>";

                echo "<div class='col-flex match-stats-panel shadow'>";
                require_once "MatchStatsPanel.php";
                echo "</div>";

                echo "<div class='col-flex match-formation-panel shadow'>";
                require_once "MatchFormationPanel.php";
                echo "</div>";

                echo "<div class='col-flex match-events-panel shadow'>";
                require_once "MatchEventsPanel.php";
                echo "</div>";

                echo "<div class='col-flex match-commentary-panel shadow'>";
                require_once "MatchCommentaryPanel.php";
                echo "</div>";
            ?>
        </div>
    </div>
    <?php require_once "../../templates/footer.html"; ?>
</body>
<script type="text/javascript" src="/js/sticky-bar.js"></script>
</html>