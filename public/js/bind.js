/**
 * Created by tonghai on 2017/11/23.
 */
function play(num) {
    $.ajax({
        url:'/bind/bind-area',
        type:'get',
        data:{area:num},
        dataType:'json',
        success:function (d) {
            if (d.code == 10000){
                location.href = 'http://'+location.host+'/bind/view-bind-account';
            }else {
                alert(d.message);
            }
        }
    });
}

function subAccount() {
    var account = $('#account').val();
    $.ajax({
        url:'/bind/bind',
        type:'get',
        data:{account:account},
        dataType:'json',
        success:function (d) {
            // if (d.code == 10000){
            //     location.href = 'http://'+location.host+'/bind/view-bind-account';
            // }else {
                alert(d.message);
            // }
        }
    });
}