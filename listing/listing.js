// console.log("fgflgreihrou");
$('.delete_check').hide();
$('.sale-delete').on("click",function(){
    $('.delete_check').toggle();
});

$('.buy_check').on("click",function(){
    $('#buy_btn').toggle();
    $('.buy_check').toggle();
});

$('.sale-cart').on('click',function(){
    $(this).toggleClass("red");
});


