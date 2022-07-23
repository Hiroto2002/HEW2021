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

    $destination_user_ids = $db->prepare("SELECT * FROM dm_relation WHERE textID=?");
    $destination_user_ids->execute(array($_GET["id"]));
    $destination_user_id = $destination_user_ids->fetch();
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
    <link rel="icon" href="../image/prof.jpg">
    <title>DM</title>
</head>


<body>
    <header class="listing-header">
        <div class="back"><a href="./dm_select.php">＜</a></div>
    </header>
    <span class="back_text">戻る</span>
        <main id="dm-select">
            <?php 

        if(isset($_POST["message"])){
        // メッセージを追加する
        $add = $db->prepare("INSERT INTO dm (text, userID, destination_user_id, created_at) VALUES (?,?,?,NOW())");
        $add ->execute(array($_POST["message"],$_SESSION["userID"],$_GET["id"]));
                // メッセージの関係を更新する
            $add = $db->prepare("UPDATE dm_relation SET modified=NOW() WHERE userId=? AND destination_user_id=?");
            $add ->execute(array($_SESSION["userID"],$_GET["id"]));
        //めっせーーじ相手を追加する
            $add = $db->prepare("INSERT INTO dm_relation (userID, destination_user_id,modified) VALUES (?,?,NOW())");
            $add ->execute(array($_SESSION["userID"],$_GET["id"]));
        }

        // メッセージを表示する
            // $dms = $db->prepare("SELECT d.text AS text,d.userID AS d_userID, d.destination_user_id AS destination_user_id, u.icon AS icon, d.created_at AS created_at FROM dm d,user u 
            // WHERE d.userID = u.userID 
            // AND d.userID = ? AND d.destination_user_id = ? 
            // OR d.userID = ? AND d.destination_user_id = ? 
            // ORDER BY created_at DESC;");
            $dms = $db->prepare("SELECT u.userID as d_userID ,d.destination_user_id, d.text AS text, u.icon AS icon, d.created_at AS created_at FROM dm d inner join user as u
            on d.userID = u.userID
            WHERE  
            (d.userID = ? AND d.destination_user_id = ?) 
            or (d.userID = ? AND d.destination_user_id = ?) 
            ORDER BY created_at ;");
            $dms ->execute(array($_SESSION["userID"],$_GET["id"],$_GET["id"],$_SESSION["userID"]));


            while ($dm = $dms->fetch(PDO::FETCH_ASSOC)) :

                if($dm["d_userID"] == $_SESSION["userID"] && $dm["destination_user_id"] == $_GET["id"]){
                            
                            ?>
                <div class='dm-account-right'>
                    
                    <img class='dm-icon-right' src="../join/upload_file/<?php echo $dm["icon"] ?>">
                    <p class="dm_text_right"><?php echo $dm["text"] ?></p>
                    <p><?php echo $dm["created_at"] ?></p>
                </div>
                        
                <?php } else if($dm["destination_user_id"] == $_SESSION["userID"] && $_GET["id"] == $dm["d_userID"] ){
                    
                    ?>
                    <div class='dm-account'>
                        <img class='dm-icon' src="../join/upload_file/<?php echo $dm["icon"] ?>">
                        <p class="dm_text_left"><?php echo $dm["text"] ?></p>
                        <p><?php echo $dm["created_at"] ?></p>
                    </div>        
                <?php }
            endwhile; ?>
        

            <form action="" method="post" class="submit">
                <input type="text" name="message" value="<?php Xss($_POST["message"]) ?>" placeholder="内容を入力する"> 
                <input type="submit" value="送信する"> 
            </form>
        </main>
    <script src="../JQuery.js"></script>
    <script src="../timeline/timeline.js"></script>

</body>
</html>

