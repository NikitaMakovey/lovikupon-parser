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

$parser = new CouponParser("https://vladivostok.lovikupon.ru/today/", "yyy");
$parser->getContent();

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .count-coupons {
            margin: auto;
            align-content: center;
        }
        .count-coupons > p {
            font-family: fantasy;
            font-size: 40px;
        }
    </style>
</head>
<body>
<div class="count-coupons">
    <p>
        <?php
        echo "Уже куплено " . $parser->getCountCoupons() . " купонов";
        ?>
    </p>
</div>
<div>
    <form method="post" action="content.php">
        <input type="submit" name="console" value="Показать все акции">
    </form>
</div>
</body>
</html>

