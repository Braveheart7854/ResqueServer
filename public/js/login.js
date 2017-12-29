/**
 * Created by tonghai on 2017/11/23.
 */
function popup(popupName){
    var windowWidth = $(window).width(),
        windowHeight = $(window).height(),
        popupHeight = $(popupName).height(),
        popupWidth = $(popupName).width(),
        _posiTop = (windowHeight - popupHeight)/2,
        _posiLeft = (windowWidth - popupWidth)/2;
    if(windowHeight>popupHeight){
        $(popupName).css({"left": _posiLeft + "px","top":_posiTop + "px","bottom":""});
    }else{
        $(popupName).css({"left": _posiLeft + "px","top":"5px","bottom":"5px","overflow":"auto"});
    };
};
function showDiv(popupName){
    $(popupName).fadeIn();
    $(".fade").fadeIn();
    popup(popupName);
    $(window).resize(function(){
        popup(popupName);
    });
};
$(".alert-close").on("click",function(){
    $(".fade").fadeOut();
    $(this).parents(".alert").fadeOut();
});
$("#login").click(function() {
    showDiv($(".login-dialog"));
});