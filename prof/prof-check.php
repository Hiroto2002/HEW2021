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


// DBから情報を読み取る
$statement = $db->prepare("SELECT * FROM user WHERE userID = ?");

$statement->execute(array($_SESSION["userID"]));

$user = $statement->fetch();


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/prof.jpg">
    <title>プロフィール確認</title>
    <link href="../css/common.css" rel="stylesheet">
</head>


<body>
    <main>
        <header class="listing-header">
            <div class="back">
                <a href="./prof.php">＜</a>
            </div>
            <span class="back_text">戻る</span>
        </header>

        <h2 class="title">プロフィール</h2>
        <dl class="form">
            <dd>氏名</dd>

            <dt>
                <?php echo $user["name"]; ?>
            </dt>

            <dd>メールアドレス</dd>

            <dt>
                <?php echo $user["email"]; ?>
            </dt>

            <dd>生年月日</dd>

            <dt>
                <?php print($user["birthday"]); ?>
                </dd>

            <dd>郵便番号</dt>
            <dt>
                <?php echo $user["zip"]; ?>
            </dt>
            <dd>住所</dd>
            <dt>
                <?php echo $user["address"]; ?>
            </dt>
            <dd>クレジット</dd>
            <dt>
                <?php
                print("カード番号<br>" . $user["card_num"] . "<br>有効期限<br>" . $user["expiration"] . "<br>セキュリテイコード<br>" . $user["card_code"]);
                ?>
            </dt>
            <dd>パスワード</dd>
            <dt>
                【表示されません】
            </dt>
            <dd>id</dt>
            <dt>
                <?php echo $user["pass"]; ?>
            </dt>
            <dd>ユーザーネーム</dd>
            <dt>
                <?php echo $user["nickname"]; ?>
            </dt>
            <dd>写真など</dd>

            <dt>
                <?php
                $path = $user["icon"];
                print("<img src='../join/upload_file/" . $path . "' class='check' alt ='アイコン' />");
                ?>
            </dt>
        </dl>

        <div class="register">

            <form action="./prof-edit.php" method="post">
                <input type="hidden" value="submit" name="change" />
                <input type="submit" value="変更する" />
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
    <script src="./join.js"></script>
    <script src="../timeline/timeline.js"></script>

</body>

</html>