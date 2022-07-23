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
    <title>プロフィール</title>
</head>


<body>
    <div class="wrap">
        <main>
            <!-- 自分のプロフィール -->
            <?php
            if (isset($_POST["and_more"])) :
                $users = $db->prepare("SELECT * FROM user WHERE userID=?");
                $users->execute(array($_POST["and_more"]));
                $user = $users->fetch();
            ?>
                <img src="<?php print("../join/upload_file/"  . $user["icon"]); ?>" alt="プロフィール" id="profile-icon"></dd>

                <div class="prof-edit">
                    <div id="prof_id"><?php print($user["userID"]) ?></div>
                    <div id="prof_name"><?php print($user["nickname"]) ?></div>
                    <form method="post" name="form1" action="../dm/dm_select.php">
                        <input type="hidden" name="dm" value="<?php echo Xss($user["userID"]) ?>">
                        <a href="javascript:form1.submit()"> メッセージを送る </a>
                    </form>
                </div>

                <div class="wrapper">

                    <ul class="tab">
                        <li><a href="#sale">投稿済み商品</a></li>
                    </ul>

                    <div id="sale" class="area">
                        <table>
                            <?php

                            //商品を表示する
                            $statement = $db->prepare("SELECT productImage,productID,userID FROM product WHERE userID=? ORDER BY deli_day DESC;");
                            $statement->execute(array($user["userID"]));

                            $i = 0;
                            while ($product = $statement->fetch(PDO::FETCH_ASSOC)) :
                                if ($i == 0) {
                                    print("<tr>");
                                }


                                if ($i == 6) {
                                    print("</tr>");
                                    $i = 0;
                                }
                                // print_r($product["productImage"]);

                            ?>
                                <td><a href='../listing/listing-detail.php?id=<?php echo Xss($product["productID"]) ?>'>
                                        <img src='../listing/upload_file/<?php print Xss($product["productImage"]) ?>' alt='商品画像' id='prof-sale' /></td>
                                </a>

                            <?php
                                $i++;
                            endwhile; ?>
                            <!--/area-->
                        </table>
                    </div>


                    <div id="cart" class="area">
                        <table>
                            <?php



                            //商品を表示する
                            $statement = $db->query("SELECT p.productImage,c.productID,c.userID AS cart_userID FROM product p, cart c WHERE p.productID = c.productID ORDER BY cartID DESC;");

                            $i = 0;
                            while ($product = $statement->fetch(PDO::FETCH_ASSOC)) :

                                if ($_SESSION["userID"] == $product["cart_userID"]) :

                                    if ($i == 0) {
                                        print("<tr>");
                                    }

                                    if ($i == 6) {
                                        print("</tr>");
                                        $i = 0;
                                    }
                                    // print_r($product["productImage"]);

                            ?>

                                    <td>
                                        <a href='../listing/listing-detail.php?id=<?php echo Xss($product["productID"]) ?>'>
                                            <img src='../listing/upload_file/<?php print Xss($product["productImage"]) ?>' alt='商品画像' id='prof-sale' />
                                    </td>

                                    <form action="" method="post">
                                        <input type='hidden' name='cart' value='<?php echo $product["productID"] ?>'>
                                        <td><input type="image" class="cancel" src="../image/cancel.png" alt="キャンセル"></td>
                                    </form>

                                    </a>

                            <?php $i++;
                                endif;
                            endwhile;
                            ?>

                        </table>

                        <!--/area-->
                    </div>
                    <!--wrapper-->
                </div>



                <ul class="g-nav2_wrap">
                    <li><a href="../timeline/timeline.php"><img class="home" src="../image/home.svg" alt="ホームボタン">
                            <p class="home_title">HOME</p>
                        </a></li>
                    <li><a href="../dm/dm_select.php"><img class="dm" src="../image/dm.svg" alt="dmボタン" />
                            <p class="dm_title">DM</p>
                        </a></li>
                    <li><a href="../timeline/rank.php"><img class="rank" src="../image/rank.svg" alt="ランキングボタン" />
                            <p class="rank_title">RANK</p>
                        </a></li>
                    <li><a href=""><img class="prof" src="../image/prof.svg" alt="プロフィールボタン">
                            <p class="profile_title">PROFILE</p>
                        </a></li>
                </ul>
        </main>
    </div>
    <!-- jQuery CDNとjsファイル読み込み -->
    <script src="../JQuery.js"></script>
    <script src="../timeline/timeline.js"></script>
    <script src="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/5-4-1/js/5-4-1.js"></script>
</body>

</html>


<?php exit();
            endif; ?>

<img src="<?php print("../join/upload_file/"  . $member["icon"]); ?>" alt="プロフィール" id="profile-icon"></dd>

<div class="prof-edit">
    <div><?php print($member["userID"]) ?></div>
    <div><?php print($member["name"]) ?></div>
    <ul>
        <li><a href="./prof-check.php">プロフィールを編集する</a></li>
        <li><a href="../login/login.php">
                <p>ログアウトする</p>
            </a></li>
    </ul>
</div>


<div class="wrapper">

    <ul class="tab">
        <li><a href="#sale">投稿済み商品</a></li>
        <li><a href="#cart">カート</a></li>
    </ul>

    <div id="sale" class="area">
        <table>
            <?php

            //商品を表示する
            $statement = $db->prepare("SELECT productImage,productID,userID FROM product WHERE userID=? ORDER BY deli_day DESC;");
            $statement->execute(array($_SESSION["userID"]));

            $i = 0;
            while ($product = $statement->fetch(PDO::FETCH_ASSOC)) :
                if ($i == 0) {
                    print("<tr>");
                }


                if ($i == 6) {
                    print("</tr>");
                    $i = 0;
                }
                // print_r($product["productImage"]);

            ?>
                <td><a href='../listing/listing-detail.php?id=<?php echo Xss($product["productID"]) ?>'>
                        <img src='../listing/upload_file/<?php print Xss($product["productImage"]) ?>' alt='商品画像' id='prof-sale' /></td>
                </a>

            <?php
                $i++;
            endwhile; ?>
            <!--/area-->
        </table>
    </div>


    <div id="cart" class="area">
        <table>
            <?php



            //商品を表示する
            $statement = $db->query("SELECT p.productImage,c.productID,c.userID AS cart_userID FROM product p, cart c WHERE p.productID = c.productID ORDER BY cartID DESC;");

            $i = 0;
            while ($product = $statement->fetch(PDO::FETCH_ASSOC)) :

                if ($_SESSION["userID"] == $product["cart_userID"]) :

                    if ($i == 0) {
                        print("<tr>");
                    }

                    if ($i == 6) {
                        print("</tr>");
                        $i = 0;
                    }
                    // print_r($product["productImage"]);

            ?>

                    <td>
                        <a href='../listing/listing-detail.php?id=<?php echo Xss($product["productID"]) ?>'>
                            <img src='../listing/upload_file/<?php print Xss($product["productImage"]) ?>' alt='商品画像' id='prof-sale' />
                    </td>

                    <form action="" method="post">
                        <input type='hidden' name='cart' value='<?php echo $product["productID"] ?>'>
                        <td><input type="image" class="cancel" src="../image/cancel.png" alt="キャンセル"></td>
                    </form>

                    </a>

            <?php $i++;
                endif;
            endwhile;
            ?>

        </table>

        <!--/area-->
    </div>
    <!--wrapper-->
    
</div>
<footer>
        <img src="../image/samecari_logo.png">

        <span>Copyright © 2022
            hirotomaeda.
            All rights reserved.
        </span>
    </footer>


<ul class="g-nav2_wrap">
    <li><a href="../timeline/timeline.php"><img class="home" src="../image/home.svg" alt="ホームボタン">
            <p class="home_title">HOME</p>
        </a></li>
    <li><a href="../dm/dm_select.php"><img class="dm" src="../image/dm.svg" alt="dmボタン" />
            <p class="dm_title">DM</p>
        </a></li>
    <li><a href="../timeline/rank.php"><img class="rank" src="../image/rank.svg" alt="ランキングボタン" />
            <p class="rank_title">RANK</p>
        </a></li>
    <li><a href=""><img class="prof" src="../image/prof.svg" alt="プロフィールボタン">
            <p class="profile_title">PROFILE</p>
        </a></li>
</ul>
</main>
</div>
<!-- jQuery CDNとjsファイル読み込み -->
<script src="../JQuery.js"></script>
<script src="../timeline/timeline.js"></script>
<script src="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/5-4-1/js/5-4-1.js"></script>
</body>

</html>