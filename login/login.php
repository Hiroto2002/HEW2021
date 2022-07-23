<?php

// noticeを消す
ini_set("display_errors", 0);

require("../dbconnect.php");
require("../common.php");

// 会員登録完了
if ($_SERVER["HTTP_REFERER"] === "http://localhost/HEW/join/check.php") {

    print('
        <div class="modal-pop">
        <div class="bg js-modal-close"></div>
        <div class="modal-pop-main">
        <img src="../image/complete.jpg" />
        </div>
        </div>
        '
    );
}

//  ログアウト
if ($_SERVER["HTTP_REFERER"] == "http://localhost/HEW/prof/prof.php") {

    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    print('
                <div class="modal-pop">
                <div class="bg js-modal-close"></div>
                <div class="modal-pop-main">
                <img src="../image/complete.jpg" />
                </div>
                </div>
                '
    );

    //cookie情報も削除
    setcookie("userID", "", time() - 3600);
    setcookie("pass", "", time() - 3600);
}


//cookieにemailの値の値が入っていれば
if ($_COOKIE["userID"] != "") {
    // $_POSTに対してcookieから保存されている値を呼び戻す
    $_POST["userID"] = $_COOKIE["userID"];
    $_POST["pass"] = $_COOKIE["pass"];
    $_POST["save"] = "on";
}

if (!empty($_POST)) {
    // ログインの処理

    //メールアドレスとパスワードが入力されているかチェック
    if ($_POST["userID"] != "" && $_POST["pass"] != "") {
        // 登録されているユーザーが居るか確認
        $login = $db->prepare('SELECT * FROM user WHERE userID=? AND pass=?');

        //passwordについては、check.phpで暗号化した状態で保存されている
        $login->execute(array("@" . $_POST["userID"], sha1($_POST["pass"])));

        //レコードの取り出し
        $member = $login->fetch();
        // ユーザーが見つかったら
        if ($member) {
            //ユーザー情報をセッションに保存
            $_SESSION["userID"] = $member["userID"];
            //ログインした時間を管理する
            $_SESSION["time"] = time();

            //ログイン情報を記録するにチェックを付けた場合
            if ($_POST["save"] == "on") {

                // 入力されたemailとpasswordの値をcookieに保存する
                //cookieの保存期間は14日間
                setcookie('userID', $_POST["userID"], time() + 60 * 60 * 24 * 14);
                setcookie('pass', $_POST["pass"], time() + 60 * 60 * 24 * 14);
            }

            // トップページに移動
            header("Location: ../timeline/timeline.php");
            exit();
        } else {
            //ログインエラー
            $error["login"] = "failed";
        }
    } else {
        $error["login"] = "blank";
    }
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="icon" href="../image/prof.jpg">
    <title>ログイン</title>
</head>

<body>
    <div class="container">
        <main id="login">

            <div id="login_box_left">
                <img src="../image/samecari_logo.png" class="login_logo">
                <img src="../image/phone.jpg" class="phone">
                <div class="img-wrap">
                    <img src="../image/site01.png" class="site">
                    <img src="../image/site02.png" class="site">
                    <img src="../image/site03.png" class="site">
                </div>
            </div>

            <div id="login_box_right">
                <h2>ログイン</h2>

                <div class="img_area">
                    <img src="../image/prof.svg" alt="アイコン" />
                </div>

                <form action="" method="post">
                    <dl class="form">

                        <dd>ID</dd>

                        <div>
                            <input type="text" name="userID" size="35" maxlength="255" value="<?php echo Xss($_POST["userID"], ENT_QUOTES); ?>" placeholder="guest01" />
                        </div>

                        <dd>password</dd>

                        <div>
                            <input type="password" name="pass" size="35" maxlength="255" value="<?php echo Xss($_POST["pass"], ENT_QUOTES); ?>" placeholder="12345678" />
                        </div>

                        <?php if ($error["login"] == "blank") : ?>
                            <p class="error">* メールアドレスとパスワードをご記入ください</p>
                        <?php endif; ?>

                        <?php if ($error["login"] == "failed") : ?>
                            <p class="error">* ログインに失敗しました。正しくご記入ください。</p>
                        <?php endif; ?>

                        <dd>ログイン情報の記録</dd>
                        <dt>
                            <input id="save" type="checkbox" name="save" value="on" />
                            <label for="save">次回からは自動的にログインする</label>
                        </dt>
                    </dl>
                    <div class="link"><input type="submit" value="ログイン" /></div>
                </form>

                <div class="link">
                    <li><a href="../join/index.php">会員登録</a></li>
                </div>
            </div>
        </main>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="./jquery.ripples-min.js"></script>
    <script src="../timeline/login.js"></script>
</body>