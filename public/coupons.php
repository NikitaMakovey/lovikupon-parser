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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .coupon {
            padding: 1%;
            background-color: rgba(237, 255, 246, 0.84);
            border: 1px solid black;
            width: 60%;
            margin-bottom: 1%;
        }
        .info {
            display: block;
            margin-bottom: -2%;
        }
        .validity {
            display: inline-block;
        }
        .sale_end {
            display: inline-block;
            horiz-align: right;
            margin-left: 45%;
        }
    </style>
</head>
<body>

    <?php
    $db = new Database("yyy");
    if (isset($_POST['SALE_END'])) {
        $query = "SELECT * FROM " . $db->getDatabaseName() . " ORDER BY `date_until_end` ASC";
        $connection = $db->getConnection();
        $result = $connection->query($query);
        unset($_POST['SALE_END']);
    } elseif (isset($_POST['VALIDITY'])) {
        $query = "SELECT * FROM " . $db->getDatabaseName() . " ORDER BY `validity` ASC";
        $connection = $db->getConnection();
        $result = $connection->query($query);
        unset($_POST['VALIDITY']);
    } else {
        $query = "SELECT * FROM " . $db->getDatabaseName();
        $connection = $db->getConnection();
        $result = $connection->query($query);
    }
    ?>
    <form action="" method="post">
        <input type="submit" name="SALE_END" value="Сортировка по окончанию продаж"><br><br>
        <input type="submit" name="VALIDITY" value="Сортировка по сроку действия"><br><br>
        <div>
            <?php while ($row = $result->fetch_array(MYSQLI_ASSOC)) { ?>
            <div class='coupon'>
                <div class="info">
                    <p class="validity">Срок действия : <b><?php echo $row['validity'] ?> дн.</b></p>
                    <p class="sale_end">До конца продаж :
                        <b>
                        <?php echo date("d дн. H:i:s", mktime(0, 0, $row['date_until_end'])) ?>
                        </b>
                    </p>
                </div>
                <a href="<?php echo $row['link'] ?>">
                    <h2>
                        <?php echo $row['title'] ?>
                    </h2>
                </a>
                <div class="image-coupons">
                    <img src="<?php echo $row['image'] ?> " alt='Promo Image'>
                </div>
            </div>
            <?php } ?>
        </div>
    </form>



