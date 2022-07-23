<?php

    ini_set("display_errors", 0);

    require("../dbconnect.php");
    require("../common.php");

    //直接ログインを防ぐ
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
    
        // 個別番号
    if (isset($_GET['id'])) {
        $productID = $_GET['id'];
    }

    
    if(isset($_SESSION["complete"])){
        
        print('
                    <div class="modal-pop">
                    <div class="bg js-modal-close"></div>
                    <div class="modal-pop-main">
                    <img src="../image/login-complete.jpg" />
                    </div>
                    </div>
                '
                );

        unset($_SESSION["complete"]);
    }

    //フォームからデータが送られてきたかチェックする
    if (!empty($_POST["deli_souce"])) {

        //エラー項目の確認
        if ($_POST["deli_souce"] == "" ) {
            $error["deli_souce"] = "blank";
        }

        if ($_POST["deli_charge"] == "") {
            $error["deli_charge"] = "blank";
        }

        if ($_POST["product_inf"] == "") {
            $error["product_inf"] = "blank";
        }

        if ($_POST["pname"] == "") {
            $error["pname"] = "blank";
        }

        if ($_POST["price"] == "") {
            $error["price"] = "blank";
        }
        if (!is_numeric($_POST["price"])) {
            $error["price"] = "miss";
        }

        if ($_POST["category"] == "") {
            $error["category"] = "blank";
        }

        
        if ($_FILES["image"]["error"] == 4) {
            $error["image"] = "blank";
        }

        $fileName = $_FILES["listing"]["name"];
        if (!empty($fileName)) {
            $ext = substr($fileName, -4);
            if ($ext != ".jpg" && $ext != '.gif' && $ext != 'jpeg'&& $ext != '.png') {
                $error["listing"] = "type";
            }
        }
        

        //エラーがなければ
        if (empty($error)) {            
            //出品するボタンを押したとき
            if(isset($_POST["listing"])){
                
                $products = $db->prepare("SELECT productID FROM product WHERE productID=?");
                $products->execute(array($_POST["productID"]));
                $product = $products->fetch();


            //画像をアップロードする
            $image = date("YmdHis") . $_FILES["image"]["name"];
            move_uploaded_file($_FILES["image"]["tmp_name"], "./upload_file/" . $image);
            unlink("./upload_file/" . $product["productImage"]);

            $statement = $db->prepare("UPDATE product SET pname=?,price=?,product_inf=?,category=?,deli_charge=?,deli_souce=?,deli_day=NOW(),
                    cnt_click=0,cnt_cart=0,productImage=? WHERE productID=?");
            $statement->execute(array(

                    $_POST["pname"],
                    $_POST["price"],
                    $_POST["product_inf"],
                    $_POST["category"],
                    $_POST["deli_charge"],
                    $_POST["deli_souce"],
                    $image,
                    $productID
                ));

                
            
            header("Location: ../timeline/timeline.php");
            exit();

            }  
        }
    }

    ?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/prof.jpg">
    <title>商品変更</title>
    <link href="../css/common.css" rel="stylesheet">
</head>

<body>
    <header class="listing-header">
        <div class="back"><a href="./listing-detail.php?id=<?php print($productID) ?>">＜</a></div>
        <span class="back_text">戻る</span>
    </header>
    <main>

        <br>
        <p>次のフォームに投稿内容を入力してください</p>
        <form action="" method="post" enctype="multipart/form-data">
            <dl class="form">

                <dd>出品画像（最大10枚）</dd>

                <dt>

                    <input type="file" name="image" accept="image/*" />
                    <?php if($error["image"] == "type"):?>
                        <p class="error">* 写真は「.png」または「.jpg」,「.gif」の画像を指定してください</p>
                    <?php endif; ?>

                    <?php if($error["image"] == "blank"):?>
                        <p class="error">* ファイルを選択してください</p>
                    <?php endif; ?>

                    
                    <?php if(!empty($error)):?>
                        <p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
                    <?php endif;?>

                </dt>

                <dd>商品の詳細</dd>

                <dt>
                    <dd>カテゴリー</dd>
                </dt>
                <dt>
                    <input type="text" name="category" size="26" maxlength="50"
                        value="<?php echo Xss($_POST["category"]); ?>" placeholder="日本料理、中華、麺類など" />
                    <?php if ($error["category"] == "blank"): ?>
                        <p class="error">* カテゴリーを入力してください</p>
                    <?php endif;?>
                    
                </dt>

                <dd>商品名と説明</dd>

                <dt>
                    <dd>商品名</dd>
                </dt>

                <dt>
                    <input type="text" name="pname" size="26" maxlength="100"
                        value="<?php echo Xss($_POST["pname"]); ?>" placeholder="商品名" />
                    <?php if ($error["pname"] == "blank"): ?>
                        <p class="error">* 商品名を入力してください</p>
                    <?php endif;?>
                </dt>

                <dt>
                    <dd>商品の説明</dd>
                </dt>

                <dt>
                    <textarea name="product_inf" id="" cols="30" rows="10"
                        placeholder="商品の説明"><?php echo Xss($_POST["product_inf"]); ?></textarea>
                    <?php if ($error["product_inf"] == "blank"): ?>
                    <p class="error">* 商品の説明を入力してください</p>
                    <?php endif;?>
                </dt>

                <dd>配送について</dd>

                <dt>
                    <dd>配送料の負担</dd>
                </dt>

                <dt>
                    <select name="deli_charge">
                        <option value="">-</option>
                        <option value="出品者">出品者</option>
                        <option value="購入者">購入者</option>
                    </select>

                    <?php if ($error["deli_charge"] == " "): ?>
                        <p class="error">* 配送料の負担を選択してください</p>
                    <?php endif;?>
                </dt>

                <dt>
                    <dd>配送元の地域</dd>
                </dt>
                <dt>
                    <select name="deli_souce">
                        <option value="">-</option>
                        <option value="北海道">北海道</option>
                        <option value="青森県">青森県</option>
                        <option value="岩手県">岩手県</option>
                        <option value="宮城県">宮城県</option>
                        <option value="秋田県">秋田県</option>
                        <option value="山形県">山形県</option>
                        <option value="福島県">福島県</option>
                        <option value="茨城県">茨城県</option>
                        <option value="栃木県">栃木県</option>
                        <option value="群馬県">群馬県</option>
                        <option value="埼玉県">埼玉県</option>
                        <option value="千葉県">千葉県</option>
                        <option value="東京都">東京都</option>
                        <option value="神奈川県">神奈川県</option>
                        <option value="新潟県">新潟県</option>
                        <option value="富山県">富山県</option>
                        <option value="石川県">石川県</option>
                        <option value="福井県">福井県</option>
                        <option value="山梨県">山梨県</option>
                        <option value="長野県">長野県</option>
                        <option value="岐阜県">岐阜県</option>
                        <option value="静岡県">静岡県</option>
                        <option value="愛知県">愛知県</option>
                        <option value="三重県">三重県</option>
                        <option value="滋賀県">滋賀県</option>
                        <option value="京都府">京都府</option>
                        <option value="大阪府">大阪府</option>
                        <option value="兵庫県">兵庫県</option>
                        <option value="奈良県">奈良県</option>
                        <option value="和歌山県">和歌山県</option>
                        <option value="鳥取県">鳥取県</option>
                        <option value="島根県">島根県</option>
                        <option value="岡山県">岡山県</option>
                        <option value="広島県">広島県</option>
                        <option value="山口県">山口県</option>
                        <option value="徳島県">徳島県</option>
                        <option value="香川県">香川県</option>
                        <option value="愛媛県">愛媛県</option>
                        <option value="高知県">高知県</option>
                        <option value="福岡県">福岡県</option>
                        <option value="佐賀県">佐賀県</option>
                        <option value="長崎県">長崎県</option>
                        <option value="熊本県">熊本県</option>
                        <option value="大分県">大分県</option>
                        <option value="宮崎県">宮崎県</option>
                        <option value="鹿児島県">鹿児島県</option>
                        <option value="沖縄県">沖縄県</option>
                    </select>

                    <?php if ($error["deli_souce"] == "blank"): ?>
                        <p class="error">* 都道府県を入力してください</p>
                    <?php endif;?>
                </dt>


                <dt>
                    <dd>販売価格</dd>
                </dt>

                <dt>
                    <input type="text" name="price" size="26" maxlength="50"
                        value="<?php echo Xss($_POST["price"]); ?>" placeholder="半角数字" />
                    <?php if ($error["price"] == "blank"): ?>
                        <p class="error">* 値段を入力してください</p>
                    <?php endif;?>
                    <?php if ($error["price"] == "miss"): ?>
                        <p class="error">* 半角数字を入力してください</p>
                    <?php endif; ?>
                </dt>

                <div id="listing-btn">
                    <input type="submit" name="listing" value="変更する" /><br>
                </div>
        </form>

    </main>
    <footer>
        <img src="../image/samecari_logo.png">

        <span>Copyright © 2022
            hirotomaeda.
            All rights reserved.
        </span>
    </footer>

    <!-- jQuery CDNとjsファイル読み込み -->
    <script src="../JQuery.js"></script>
    <script src="../timeline/timeline.js"></script>
</body>

</html>