let flag = true;

// 开启弹窗
function openMsg(str) {
    if (flag){
        flag = false;
        let toast = "<div class='msg'><p class='msg-content'></p></div>"
        $('body').append(toast);
        $('.msg-content').text(str);
        $('.msg').show();
        setTimeout(function () {
            $('.msg').hide();
            $('.msg').remove();
            flag = true;
        }, 3000);
    }
}
