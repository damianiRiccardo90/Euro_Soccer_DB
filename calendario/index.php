<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <title>ESDB: Calendario</title>
</head>
<body>
    <?php require_once "../templates/header.html"; ?>
    <div class="row-flex main">
        <div class="row-flex panel">
            <div class="col-flex left-subpanel">
                <?php
                require_once "MatchDay.php";
                (new MatchDay())->printHtmlTable();
                ?>
            </div>
            <div class="col-flex right-subpanel">
                <?php
                require_once "Calendar.php";
                (new Calendar())->printHtmlTable();
                ?>
            </div>
        </div>
    </div>
    <?php require_once "../templates/footer.html"; ?>
</body>
<script type="text/javascript" src="/js/sticky-bar.js"></script>
</html>
