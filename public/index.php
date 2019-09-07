<?php

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use App\Database;
use App\CouponParser;

require dirname(__DIR__).'/config/bootstrap.php';

// Don't hurt this :: begin

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

//Don't hurt this :: end

?>
<!DOCTYPE html>
<html>
<head>
    <style>
        input {
            font-family: "Times New Roman";
            height: 100px;
            font-size: 42px;
            width: 480px;
            background-color: rgba(189, 245, 255, 0.82);
            border-radius: 8px;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>

    </style>
</head>
<body>
<form method="post" action="content.php">
    <input type="submit" name="analise" value="Проанализировать купоны">
</form>

</body>
</html>
