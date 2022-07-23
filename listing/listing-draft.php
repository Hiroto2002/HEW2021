<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/reset.css" rel="stylesheet">
    <link href="../css/common.css" rel="stylesheet">
    <link rel="icon" href="../image/prof.jpg">
    <title>下書き</title>
</head>


<body>

    <header class="listing-header">
        <div class="back"><a href="./listing.php"><</a></div>
    </header>
        
        <main id="dm-select"> 
            <?php
                for($i=0;$i<4;$i++){
                    print("<a href='./dm.php'><div class='dm-account'>
                                <img class='dm-icon' src='../image/cart.png' alt='商品'>
                                <p class='yname'>商品名</p></a>
                                </div>");
                }
                
            ?>
        </main>
</body>
</html>

