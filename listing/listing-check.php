<?php
// noticeを消す

ini_set("display_errors", 0);

require("../dbconnect.php");
require("../common.php");


$category = $_SESSION["listing"]["category"];
$pname = $_SESSION["listing"]["pname"];
$product_inf = $_SESSION["listing"]["product_inf"];
$deli_charge = $_SESSION["listing"]["deli_charge"];
$deli_souce = $_SESSION["listing"]["deli_souce"];
$deli_day = $_SESSION["listing"]["deli_day"];
$price = $_SESSION["listing"]["price"];
$image = "./upload_file/" . $_SESSION["listing"]["image"];
$_SESSION["listing"]["image-path"] = $image;
// 入力内容を確認ボタンクリック時に
//$_POSTにはactionという名前のデータが入ってくるので、
//if文の中の処理が実行される

if (!empty($_POST)) {

    // INSERT文の本来の書き方
    // INSERT INTO members (name, email, password, picture, created)
    // VALUES(?,?,?,? NOW());
    // 登録処理をする
    $statement = $db->prepare("INSERT INTO product (pname,price,product_inf,category,deli_charge,deli_souce,deli_day,
        userID,cnt_click,cnt_cart,productImage) VALUES(?,?,?,?,?,?,NOW(),?,0,0,?)");

    echo $ret = $statement->execute(array(

        $pname,
        $price,
        $product_inf,
        $category,
        $deli_charge,
        $deli_souce,
        $_SESSION["userID"],
        $_SESSION["listing"]["image"]
    ));

    //セッション情報を削除する
    unset($_SESSION["listing"]);

    // 完了ページに転送する
    header("Location: ../timeline/timeline.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/common.css" rel="stylesheet">
    <!-- <script src="./a.js"></script> -->

    <link rel="icon" href="../image/prof.jpg">
    <title>確認画面</title>
</head>

<body>
    <main>
        <header class="listing-header">
            <div class="back">
                <a href="
            <?php
            $before_url = $_SERVER['HTTP_REFERER'];
            print($before_url);
            ?>">＜</a>
            </div>
            <span class="back_text">戻る</span>

        </header>
        <br>
        <h2 class="title">確認</h2>
        <dl class="form">
            <dd>写真</dt>

            <dt>
                <?php
                $path = $_SESSION["listing"]["image"];
                print("<img src='./upload_file/" . $path . "' class='check'/>");
                ?>
            </dt>

            <dd>商品の詳細</dd>

            <dd>カテゴリ</dt>
            <dt>
                <?php echo Xss($category); ?>
            </dt>

            <dd>商品名と説明</dd>

            <dd>商品名</dd>
            <dt>
                <?php echo Xss($pname); ?>
            </dt>
            <dd>商品の説明</dd>
            <dt>
                <?php echo Xss($product_inf); ?>
            </dt>

            <dd>配送について</dd>

            <dd>配送料の負担</dd>
            <dt>
                <?php echo Xss($deli_charge); ?>
            </dt>

            <dd>発送元の地域</dt>
            <dt>
                <?php echo Xss($deli_souce); ?>
            </dt>

            <dd>販売価格</dd>
            <dt>
                <?php echo Xss($price); ?>
            </dt>

        </dl>

        <div class="register">

            <form action="" method="post">
                <input type="hidden" value="submit" name="register" />
                <input type="submit" value="登録" />
            </form>

        </div>

    </main>
    <footer>
        <img src="../image/samecari_logo.png">

        <span>Copyright © 2022
            hirotomaeda.
            All rights reserved.
        </span>
    </footer>
    <script src="../JQuery.js"></script>
    <script src="../timeline/timeline.js"></script>
    <script src="./listing.js"></script>
</body>

</html>