// 完了画面クリックで消える
$('.js-modal-close').on('click',function(){
    $('.modal-pop').fadeOut();
})


$('.container').ripples({
    resolution: 400
});

$(".index_back").on("mouseover",function(){
    $(".index_back_text").show(100);
});

$(".index_back").on("mouseout",function(){
    $(".index_back_text").hide(100);
});

$('.img-wrap img:nth-child(n+2)').hide();
setInterval(function() {
    $(".img-wrap img:first-child").fadeOut(3000);
    $(".img-wrap img:nth-child(2)").fadeIn(3000);
    $(".img-wrap img:first-child").appendTo(".img-wrap");
}, 3000);



