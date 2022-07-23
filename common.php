<?php

function Xss($value){
    return htmlspecialchars($value, ENT_QUOTES); 
}
?>

<!-- 本文内のURLにリンクを設定します -->
<?php
    function makeLink($value){
        return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)",'<a href="\1\2">\1\2</a>' , $value);
        //mb_ereg_replace("正規表現パターン"、"置換文字列"、"調べたい文字列")
        // 第一引数を第二引数に置換する
    }
?>