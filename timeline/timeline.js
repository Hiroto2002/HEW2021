// 完了画面クリックで消える
$('.js-modal-close').on('click',function(){
    $('.modal-pop').fadeOut();
})

//戻るボタン
$(".back").on("mouseover",function(){
    $(".back_text").show(100);
});

$(".back").on("mouseout",function(){
    $(".back_text").hide(100);
});


function fadeAnime(){

    // ふわっ
    $('.fadeUpTrigger').each(function(){ //fadeUpTriggerというクラス名が
    var elemPos = $(this).offset().top+50;//要素より、50px上の
    var scroll = $(window).scrollTop();
    var windowHeight = $(window).height();
    if (scroll >= elemPos - windowHeight){
    $(this).addClass('fadeUp');// 画面内に入ったらfadeUpというクラス名を追記
    }else{
    $(this).removeClass('fadeUp');// 画面外に出たらfadeUpというクラス名を外す
    }
    });
}

// 画面をスクロールをしたら動かしたい場合の記述
    $(window).scroll(function (){
    fadeAnime();/* アニメーション用の関数を呼ぶ*/
    });// ここまで画面をスクロールをしたら動かしたい場合の記述

// 画面が読み込まれたらすぐに動かしたい場合の記述
    $(window).on('load', function(){
    fadeAnime();/* アニメーション用の関数を呼ぶ*/
    });// ここまで画面が読み込まれたらすぐに動かしたい場合の記述



/**
 * 隠れているものを表示、非表示
 * ボタンを何度でも押せる
 */

// 検索ボタン
$('.search').on('click',function(){
    $('.invisible-search').toggle(200),
    $('.search').hide(),
    $('.search-cancel').show();
    $('.search-cancel').css("background-color","transparent");
});

$('.search-cancel').on('click',function(){
    $('.invisible-search').toggle(200);
    $('.search-cancel').hide(),
    $('.search').css("display","block");

});

// カートのゴミ箱
$('.dust').on('click',function(){
    $(this).hide(100),
    $(".cancel").css("display", "block");
    $(".invisible-dust").show();
    $(".invisible-dust").css("background-color","transparent");
});

$('.invisible-dust').on('click',function(){
    $(this).hide()
    $(".cancel").css("display", "none");
    $(".dust").show();
});

function getFileName(){
    var title = window.location.href.split('/').pop();
    if(title == "timeline.php"){
        $(".home").css("background-color","rgb(255, 187, 61)")
    }
    if(title == "dm_select.php"){
        $(".dm").css("background-color","rgb(255, 187, 61)")
    }
    if(title == "rank.php"){
        $(".rank").css("background-color","rgb(255, 187, 61)")
    }
    if(title == "prof.php" ){
        $(".prof").css("background-color","rgb(255, 187, 61)")
    }
    if(title == "cart.php"){
        $(".cart").css("background-color","rgb(255, 132, 61);")
    }
}
getFileName();



//任意のタブにURLからリンクするための設定
function GethashID (hashIDName){
    if(hashIDName){
      //タブ設定
      $('.tab li').find('a').each(function() { //タブ内のaタグ全てを取得
        var idName = $(this).attr('href'); //タブ内のaタグのリンク名（例）#lunchの値を取得 
        if(idName == hashIDName){ //リンク元の指定されたURLのハッシュタグ（例）http://example.com/#lunch←この#の値とタブ内のリンク名（例）#lunchが同じかをチェック
          var parentElm = $(this).parent(); //タブ内のaタグの親要素（li）を取得
          $('.tab li').removeClass("active"); //タブ内のliについているactiveクラスを取り除き
          $(parentElm).addClass("active"); //リンク元の指定されたURLのハッシュタグとタブ内のリンク名が同じであれば、liにactiveクラスを追加
          //表示させるエリア設定
          $(".area").removeClass("is-active"); //もともとついているis-activeクラスを取り除き
          $(hashIDName).addClass("is-active"); //表示させたいエリアのタブリンク名をクリックしたら、表示エリアにis-activeクラスを追加 
        }
      });
    }
  }
  
  //タブをクリックしたら
  $('.tab a').on('click', function() {
    var idName = $(this).attr('href'); //タブ内のリンク名を取得  
    GethashID (idName);//設定したタブの読み込みと
    return false;//aタグを無効にする
  });
  
  
  // 上記の動きをページが読み込まれたらすぐに動かす
  $(window).on('load', function () {
      $('.tab li:first-of-type').addClass("active"); //最初のliにactiveクラスを追加
      $('.area:first-of-type').addClass("is-active"); //最初の.areaにis-activeクラスを追加
    var hashName = location.hash; //リンク元の指定されたURLのハッシュタグを取得
    GethashID (hashName);//設定したタブの読み込み
  });