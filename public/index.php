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
    .coupon {
        background-color: rgba(157, 208, 255, 0.74);
    }
</style>
</head>
<body>

<b>
    <?php
    $parser = new CouponParser("https://vladivostok.lovikupon.ru/today/", "lovikupon_db");
    echo "Уже куплено " . $parser->getContent() . " купонов";
    ?>
</b>
<br>
<?php
$db = new Database("lovikupon_db");
$query = "SELECT * FROM " . $db->getDatabaseName();
$connection = $db->getConnection();
$result = $connection->query($query);
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    ?>
    <div class='coupon'>
        <div class="validity">
            <p><?php echo $row['validity'] ?></p>
            <p><?php echo $row['date_until_end'] ?></p>
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
    <?php
}
$connection->close();
?>

</body>
</html>
