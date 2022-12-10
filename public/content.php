<?php

if (isset($_POST['analise'])) {
    unset($_POST['analise']);
    header("Location: http://localhost:8000/console.php");
}

if (isset($_POST['console'])) {
    unset($_POST['console']);
    header("Location: http://localhost:8000/coupons.php");
}

