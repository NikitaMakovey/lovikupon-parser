<?php

if (isset($_POST['analise'])) {
    unset($_POST['analise']);
    header("Location: http://localhost:8888/parser_app_lovikupon/public/console.php");
}

if (isset($_POST['console'])) {
    unset($_POST['console']);
    header("Location: http://localhost:8888/parser_app_lovikupon/public/coupons.php");
}

