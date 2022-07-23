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
    <title>rank</title>
</head>


<body>
    <div class="wrap">
        <header>
            <nav class="g-nav1">
                <ul class="g-nav1_wrap">
                    <li><a href="cart.php"><img class="cart" src="../image/cart.png" alt="カートボタン" /></a></li>
                </ul>
            </nav>
        </header>

        <main class="sale">

            <table class="rank_box">
                <div>
                    <h2 id="tl_title">ランキング</h2>
                </div>

                <?php
                //商品を表示する
                $statement = $db->query("SELECT productImage,productID,price,cnt_click + cnt_cart AS ranking FROM product ORDER BY ranking DESC;");

                $i = 0;
                $j = 0;
                while ($product = $statement->fetch(PDO::FETCH_ASSOC)) :

                    if ($j == 0) {
                        print("<tr>
                                <img class='Tiara fadeUpTrigger' id='gold' src='../image/first.png'>    
                            ");
                    }

                    if ($j == 1) {
                        print("
                                <img class='Tiara fadeUpTrigger' id='silver' src='../image/second.png'>    
                                ");
                    }

                    if ($j == 2) {
                        print("
                                <img class='Tiara fadeUpTrigger' id='bronze' src='../image/third.png'>
                            ");
                    }

                    if ($j == 3) {
                        print("</tr>");
                    }



                    if ($i == 0) {
                        print("<tr>");
                    }


                    if ($i == 3) {
                        print("</tr>");
                        $i = 0;
                    }
                ?>
                    <td><a href='../listing/listing-detail.php?id=<?php echo Xss($product["productID"]) ?>'>
                            <img src='../listing/upload_file/<?php print Xss($product["productImage"]) ?>' alt='商品画像' id='rank_sale' class="fadeUpTrigger" /></td>
                    <td class='price fadeUpTrigger'>
                        <p><?php print Xss("￥" . number_format($product["price"])) ?></p>
                    </td>
                    </a>

                <?php
                    $i++;
                    $j++;
                endwhile;
                ?>

            </table>
        </main>
        
    <footer>
        <img src="../image/samecari_logo.png">

        <span>Copyright © 2022
            hirotomaeda.
            All rights reserved.
        </span>
    </footer>
    </div>
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
    <!-- jQuery CDNとjsファイル読み込み -->
    <script src="../JQuery.js"></script>
    <script src="./timeline.js"></script>
</body>

</html>