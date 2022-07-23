<?php

require("../dbconnect.php");
require("../common.php");

    // noticeを消す
    ini_set("display_errors", 0);


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


        // DBから情報を読み取る
        $statement = $db->prepare("SELECT * FROM user WHERE userID = ?");

        $statement->execute(array($_SESSION["userID"]));

        $user = $statement->fetch();

if(!isset($_POST["change"])){
    if (!empty($_POST)) {
        
        //エラー項目の確認
        if ($_POST["iname"] == "") {
            $error["iname"] = "blank";
        }

        if ($_POST["fname"] == "") {
            $error["fname"] = "blank";
        }

        if ($_POST["email"] == "") {
            $error["email"] = "blank";
        }

        $reg_str = "/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/";
        if (!preg_match($reg_str, $_POST["email"])) {
            $error["email"] = "miss";
        }

        //重複アカウントのチェック
        $member = $db->prepare("SELECT COUNT(*) AS cnt FROM user WHERE email=?");
        $member->execute(
            array($_POST["email"])
        );

        $record = $member->fetch();

        // 指定したメールアドレスが1行でもあれば
        // 重複していることがわかる
        if ($record["cnt"] > 0) {
            $error["email"] = "duplicate";
        }

        if ($_POST["year"] == "") {
            $error["year"] = "blank";
        }
        
        if ($_POST["month"] == "") {
            $error["month"] = "blank";
        }
        
        if ($_POST["day"] == "-") {
            $error["day"] = "blank";
        }

        
        if ($_POST["zip1"] == "") {
            $error["zip1"] = "blank";
        }
        
        if ($_POST["zip2"] == "") {
            $error["zip2"] = "blank";
        }

        if(!is_numeric($_POST["zip1"]) || !is_numeric($_POST["zip2"])){
            if(!isset($error)){
                $error["zip"] = "miss";   
            }
        }
        
        if ($_POST["address"] == "") {
            $error["address"] = "blank";
        }

        if ($_POST["card_num"] == "") {
            $error["card_num"] = "blank";
        }

        if(!is_numeric($_POST["card_num"])){
            $error["card_num"] = "miss";   
        }
        
        if ($_POST["exp_year"] == "") {
            $error["exp_year"] = "blank";
        }

        if ($_POST["exp_month"] == "") {
            $error["exp_month"] = "blank";
        }
        
        if ($_POST["card_code"] == "") {
            $error["card_code"] = "blank";
        }

        if(!is_numeric($_POST["card_code"]) || !is_numeric($_POST["card_code"])){
            $error["card_code"] = "miss";   
        }

        if (strlen($_POST["pass"]) < 6) {
            $error["pass"] = "length";
            //strlen(文字の長さを取得)
        }
        
        if ($_POST["nickname"] == "") {
                $error["nickname"] = "blank";
        }

        if ($_POST["pass"] == "") {
            $error["pass"] = "blank";
        }


        if ($_FILES["icon"]["error"] == 4) {
            $error["icon"] = "blank";
        }
        
        $fileName = $_FILES["icon"]["name"];
        if (!empty($fileName)) {
            $ext = substr($fileName, -4);

        if ($ext != ".jpg" && $ext != '.gif' && $ext != 'jpeg'&& $ext != '.png') {
                $error["icon"] = "type";
            }
        }
        
        
        //エラーがなければ
        if (empty($error)) {
            //画像をアップロードする
            $icon = date("YmdHis") . $_FILES["icon"]["name"];
            move_uploaded_file($_FILES["icon"]["tmp_name"],"../join/upload_file/" . $icon);
            unlink("../join/upload_file/" . $user["icon"]);
            

            $statement = $db->prepare("UPDATE user SET email=?, name=?, birthday=?, zip=?, address=?, card_num=?, 
            expiration=?, card_code=?, pass=?, nickname=?, icon=?, created=NOW() WHERE userID=?");
            $statement->execute(
                array(
            $_POST["email"],
            $_POST["iname"] . " " . $_POST["fname"],
            $_POST["year"] . "年". $_POST["month"] . "月" . $_POST["day"] . "日",
            $_POST["zip1"] . "-" . $_POST["zip2"],
            $_POST["address"],
            $_POST["card_num"],
            $_POST["exp_year"] . "年". $_POST["exp_month"] ."月",
            $_POST["card_code"],
            sha1($_POST["pass"]),
            $_POST["nickname"],
            $icon,
            $_SESSION["userID"]
                )
            );

            print("<script>alert('完了しました');
                    location.href='./prof-check.php';
                </script>");
            exit();            
        }
    }
}

    ?>


<!DOCTYPE html>
<html lang="ja">
<? ?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/prof.jpg">
    <title>プロフィール変更</title>
    <link href="../css/common.css" rel="stylesheet">
</head>

<body>
    
    <main>
        
        <header class="listing-header">
            <div class="back"><a href="./prof-check.php">＜</a></div>
            <span class="back_text">戻る</span>
        </header>
        
        <p>次のフォームに必須入力を入力してください</p>
        
        <!-- action属性が空の場合は自分のファイルを再度読み込む -->
        <form  action="" method="post"  enctype="multipart/form-data" >
            <dl class="form">
                
                <dt>
                    <dd>氏名</dd>
                </dt>
                
                <dt>
                    <input class="iname" type="text" name="iname" size="13" maxlength="50"
                        value="<?php echo Xss($_POST["iname"]); ?>" placeholder="姓"/>
                    <input type="text" name="fname" size="13" maxlength="50"
                        value="<?php echo Xss($_POST["fname"]); ?>" placeholder="名"/>
                    
                    <?php if ($error["iname"] || $error["fname"] == "blank"): ?>
                        <p class="error">* 氏名を入力してください</p>
                    <?php endif;?>
                </dt>
                    
                <dt>
                    <dd>メールアドレス</dd>
                </dt>

                <dd>
                    <input type="text" name="email" size="35" maxlength="255"
                        value="<?php echo Xss($_POST["email"]) ?>" placeholder="半角英数字を入力してください"/>
                            
                            <?php if ($error["email"] == "blank"): ?>
                                <p class="error">* メールアドレスを入力してください</p>
                            <?php endif;?>

                            <?php if ($error["email"] == "duplicate"): ?> 
                                <p class="error">* 指定されたメールアドレスはすでに登録されています</p>
                            <?php endif; ?>

                            <?php if ($error["email"] == "miss"): ?> 
                                <p class="error">＊正しい形式で入力してください</p>
                            <?php endif; ?>

                </dd>
                                
                                <dt>
                                    <dd>生年月日</dd>
                                </dt>

                                <dd>

                                <select name="year">
                                    <option value="">-</option>
                                    <option value="1900">1900</option>
                                    <option value="1901">1901</option>
                                    <option value="1902">1902</option>
                                    <option value="1903">1903</option>
                                    <option value="1904">1904</option>
                                    <option value="1905">1905</option>
                                    <option value="1906">1906</option>
                                    <option value="1907">1907</option>
                                    <option value="1908">1908</option>
                                    <option value="1909">1909</option>
                                    <option value="1910">1910</option>
                                    <option value="1911">1911</option>
                                    <option value="1912">1912</option>
                                    <option value="1913">1913</option>
                                    <option value="1914">1914</option>
                                    <option value="1915">1915</option>
                                    <option value="1916">1916</option>
                                    <option value="1917">1917</option>
                                    <option value="1918">1918</option>
                                    <option value="1919">1919</option>
                                    <option value="1920">1920</option>
                                    <option value="1921">1921</option>
                                    <option value="1922">1922</option>
                                    <option value="1923">1923</option>
                                    <option value="1924">1924</option>
                                    <option value="1925">1925</option>
                                    <option value="1926">1926</option>
                                    <option value="1927">1927</option>
                                    <option value="1928">1928</option>
                                    <option value="1929">1929</option>
                                    <option value="1930">1930</option>
                                    <option value="1931">1931</option>
                                    <option value="1932">1932</option>
                                    <option value="1933">1933</option>
                                    <option value="1934">1934</option>
                                    <option value="1935">1935</option>
                                    <option value="1936">1936</option>
                                    <option value="1937">1937</option>
                                    <option value="1938">1938</option>
                                    <option value="1939">1939</option>
                                    <option value="1940">1940</option>
                                    <option value="1941">1941</option>
                                    <option value="1942">1942</option>
                                    <option value="1943">1943</option>
                                    <option value="1944">1944</option>
                                    <option value="1945">1945</option>
                                    <option value="1946">1946</option>
                                    <option value="1947">1947</option>
                                    <option value="1948">1948</option>
                                    <option value="1949">1949</option>
                                    <option value="1950">1950</option>
                                    <option value="1951">1951</option>
                                    <option value="1952">1952</option>
                                    <option value="1953">1953</option>
                                    <option value="1954">1954</option>
                                    <option value="1955">1955</option>
                                    <option value="1956">1956</option>
                                    <option value="1957">1957</option>
                                    <option value="1958">1958</option>
                                    <option value="1959">1959</option>
                                    <option value="1960">1960</option>
                                    <option value="1961">1961</option>
                                    <option value="1962">1962</option>
                                    <option value="1963">1963</option>
                                    <option value="1964">1964</option>
                                    <option value="1965">1965</option>
                                    <option value="1966">1966</option>
                                    <option value="1967">1967</option>
                                    <option value="1968">1968</option>
                                    <option value="1969">1969</option>
                                    <option value="1970">1970</option>
                                    <option value="1971">1971</option>
                                    <option value="1972">1972</option>
                                    <option value="1973">1973</option>
                                    <option value="1974">1974</option>
                                    <option value="1975">1975</option>
                                    <option value="1976">1976</option>
                                    <option value="1977">1977</option>
                                    <option value="1978">1978</option>
                                    <option value="1979">1979</option>
                                    <option value="1980">1980</option>
                                    <option value="1981">1981</option>
                                    <option value="1982">1982</option>
                                    <option value="1983">1983</option>
                                    <option value="1984">1984</option>
                                    <option value="1985">1985</option>
                                    <option value="1986">1986</option>
                                    <option value="1987">1987</option>
                                    <option value="1988">1988</option>
                                    <option value="1989">1989</option>
                                    <option value="1990">1990</option>
                                    <option value="1991">1991</option>
                                    <option value="1992">1992</option>
                                    <option value="1993">1993</option>
                                    <option value="1994">1994</option>
                                    <option value="1995">1995</option>
                                    <option value="1996">1996</option>
                                    <option value="1997">1997</option>
                                    <option value="1998">1998</option>
                                    <option value="1999">1999</option>
                                    <option value="2000">2000</option>
                                    <option value="2001">2001</option>
                                    <option value="2002">2002</option>
                                    <option value="2003">2003</option>
                                    <option value="2004">2004</option>
                                    <option value="2005">2005</option>
                                    <option value="2006">2006</option>
                                    <option value="2007">2007</option>
                                    <option value="2008">2008</option>
                                    <option value="2009">2009</option>
                                    <option value="2010">2010</option>
                                    <option value="2011">2011</option>
                                    <option value="2012">2012</option>
                                    <option value="2013">2013</option>
                                    <option value="2014">2014</option>
                                    <option value="2015">2015</option>
                                    <option value="2016">2016</option>
                                    <option value="2017">2017</option>
                                    <option value="2018">2018</option>
                                    <option value="2019">2019</option>
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option>
                                </select>　年
                                
                                <select name="month">
                                    <option value="">-</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>　月
                                
                                <select name="day">
                                    <option value="">-</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select>　日

                                <?php if ($error["year"] || $error["month"] || $error["day"] == "blank"): ?>
                                    <p class="error">* 生年月日を入力してください</p>
                                <?php endif;?>
                            </dd>

                        <dt>
                            <dd>郵便番号</dd>
                        </dt>

                        <dd>
                            <input type="text" name="zip1" size="4" maxlength="3"
                                value="<?php echo Xss($_POST["zip1"]); ?>" placeholder="半角数字"/> -
                            <input type="text" name="zip2" size="5" maxlength="4" onKeyUp="AjaxZip3.zip2addr('zip1','zip2','address','address');"
                                value="<?php echo Xss($_POST["zip2"]); ?>" placeholder="半角数字"/>
                            
                            <?php if ($error["zip1"] || $error["zip2"] == "blank"): ?>
                                <p class="error">* 郵便番号を入力してください</p>
                            <?php endif;?>

                            <?php if ($error["zip"] == "miss"): ?>
                                <p class="error">* 半角数字を入力してください</p>
                            <?php endif;?>
                        
                        </dd>

                        <dt>
                            <dd>住所</dd>
                        </dt>
                
                        <dd>
                            <input type="text" name="address" size="40" 
                                value="<?php echo Xss($_POST["address"]); ?>" placeholder="住所を入力してください"/>

                            <?php if ($error["address"] == "blank"): ?>
                                <p class="error">* 住所を入力してください</p>
                            <?php endif;?>
                        </dd>
                
                        <dt>
                            <dd>クレジット</dd>
                        </dt>
                        
                        <dd>
                            <div>カード番号</div>
                                <input type="text" name="card_num" size="35" maxlength="255"
                                    value="<?php echo Xss($_POST["card_num"]); ?>" placeholder="半角数字を入力してください"/>
                                
                                <?php if ($error["card_num"] == "blank"): ?>
                                    <p class="error">* カード番号を入力してください</p>
                                <?php endif;?><br>

                                <?php if ($error["card_num"] == "miss"): ?>
                                    <p class="error">* 半角数字を入力してください</p>
                                <?php endif;?><br>
                            <div>有効期限</div>
                            <select name="exp_year">
                                <option value="">-</option>
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                            月 /
                            <select name="exp_month">
                                <option value="">-</option>
                                <option value="22">22</option>
                                <option value="22">23</option>
                                    <option value="22">24</option>
                                    <option value="22">25</option>
                                    <option value="22">26</option>
                                    <option value="22">27</option>
                                    <option value="22">28</option>
                                    <option value="22">29</option>
                                    <option value="22">30</option>
                                    <option value="22">31</option>
                                    <option value="22">32</option>
                                    <option value="22">33</option>
                                    <option value="22">34</option>
                                </select>
                                年
                                <?php if ($error["exp_year"] || $error["exp_month"]  == "blank"): ?>
                                    <p class="error">* 有効期限を入力してください</p>
                                <?php endif;?><br>
                                    
                                <span>セキュリテイコード</span><br>
                                <input type="text" name="card_code" size="3" maxlength="3"
                                    value="<?php echo Xss($_POST["card_code"]); ?>" placeholder="半角数字"/>
                                
                                <?php if ($error["card_code"] == "blank"): ?>
                                    <p class="error">* セキュリティコードを入力してください</p>
                                <?php endif;?>

                                <?php if ($error["card_code"] == "miss"): ?>
                                    <p class="error">* 半角数字を入力してください</p>
                                <?php endif;?>
                                            
                                    <dt>
                                        <dd>パスワード</dd>
                                    </dt>
                    
                                    <dd>
                                        <input type="password" name="pass" size="10" maxlength="20"
                                            value="<?php echo Xss($_POST["pass"]); ?>" placeholder="半角６桁以上"/>
                                            <?php if ($error["pass"] == "blank"): ?>
                                                <p class="error">* パスワードを入力してください</p>
                                            <?php endif; ?>
                                            <?php if ($error["pass"] == "length") :?>
                                                <p class="error">* パスワードは6文字以上で入力してください</p>
                                            <?php endif; ?>
                                    </dd>
                                                
                                    <dt>
                                        <dd>ユーザーネーム</dd>
                                    </dt>
                                    
                                    <dd>
                                        <input type="text" name="nickname" size="35" maxlength="255"
                                        value="<?php echo Xss($_POST["nickname"]); ?>" placeholder="ユーザーネームを入力してください"/>
                                        <?php if ($error["nickname"] == "blank"): ?>
                                            <p class="error">* ユーザーネームを入力してください</p>
                                        <?php endif;?>
                                    </dd>
                        
                                    <dt> 
                                        <dd>アイコン</dd>
                                    </dt>
                                    
                                    <dd>
                                        <input type="file" name="icon" accept="image/*"/>
                                    <?php if($error["icon"] == "type"):?>
                                        <p class="error">* 写真は「.png」または「.jpg」,「.gif」の画像を指定してください</p>
                                    <?php endif; ?>
                                    
                                    <?php if($error["icon"] == "blank"):?>
                                        <p class="error">* ファイルを選択してください</p>
                                    <?php endif; ?>
                                    

                                    <?php if(!empty($error)):?>
                                        <p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
                                    <?php endif;?> 
                                </dd>
                        </dl>
                        <div><input type="submit" value="変更を確定する" /></div>            
                    </dd>
                </dl>
            </form>
        </main>    
        <footer>
        <img src="../image/samecari_logo.png">

        <span>Copyright © 2022
            hirotomaeda.
            All rights reserved.
        </span>
    </footer>
    <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
    <script src="../JQuery.js"></script>
    <script src="./join.js"></script>
    <script src="../timeline/timeline.js"></script>
</body> 

</html>