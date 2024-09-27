/**
 * 获取七牛云Token
 * @returns {string}
 */
function dealUpdateData() {
    let token = '';
    let policy = {};
    let bucketName = 'ynimage001'; // 印尼的
    let AK = 'lb5ntuPzvl6BoSqL5yato8Pqwg5WXoPHGVzTktIt';
    let SK = '7i3g_SDTFCvJ-TYs3FBfCJJtki8Rp2hYfNVIK908';
    let deadline = Math.round(new Date().getTime() / 1000) + 3600;
    policy.scope = bucketName;
    policy.deadline = deadline;
    token = getUpToken(AK, SK, policy);
    return token;
}

let move = function(e){
    e.preventDefault && e.preventDefault();
    e.returnValue=false;
    e.stopPropagation && e.stopPropagation();
    return false;
}

let keyFunc=function(e){
    if(37 <= e.keyCode && e.keyCode <= 40){
        return move(e);
    }
}

/**
 * 关闭提示模态框
 */
function closeIndexModal() {
    $('#notice-dialog').css('display', 'none');
    $('#my-dialog-cover').css('display', 'none');
    startViewScroll();
}

/**
 * 打开提示模态框
 */
function openIndexModal() {
    $('#notice-dialog').css('display', 'block');
    $('#my-dialog-cover').css('display', 'block');
    stopViewScroll();
}

/**
 * 关闭页面滚动
 */
function stopViewScroll() {
    document.documentElement.style.overflow='hidden';
    document.body.style.overflow='hidden';
    document.body.onkeydown=keyFunc;
}

/**
 * 开启页面滚动
 */
function startViewScroll() {
    document.documentElement.style.overflow='';
    document.body.style.overflow='';
    document.body.onkeydown=null;
}

/**
 * 检测传入参数是否为空或未定义
 * @param data
 * @returns {boolean}
 */
function isEmpty(data) {
    return data === undefined || data === null || data === '' || data === {} || data === [] || data.length === 0;
}


/**
 * A卡卡号长度
 * @param that
 */
function checkACardNumberLength(that){
    let value = $(that).val();
    if (value.length > 12){
        $(that).val(value.slice(0, 12));
    }
}


function clickUpload() {
    document.getElementById('file-txz').click();
}

/**
 * 上传图片
 * @param c
 * @param d
 */
function upload(c, d) {
    "use strict";
    let $c = document.querySelector(c),
        $d = document.querySelector(d),
        file = $c.files[0],
        reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function (e) {
        $d.setAttribute("src", e.target.result);
    };

    let formData = new FormData();
    formData.append("file", file);
    formData.append("token", dealUpdateData());

    $.ajax({
        type: 'post',
        url: 'https://upload-as0.qiniup.com', // 东南亚的
        contentType: false,
        data: formData,
        processData: false,// 不转化为信息
        success: function (rs) {
            // 通过ajax调用后台接收图片地址的方法,rs.key就是文件名
            if (!isEmpty(rs.key)) {
                $("input[name = image]").val(rs.key);
            }
        },
        error: function (e) {
            openMsg("Network Error");
            console.log(e.status);
            console.log(e.responseText);
        }
    })

}

/**
 * 上传UPI码
 */
function uploadUpi(type) {
    let utr = $("input[name = utr]").val();
    let image = $("input[name = image]").val();
    if(type==2)
    {
        if (isEmpty(utr)){
            openMsg('No parameter: UTR Code, please enter!');
            return;
        }

        if (utr.length < 12){
            openMsg('The length of UTR Code is incorrect, please double check');
            return;
        }
        if (isNaN(utr)){
            openMsg('UTR Code can only be 12 digits, please double check');
            return;
        }
    }
    else
    {
        if (isEmpty(image)){
            openMsg('Please upload later');
            return;
        }
    }

    let orderno = $("input[name = orderno]").val();
    let baseUrl = $("input[name = domain]").val();
    if(type==0)
    {
        utr = image;
    }
    if (isEmpty(orderno)) {
        openMsg().error('No parameter: orderno');
    } else if (isEmpty(baseUrl)) {
        openMsg.error('No parameter: domain');
    } else {
        $.ajax({
            type: 'post',
            url: baseUrl + '/adminappapi/Home/payment_voucher',
            async: true,
            dataType: 'json',
            data: {
                "orderno": orderno,
                "type": type,
                "photo": utr
            },
            success: function (res) {
                if (200 === res.code) {
                    openMsg(res.msg);
                } else {
                    openMsg(res.msg);
                }
            },
            error: function (e) {
                openMsg("Network Error");
                console.log(e.status);
                console.log(e.responseText);
            }
        })
    }
}
