<?php


require("../dbconnect.php");
require("../common.php");

    // noticeを消す
    ini_set("display_errors", 0);

    //完了画像
    $before_url = $_SERVER['HTTP_REFERER'];
    if($before_url == "http://localhost/HAL/login/login.php"){

        print('
        <div class="modal-pop">
            <div class="bg js-modal-close"></div>
            <div class="modal-pop-main">
                <img src="../image/login-complete.jpg" />
            </div>
        </div>');
    }


    if (isset($_SESSION["userID"]) && $_SESSION["time"] + 3600 > time()){
        //ログインした時間を現在に更新
        // print($_SESSION["userID"]);
        $_SESSION["time"] = time();
        
        // セッション変数のidを使って、ユーザー情報を呼び出す
        $members = $db->prepare("SELECT * FROM user WHERE userID=?");
        $members->execute(array($_SESSION["userID"]));
        $member = $members->fetch();

    } else{
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
    <title>タイムライン</title>
</head>


<body>
    <div class="wrap">
        <header class="g-nav">
            <nav class="g-nav1">
                <ul class="g-nav1_wrap">
                    <li><img class="search" src="../image/search.png" alt="検索ボタン"></li>
                    <li><img class="search-cancel" src="../image/cancel.png" alt="キャンセルボタン"></li>
                    <li>
                        <!--検索する-->
                        <form action= "" class="invisible-search" method="post">
                            <input type="text" class="tl-search" placeholder="なにをお探しですか？" name="search" value="<?php Xss($_POST["search"])?>">
                            <input type="image" class="tl-search-btn" src="../image/search.png" alt="検索する"/>
                        </form>
                    </li>
                    <li><a href="cart.php"><img class="cart"src="../image/cart.png" alt="カートボタン"/></a></li>
                    <li class="plus"><a href="../listing/listing.php">+</a></li>
                </ul>    
            </nav>
        </header>
        
        
        <main class="sale" id="sale_timeline">

        <div ><img src="../image/samecari_logo.png" alt="profile" id="logo"></div>

        <div class="timeline-header fadeUpTrigger">
                <h2><span>"食"</span>のフリマアプリ</h2>
                <p>samecariでグルメになろう！</p>
        </div>
        
            <div class="scrolldown2"><span>Scroll</span></div>
        
        <div>
            <h2 id="tl_title">商品一覧</h2>
        </div>
        <table>

        <?php

        //検索する
        if(isset($_POST["search"])){

            $statement = $db->prepare("SELECT pname,productID,productImage,price FROM product WHERE pname LIKE ? ORDER BY deli_day DESC;");
            $statement->execute(
                    array('%'.$_POST['search'].'%')
                );
                $i = 0;
                while($product = $statement->fetch(PDO::FETCH_ASSOC)):
                    if($i == 0){
                        print("<tr>");
                    }

                    if($i == 4){
                        print("</tr>");
                        $i = 0;
                    }

        ?>
                        <td><a href='../listing/listing-detail.php?id=<?php echo Xss($product["productID"])?>'>
                            <img src='../listing/upload_file/<?php print Xss($product["productImage"])?>' alt = '商品画像' id='sale' class="fadeUpTrigger"/></td> 
                        <td class='price'><p><?php print Xss("￥" . number_format($product["price"]))?></p></td>
                        </a>

                <?php
                    $i++;
                endwhile;
        }else{
    



        //商品を表示する
            $statement = $db->query("SELECT productImage,productID,price FROM product ORDER BY deli_day DESC;");
            
                    $i = 0;
                while($product = $statement->fetch(PDO::FETCH_ASSOC)):
                    if($i == 0){
                        print("<tr>");
                    }


                    if($i == 4){
                        print("</tr>");
                        $i = 0;
                    }
                    // print_r($product["productImage"]);

        ?>
                    
                        <td><a href='../listing/listing-detail.php?id=<?php echo Xss($product["productID"])?>'>
                            <img src='../listing/upload_file/<?php print Xss($product["productImage"])?>' alt = '商品画像' id='sale' class="fadeUpTrigger"/></td> 
                        <td class='price fadeUpTrigger'><p><?php print Xss("￥" . number_format($product["price"]))?></p></td>
                    </div>    
                    </a>

                <?php
                    $i++;
                endwhile;}?>

        </table>
    </main>

    <footer>
        <img src="../image/samecari_logo.png">

        <span>
            Copyright © 2022
            hirotomaeda.
            All rights reserved.
        </span>
    </footer>
    
            <ul class="g-nav2_wrap">
                <li><a href=""><img class="home" src="../image/home.svg" alt="ホームボタン"><p class="home_title">HOME</p></a></li>
                <li><a href="../dm/dm_select.php"><img class="dm"src="../image/dm.svg" alt="dmボタン"/><p class="dm_title">DM</p></a></li>
                <li><a href="./rank.php"><img class="rank" src="../image/rank.svg" alt="ランキングボタン"/><p class="rank_title">RANK</p></a></li>
                <li><a href="../prof/prof.php"><img class="prof" src="../image/prof.svg" alt="プロフィールボタン"><p class="profile_title">MY PAGE</p></a></li>
            </ul>
    </div>
        
    
    <!-- jQuery CDNとjsファイル読み込み -->
    <script src="../JQuery.js"></script>
    <script src="./timeline.js"></script>
</body>
</html>

