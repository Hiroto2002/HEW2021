<?php


require("../dbconnect.php");
require("../common.php");
ini_set("display_errors", 0);


//直接ログインを防ぐ
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

// 商品を消す
if (isset($_POST["delete"])) {

    $statement = $db->prepare("DELETE FROM product WHERE productID = ?");
    $statement->execute(array($productID));

    header(("Location: ../timeline/timeline.php"));
    exit();
}

?>



<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/prof.jpg">
    <title>商品詳細</title>
    <link href="../css/common.css" rel="stylesheet">
</head>


<body>
    <?php

    //商品購入確認
    if (isset($_POST["buy"])) {

        $statement = $db->prepare("DELETE FROM product WHERE productID = ?");
        $statement->execute(array($productID));

        header("Location: ../timeline/timeline.php");
        exit();
    }
    ?>

    <header class="listing-header">
        <div class="back"><a href="
            <?php

            if ($_SERVER['HTTP_REFERER'] === "http://localhost/HEW/listing/listing-edit.php?id=" . $productID) {
                print("../timeline/timeline.php");
            } else if($_SERVER['HTTP_REFERER'] === "http://localhost/HEW/listing/listing-detail.php?id=" . $productID){
                print("../timeline/timeline.php");
            }else {
                print($_SERVER['HTTP_REFERER']);
            }

            ?>

            "
            >＜</a>
            <span class="back_text">戻る</span>
        </div>
    </header>



    <?php

    // カートに入っているのか検索する
    $statement = $db->prepare("SELECT userID, productID, COUNT(*) AS 'count' FROM cart WHERE userID = ? AND productID = ? ");
    $statement->execute(
        array($_SESSION["userID"], $productID)
    );

    $counts = $statement->fetch();
    $count = $counts["count"];


    //入っていないなら
    if ($count == 0) {

        // カートボタンが押されたとき
        if (isset($_POST["cart"])) {
            print("<script>alert('カートに追加しました');</script>
                <style> 
                    .sale-cart{
                                background-color: rgb(230, 89, 89);
                            }
                </style>");

            //カートに追加する
            $statement = $db->prepare("INSERT INTO cart(userID,productID) VALUES(?,?)");
            $statement->execute(
                array($_SESSION["userID"], $productID)
            );

            //カートの状態を読み取る
            $statement = $db->prepare("SELECT p.*,u.icon,u.name FROM product p,  user u WHERE p.userID = u.userID AND p.productID = ?");

            $statement->execute(array($productID));

            $product = $statement->fetch();


            //カートのカウントを増やす
            $cnt_cart = $db->prepare("UPDATE product SET cnt_cart = ? WHERE productID = ?");
            $cnt_cart->execute(
                array($product["cnt_cart"] + 1, $productID)
            );

            print("<script>location.href='./listing-detail.php?id=" . $productID . "'</script>");

        }


        // すでにカートの中に入っているとき
    } else {

        print("
                <style> 
                    .sale-cart{
                                background-color: rgb(230, 89, 89);
                            }
                </style>");

        // カートボタンが押されたとき
        if (isset($_POST["cart"])) {
            // カートから削除する
            print("<script>alert('カートから削除しました');</script>
                <style> 
                    .sale-cart{
                                background-color: rgb(255, 187, 61);
                            }
                </style>");

            $statement = $db->prepare("DELETE FROM cart WHERE userID = ? AND productID = ? ");
            $statement->execute(
                array($_SESSION["userID"], $productID)
            );

            //カートの状態を読み取る
            $statement = $db->prepare("SELECT p.*,u.icon,u.name FROM product p,  user u WHERE p.userID = u.userID AND p.productID = ?");

            $statement->execute(array($productID));

            $product = $statement->fetch();


            // カートのカウントを減らす
            $cnt_cart = $db->prepare("UPDATE product SET cnt_cart = ? WHERE productID = ?");
            $cnt_cart->execute(
                array($product["cnt_cart"] - 1, $productID)
            );


            print("<script>location.href='./listing-detail.php?id=" . $productID . "'</script>");

        }
    }

    ?>

    <div class="wrap">
        <main class="sale">

            <?php

            // データベースから持ってくる
            $statement = $db->prepare("SELECT p.*,u.icon,u.name,u.nickname FROM product p,  user u WHERE p.userID = u.userID AND p.productID = ?");

            $statement->execute(array($productID));

            $product = $statement->fetch();

            //クリック数を追加する


            if ($_SERVER['HTTP_REFERER'] != "http://localhost/HEW/listing/listing-detail.php?id=" . $productID) {

                $count = $db->prepare("UPDATE product SET cnt_click = ? WHERE productID = ?");

                $count->execute(array($product['cnt_click'] + 1, $productID));

            }

            ?>

            <img id="sale-detail" src="../listing/upload_file/<?php print($product["productImage"]) ?>" alt="商品画像"><br>
            <span><?php print($product["pname"]) ?></span>

            <!-- ゴミ箱を表示 -->
            <?php if ($_SESSION["userID"] == $product["userID"]) : ?>
                <a><img class="sale-delete" src="../image/gomi.svg" alt="商品を削除する"></a>
            <?php endif; ?>

            <!-- カートを表示 -->
            <?php if ($_SESSION["userID"] != $product["userID"]) : ?>
                <!-- <a><img class="sale-cart" src="../image/cart.png" alt="カートに追加する"></a> -->
                <form action="" method="post">
                    <input type="hidden" name="cart" value="a">
                    <input type="image" class="sale-cart" src="../image/cart.png" alt="カートに追加する " id="cart">
                </form>
            <?php endif; ?>

            <form action="" method="post" class="delete_check">
                <input type="hidden" value="<?php Xss($_POST["delete"]) ?>" name="delete">
                <input type="submit" value="削除しますか？">
            </form>

            <p> <span id="accent"><?php print("￥" . $product["price"]) ?></span>(税込)送料込み </p>

            <strong>商品の情報</strong>
            <p><?php print($product["product_inf"]) ?></p>
            <strong>カテゴリ</strong>
            <p><?php print($product["category"]) ?></p>
            <strong>配送料の負担</strong>
            <p><?php print($product["deli_charge"]) ?></p>
            <strong>発送元の地域</strong>
            <p><?php print($product["deli_souce"]) ?></p>

            <strong>出品者</strong><br>
            <img class="sale-icon" src="../join/upload_file/<?php print($product["icon"]) ?>" alt="ユーザーアイコン">

            <?php if ($_SESSION["userID"] == $product["userID"]) : ?>
                <a href="../prof/prof.php"><?php print($product["nickname"]) ?></a><br>
                <form action="./listing-edit.php" method="get">
                    <input type="hidden" name="id" value="<?php print($productID) ?>">
                    <input type="submit" value="編集する" class="edit_btn">
                </form>
                <!-- <button type="button" onclick="location.href='./listing-edit.php'" class="edit_btn">編集する</button> -->
            <?php endif; ?>

            <?php if ($_SESSION["userID"] != $product["userID"]) : ?>
                <form method="post" name="form1" action="../prof/prof.php">
                    <input type="hidden" name="and_more" value="<?php echo Xss($product["userID"]) ?>">
                    <a href="javascript:form1.submit()" class="listing-submit"> <?php print($product["nickname"]) ?></a>
                </form>

                <button class="buy_check">購入する</button>

                <form action="" method="post" id="buy_btn" class="edit_btn">
                    <input type="hidden" value="a" name="buy">
                    <input type="submit" value="本当に購入しますか？" class="edit_btn">
                </form>
            <?php endif; ?>


        </main>
    </div>
    <footer>
        <img src="../image/samecari_logo.png">

        <span>Copyright © 2022
            hirotomaeda.
            All rights reserved.
        </span>
    </footer>

    <script src="../JQuery.js"></script>
    <script src="./listing.js"></script>
    <script src="../timeline/timeline.js"></script>
</body>

</html>