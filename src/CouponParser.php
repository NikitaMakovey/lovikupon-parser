<?php


namespace App;

use Symfony\Component\DomCrawler\Crawler;

define("LINK", "https://vladivostok.lovikupon.ru");
define("ROOT_PATH", "//div[@class='promo-container']/div[@class='promo-block promo-block-teaser']/div[@class='spacer']");
define("TITLE", "//h2//a//span");
define("TO_PATH", "//h2//a/@href");
define("VALIDITY", "//div[@class='section fsize11 grey-6 tahoma']//div[@class='section-left']");
define("TIME_TO", "//nobr");
define("IMAGE", "//div[@class='promo-image']//a//img/@src");
define("COUPONS_COUNT", "//div[@class='coupons-count']//strong");
const timeArray = array(
    "янв." => 0,
    "фев." => 31,
    "март." => 59,
    "апр." => 90,
    "мая" => 120,
    "июн." => 151,
    "июл." => 181,
    "авг." => 212,
    "сент." => 243,
    "окт." => 273,
    "нояб." => 304,
    "дек." => 334,
);

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
     * @var int
     */
    private $count_coupons;

    /**
     * CouponParser constructor.
     * @param $link
     * @param $db_name
     */
    public function __construct($link, $db_name)
    {
        $this->link = $link;
        $this->db_name = $db_name;
        $connection = new \mysqli("localhost", "root", "root");
        $connection->connect("localhost", "root", "root", $db_name);
        $query = "TRUNCATE TABLE $db_name";
        $connection->query($query);
    }

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
            preg_match_all("/[0-9]+|(янв.|фев.|март.|апр.|мая|июн.|июл.|авг.|сент.|окт.|нояб.|дек.)/", $validity, $matchess);
            $validity = $matchess[0][2] + timeArray[$matchess[0][3]] - ($matchess[0][0] + timeArray[$matchess[0][1]]) + 1;
            $sale_end =
                trim(preg_replace("/\s{2,}/", " ", $node->filterXPath(TIME_TO)->text()));
            preg_match_all("/[0-9]+/", $sale_end, $matches);
            $sale_end = $matches[0][0] * 24 * 60 * 60 +
                                $matches[0][1] * 60 * 60 +
                                    $matches[0][2] * 60 +
                                        $matches[0][3];
            $image_src = $node->filterXPath(IMAGE)->text();
            $db = new Database($this->db_name);
            $query = "INSERT INTO `" . $db->getDatabaseName() . "` 
                    (`title`, `link`, `validity`, `sale_end`, `image_src`) 
                    VALUES ('$title', '$link', '$validity', '$sale_end', '$image_src')";
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
        $this->count_coupons = end($coupons);
    }

    /**
     * @return int
     */
    public function getCountCoupons(): int
    {
        return $this->count_coupons;
    }

}