<?php


require("../dbconnect.php");
require("../common.php");

// noticeを消す
ini_set("display_errors", 0);


if (isset($_SESSION["userID"]) && $_SESSION["time"] + 3600 > time()) {
    //ログインした時間を現在に更新
    // print($_SESSION["userID"]);
    $_SESSION["time"] = time();

    // セッション変数のidを使って、ユーザー情報を呼び出す
    $members = $db->prepare("SELECT * FROM user WHERE userID=?");
    $members->execute(array($_SESSION["userID"]));
    $member = $members->fetch();
} else {
    // ログインしていない
    header('Location: ../login/login.php');
    exit();
}


// 個別番号
if (isset($_GET['id'])) {
    $productID = $_GET['id'];
}

//カートから商品を消す
if (isset($_POST["cart"])) {
    $statement = $db->prepare("DELETE FROM cart WHERE userID = ? AND productID = ? ");
    $statement->execute(
        array($_SESSION["userID"], Xss($_POST["cart"]))
    );
}


?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/reset.css" rel="stylesheet">
    <link href="../css/common.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300&display=swap" rel="stylesheet">
    <link rel="icon" href="../image/prof.jpg">
    <title>cart</title>
</head>


<body>
    <div class="wrap">
    <br><div id="dm_title" class="fadeUpTrigger"><span>カート一覧</span></div>

        <header>
            <nav class="g-nav1">
                <ul class="g-nav1_wrap">
                    <li><a href="cart.php"><img class="cart" src="../image/cart.png" alt="カートボタン" /></a></li>
                    <li><img class="dust" src="../image/gomi.svg" alt="削除" />
                        <img class="invisible-dust" src="../image/cancel.png" />
                    </li>
                </ul>
            </nav>
        </header>

        <main class="sale" id="sale_timeline">
            <table>
                <?php

                //商品を表示する
                $statement = $db->query("SELECT p.productImage,c.productID,p.price,c.userID AS cart_userID FROM product p, cart c WHERE p.productID = c.productID ORDER BY cartID DESC;");

                $i = 0;
                while ($product = $statement->fetch(PDO::FETCH_ASSOC)) :

                    if ($_SESSION["userID"] == $product["cart_userID"]) :

                        if ($i == 0) {
                            print("<tr>");
                        }

                        if ($i == 4) {
                            print("</tr>");
                            $i = 0;
                        }
                        // print_r($product["productImage"]);

                ?>

                        <td>
                            <a href='../listing/listing-detail.php?id=<?php echo Xss($product["productID"]) ?>'>
                                <img src='../listing/upload_file/<?php print Xss($product["productImage"]) ?>' alt='商品画像' id='sale' class="fadeUpTrigger" />
                        </td>

                        <form action="" method="post">
                            <input type='hidden' name='cart' value='<?php echo $product["productID"] ?>'>
                            <td><input type="image" class="cancel" src="../image/cancel.png" alt="キャンセル"></td>
                        </form>

                        <td class='price fadeUpTrigger'>
                            <p><?php print Xss("￥" . number_format($product["price"])) ?></p>
                        </td>
                        </a>

                <?php $i++;
                    endif;
                endwhile; ?>

            </table>
        </main>
        <ul class="g-nav2_wrap">
            <li><a href="../timeline/timeline.php"><img class="home" src="../image/home.svg" alt="ホームボタン">
                    <p class="home_title">HOME</p>
                </a></li>
            <li><a href="../dm/dm_select.php"><img class="dm" src="../image/dm.svg" alt="dmボタン" />
                    <p class="dm_title">DM</p>
                </a></li>
            <li><a href=""><img class="rank" src="../image/rank.svg" alt="ランキングボタン" />
                    <p class="rank_title">RANK</p>
                </a></li>
            <li><a href="../prof/prof.php"><img class="prof" src="../image/prof.svg" alt="プロフィールボタン">
                    <p class="profile_title">PROFILE</p>
                </a></li>
        </ul>
    </div>
    <script src="../JQuery.js"></script>
    <script src="./timeline.js"></script>

</body>

</html>