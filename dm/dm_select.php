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
    <title>DM選択</title>
</head>


<body>
    <header>
        <nav class="g-nav1">
            <ul class="g-nav1_wrap">
                <li><a href="../timeline/cart.php"><img class="cart" src="../image/cart.png" alt="カートボタン" /></a></li>
            </ul>

        </nav>
    </header>
    <main id="dm-select">

        <div id="dm_title" class="fadeUpTrigger"><span>ダイレクトメッセージを送る</span></div>

        <?php

        $dms = $db->query("SELECT * FROM dm_relation");

        if (isset($_POST["dm"])) {
            $statements = $db->prepare("INSERT INTO dm_relation(userID,destination_user_id,modified) VALUES(?,?,NOW())");
            $statements->execute(array($_SESSION["userID"], $_POST["dm"]));

            //$statements = $db->prepare("INSERT INTO dm_relation(userID,destination_user_id) VALUES(?,?)");
            //$statements->execute(array($_POST["dm"],$_SESSION["userID"]));
            //自分からメッセージ
            $dm_relations = $db->prepare("SELECT  d.textID AS textID, d.destination_user_id AS destination_user_id, 
            d.userID AS userID, u.icon AS icon,u.nickname AS nickname 
            FROM dm_relation d inner join user as u
            on d.userID = u.userID
            WHERE  (d.userID=?) OR (d.destination_user_id=?)
            ORDER BY d.modified DESC;
            ");
            $dm_relations->execute(array($_SESSION["userID"], $_SESSION["userID"]));
        }else{

        //dm選択を表示する、誰かからのメッセージ
        $dm_relations = $db->prepare("SELECT  d.textID AS textID, d.destination_user_id AS destination_user_id, 
        d.userID AS userID, u.icon AS icon,u.nickname AS nickname 
        FROM dm_relation d inner join user as u
        on d.destination_user_id = u.userID
        WHERE  (d.userID=?) OR (d.destination_user_id=?)
        ORDER BY d.modified DESC;
        ");
        $dm_relations->execute(array($_SESSION["userID"], $_SESSION["userID"]));
    }


        while ($dm_relation = $dm_relations->fetch(PDO::FETCH_ASSOC)) :

            // 最新のDMを表示
            $dms = $db->prepare("SELECT  d.text,r.userID,r.destination_user_id FROM dm d, dm_relation r 
            WHERE r.destination_user_id=? AND r.userID=? AND d.destination_user_id = r.destination_user_id
            ORDER BY d.created_at DESC;");
            $dms->execute(array($dm_relation["destination_user_id"], $_SESSION["userID"]));
            $dm = $dms->fetch(PDO::FETCH_ASSOC);

        ?>
            <a href='./dm.php?id=<?php if ($dm_relation["userID"] == $_SESSION["userID"]) {
                                        echo $dm_relation["destination_user_id"];
                                    } else {
                                        echo $dm_relation["userID"];
                                    } ?>'>
                <div class='dm-account fadeUpTrigger'>
                    <img class='dm-icon' src='../join/upload_file/<?php echo $dm_relation["icon"] ?>' alt='プロフィール'>
                    <span class='yname'><?php print($dm_relation["nickname"]) ?></span>
                    <span class='new_text'><?php
                                            if (strlen($dm["text"]) > 23) {
                                                $longdm = (mb_substr($dm["text"], 0, 22));
                                                print($longdm . "…");
                                            } else {
                                                print($dm["text"]);
                                            } ?></span>
                </div>
            </a>

        <?php endwhile; ?>

    </main>
    <footer id="dm_footer">
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
        <li><a href=""><img class="dm" src="../image/dm.svg" alt="dmボタン" />
                <p class="dm_title">DM</p>
            </a></li>
        <li><a href="../timeline/rank.php"><img class="rank" src="../image/rank.svg" alt="ランキングボタン" />
                <p class="rank_title">RANK</p>
            </a></li>
        <li><a href="../prof/prof.php"><img class="prof" src="../image/prof.svg" alt="プロフィールボタン">
                <p class="profile_title">PROFILE</p>
            </a></li>
    </ul>
    <script src="../JQuery.js"></script>
    <script src="../timeline/timeline.js"></script>


</body>

</html>