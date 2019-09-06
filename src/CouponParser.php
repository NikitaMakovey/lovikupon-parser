<?php


namespace App;

use Symfony\Component\DomCrawler\Crawler;

define("LINK", "https://vladivostok.lovikupon.ru");
define("ROOT_PATH", "//div[@class='promo-container']/div[@class='promo-block promo-block-teaser']/div[@class='spacer']");
define("TITLE", "//h2//a//span");
define("TO_PATH", "//h2//a/@href");
define("VALIDITY", "//div[@class='section fsize11 grey-6 tahoma']//div[@class='section-left']");
define("TIME_TO", "//nobr//span[@class='time']");
define("IMAGE", "//div[@class='promo-image']//a//img/@src");
define("COUPONS_COUNT", "//div[@class='coupons-count']//strong");
define("DATABASE", "database_1");

/**
 * Class CouponParser
 * @package App
 */
class CouponParser
{
    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $db_name;

    /**
     * CouponParser constructor.
     * @param $link
     * @param $db_name
     */
    public function __construct($link, $db_name)
    {
        $this->link = $link;
        $this->db_name = $db_name;
    }

    /**
     * @return int
     */
    public function getContent()
    {
        $link = $this->link;
        $html = file_get_contents($link);

        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $crawler->filterXPath(ROOT_PATH)->each(function (Crawler $node, $i) {
            $title = trim($node->filterXPath(TITLE)->text());
            $link = LINK . $node->filterXPath(TO_PATH)->text();
            $validity =
                trim(preg_replace("/\s{2,}/", " ",
                    $node->filterXPath(VALIDITY)->text()));
            $date_until_end =
                trim(preg_replace("/\s{2,}/", " ", $node->filterXPath(TIME_TO)->text()));
            $image = $node->filterXPath(IMAGE)->text();
            $db = new Database($this->db_name);
            $query = "INSERT INTO `" . $db->getDatabaseName() . "` 
                    (`title`, `link`, `validity`, `date_until_end`, `image`) 
                    VALUES ('$title', '$link', '$validity', '$date_until_end', '$image')";
            $connection = $db->getConnection();
            if ($connection->query($query) === TRUE) {
                $connection->close();
            } else {
                echo $connection->error . "<br>";
            }
        });
        $coupons = $crawler->filterXPath(COUPONS_COUNT)->each(function (Crawler $node, $i) {
            static $result = 0;
            $result += intval($node->text());
            return $result;
        });
        return end($coupons);
    }

}