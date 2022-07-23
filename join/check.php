<?php
// noticeを消す

ini_set("display_errors", 0);

require("../dbconnect.php");
require("../common.php");

if (!isset($_SESSION["join"])) {
    print("aaa");
    header("Location: index.php");
    //強制的にindex.phpに転送する
    exit();
}

$email = $_SESSION["join"]["email"];
$iname = $_SESSION["join"]["iname"];
$fname = $_SESSION["join"]["fname"];
$year = $_SESSION["join"]["year"];
$month = $_SESSION["join"]["month"];
$day = $_SESSION["join"]["day"];
$zip1 = $_SESSION["join"]["zip1"];
$zip2 = $_SESSION["join"]["zip2"];
$address = $_SESSION["join"]["address"];
$card_num = $_SESSION["join"]["card_num"];
$exp_year = $_SESSION["join"]["exp_year"];
$exp_month = $_SESSION["join"]["exp_month"];
$card_code = $_SESSION["join"]["card_code"];
$userID = $_SESSION["join"]["userID"];
$pass = $_SESSION["join"]["pass"];
$nickname = $_SESSION["join"]["nickname"];
$icon = "./upload_file/" . $_SESSION["join"]["icon"];
$_SESSION["join"]["icon-path"] = $icon;
// 入力内容を確認ボタンクリック時に
//$_POSTにはactionという名前のデータが入ってくるので、
//if文の中の処理が実行される

if (!empty($_POST)) {

    // INSERT文の本来の書き方
    // INSERT INTO members (name, email, password, picture, created)
    // VALUES(?,?,?,? NOW());
    // 登録処理をする
    $statement = $db->prepare("INSERT INTO user (email, name, birthday, zip, address, card_num, 
            expiration, card_code, userID, pass, nickname, icon, created) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,NOW())");
    echo $ret = $statement->execute(array(

        $email,
        $iname . " " . $fname,
        $year . "年" . $month . "月" . $day . "日",
        $zip1 . "-" . $zip2,
        $address,
        $card_num,
        $exp_year . "年" . $exp_month . "月",
        $card_code,
        "@" . $userID,
        sha1($pass),
        $nickname,
        $_SESSION["join"]["icon"]
        //sha 1 :パスワードを暗号化している
    ));

    //セッション情報を削除する
    unset($_SESSION["join"]);

    // 完了ページに転送する
    header("Location: ../login/login.php");
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
    <link rel="icon" href="../image/prof.jpg">
    <title>確認画面</title>
</head>

<body>
    <div class="container">
        <main class="memberAdd">
            <div class="index_back">
                <a href="
            <?php
            $before_url = $_SERVER['HTTP_REFERER'];
            print($before_url);
            ?>">＜</a>
            </div>
            <span class="index_back_text">戻る</span>

            <h2 class="title">確認</h2>
            <dl class="form">
                <dd>氏名</dd>
                <dt>
                    <?php echo Xss($iname . " " . $fname); ?>
                </dt>
                <dd>メールアドレス</dt>
                <dt>
                    <?php echo Xss($email); ?>
                </dt>
                <dd>生年月日</dd>
                <dt>
                    <?php print($year . "年" . $month . "月" . $day . "日"); ?>
                </dt>
                <dd>郵便番号</dd>
                <dt>
                    <?php echo Xss($zip1 . "-" . $zip2); ?>
                </dt>
                <dd>住所</dt>
                <dt>
                    <?php echo Xss($address); ?>
                </dt>
                <dt>
                    <?php
                    print("<dd>カード番号</dd>" . Xss($card_num) . "<dd>有効期限</dd>" . Xss($exp_year . "年" . $exp_month . "月") . "<dd>セキュリテイコード</dd>" . Xss($card_code));
                    ?>
                </dt>
                <dd>パスワード</dd>
                <dt>
                    【表示されません】
                </dt>
                <dd>id</dd>
                <dt>
                    <?php echo Xss($userID); ?>
                </dt>
                <dd>ユーザーネーム</dd>
                <dt>
                    <?php echo Xss($nickname); ?>
                </dt>
                <dd>写真など</dd>

                <dt>
                    <?php
                    $path = $_SESSION["join"]["icon"];
                    print("<img src='./upload_file/" . $path . "' class='check'/>");
                    ?>
                </dt>
            </dl>


            <form action="" method="post">
                <input type="hidden" value="submit" name="register" />
                <input type="submit" value="登録" />
            </form>
        </main>
    </div>
    <script src="../JQuery.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="../login/jquery.ripples-min.js"></script>
    <script src="../timeline//login.js"></script>
</body>

</html>