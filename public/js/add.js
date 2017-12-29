/**
 * Created by tonghai on 2017/11/23.
 */
function addAccount() {
    var account = $('#account').val();
    $.ajax({
        url:'/admin/ambass/add-ambass',
        type:'get',
        data:{account:account},
        dataType:'json',
        success:function (d) {
            if (d.code == 10000){
                alert(d.message);
                location.reload();
            }else {
                alert(d.message);
            }
        }
    });
}