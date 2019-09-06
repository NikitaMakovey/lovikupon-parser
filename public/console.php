<!DOCTYPE html>
<html>
<head>
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
        use App\CouponParser;

        $parser = new CouponParser("https://vladivostok.lovikupon.ru/today/", "lovikupon_db");
        echo "Уже куплено " . $parser->getContent() . " купонов";
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

