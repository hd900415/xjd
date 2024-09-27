function getOffsetHeight(eleHeight = 0) {
    var clientHeight = $(window.top).height();
    if (!eleHeight) {
        if ($(".layui-layer").length > 0) {
            eleHeight = $(".layui-layer").height()
        }
    }

    if (window.matchMedia("(max-width: 720px)").matches) {
        var offset = (clientHeight - eleHeight) / 2
    } else {
        var offset = (clientHeight - eleHeight) / 2
    }
    if (eleHeight > clientHeight) {
        offset = "5vh";
    }
    console.log('offset:', offset, eleHeight, clientHeight)
    return offset
}

function getClientWidth() {
    var clientHeight = $(window.top).width();
}

layer.config({
    anim: 1, //默认动画风格
    offset: getOffsetHeight()
});

function getAgoDay(n) {
    let date = new Date();
    let seperator = "-"
    let newDate = new Date(date.getTime() - n * 24 * 60 * 60 * 1000);
    let year = newDate.getFullYear();
    let month = newDate.getMonth() + 1;
    if (month < 10) month = '0' + month;
    let day = newDate.getDate();
    if (day < 10) day = '0' + day;
    return year.toString() + seperator + month.toString() + seperator + day.toString()
}


$(document).ready(function () {
    // console.log('initializing');
    $(".time-input").each(function () {
        var _id = $(this).attr('id');
        $('#' + _id).daterangepicker({
            timePicker24Hour: true,
            timePicker: true,
            clearBtn: true,
            autoUpdateInput: false,
            "showDropdowns": true,
            "showISOWeekNumbers": true,
            "ranges": {
                '今天': [moment().format('YYYY-MM-DD') + '00:00:00', moment().format('YYYY-MM-DD') + '23:59:59'],
                '昨天': [moment().subtract(1, 'days').format('YYYY-MM-DD') + '00:00:00', moment().subtract(1, 'days').format('YYYY-MM-DD') + '23:59:59'],
                '近一周': [moment().subtract(6, 'days').format('YYYY-MM-DD') + '00:00:00', moment().format('YYYY-MM-DD') + '23:59:59'],
                '近一个月': [moment().subtract(29, 'days').format('YYYY-MM-DD') + '00:00:00', moment().format('YYYY-MM-DD') + '23:59:59'],
                '本月': [moment().startOf('month'), moment().endOf('month')],
                '上个月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },

            "locale": {
                "format": 'YYYY-MM-DD HH:mm:ss',
                "separator": " ~ ",
                "applyLabel": "确定",
                "cancelLabel": "取消",
                "fromLabel": "从",
                "toLabel": "到",
                "customRangeLabel": "自定义",
                "weekLabel": "周",
                resetLabel: "重置",
                cancelLabel: "清空",
                daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
                monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                "firstDay": 1
            },
            clickApply: function (e) {
                this.autoUpdateInput = true;

            },
            isInvalidDate: function (date) {
                return false;
            },
            "alwaysShowCalendars": true,
            "startDate": moment().subtract(6, 'days').format('YYYY-MM-DD') + '00:00:00',
            "endDate": moment().format('YYYY-MM-DD') + '23:59:59',
            "linkedCalendars": false,

        }, function (start, end, label) {
            this.autoUpdateInput = true;
            console.log('确定时间:', start, end, label)
            var sbilings = $(this.element).nextAll()
            if (sbilings.length == 3) {
                $(sbilings[1]).val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
                $(sbilings[2]).val(moment(end).format('YYYY-MM-DD HH:mm:ss'));
            }
            if (moment(start).format("YYYY-MM-DD") == '1970-08-01' || moment(end).format("YYYY-MM-DD") == '1970-08-01') {
                $('#' + _id).val("");
            }
        }).on('cancel.daterangepicker', function (event, picker) {
            console.log(picker)
            $(picker.element).val("");
        });
    });
    $(".clear-icon-btn").click(function () {
        $(this).prev().val('')
        $(this).nextAll().val("")
    })
})

function getQueryString() {
    var result = location.search.match(new RegExp("[\?\&][^\?\&]+=[^\?\&]+", "g"));
    var params = {};
    if (result && result.length > 0) {
        for (var i = 0; i < result.length; i++) {
            var str = result[i].substring(1);
            if (str) {
                var arr = str.split("=");
                if (arr.length > 1 && arr[1]) {
                    params[arr[0]] = arr[1];
                }

            }
        }
    }
    return params;

}

function checkAll(ele) {
    if ($(ele).is(":checked")) {
        $("input.check-lid").each(function () {
            $(this).prop('checked', true);
        })
    } else {
        $("input.check-lid").each(function () {
            $(this).prop('checked', false);
        })
    }
}

function batchAudit(auditType = '', checkType = '') {
    if (window.matchMedia("(max-width: 720px)").matches) {
        var areas = ['20rem', '25rem'];
        var offset = '30vh'
    } else {
        var areas = ['30rem', '26rem'];
        var offset = '35vh'
    }
    var check_loanIds = getLoanIds();
    if (check_loanIds.length == 0) {
        layer.msg('请选择数据后重试', {offset: offset});
        return false;
    }
    layer.open({
        type: 1,
        title: "批量修改状态",
        area: areas,
        offset: offset,
        content: $('#modal-audit-status'),
        btn: ['关闭', '确定'],
        btn1: function (index, layero) {
            layer.close(index);
            return false;
        },
        btn2: function (index, layero) {
            var ly1 = layer.load(1, {offset: offset});
            var params = getQueryString();
            var change_status = $("select[name='change_status']").val();
            var audit_state = $("input[name='current_status']").val();
            delete params['m'];
            delete params['a'];
            params['status'] = change_status;
            params['ids'] = check_loanIds;
            // if (check_loanIds.length > 0) {
            //     if ($("#checkall").is(':checked')) {
            //          = 'all';
            //     }
            // }
            params['audit_type'] = checkType
            params['audit_state'] = audit_state;
            if (check_loanIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    data: params,
                    url: '/Loan/ChangeLoanOrderStatus',
                    dataType: 'json',
                    success: function (reponse) {
                        if (reponse.status == 1) {
                            layer.msg(reponse.info, {time: 3000});
                            layer.close(ly1);
                            location.reload();
                        } else {
                            layer.close(ly1);
                            layer.msg(reponse.info);
                        }
                    },
                    error: function (e) {
                        layer.close(ly1);
                        layer.msg("发起请求出错" + e)
                    }
                });
            } else {
                layer.msg('请先选择内容')
            }
            console.log(check_loanIds);
        }
    });
}

function getLoanIds() {
    var check_loanIds = [];
    $("input.check-lid").each(function () {
        if ($(this).is(":checked")) {
            var _id = parseInt($(this).val());
            check_loanIds.push(_id);
        }
    });
    return check_loanIds;
}

function batchDelete(type = '', checkType = 1) {
    var check_loanIds = getLoanIds();
    var params = {};
    params = getQueryString();
    params['type'] = checkType;
    delete params.m;
    delete params.a;
    console.log('checkType11:', checkType)
    params['ids'] = check_loanIds;
    var audit_state = $("input[name='current_status']").val();
    params['audit_state'] = audit_state;
    console.log('批量删除1:', params);
    if (params['type'] == 1 && params['ids'].length == 0) {
        layer.msg('请选择要删除的内容');
        return false;
    }
    var _num1 = Math.ceil((Math.random() * 100) % 10);
    var _num2 = Math.ceil((Math.random() * 1000) % 100);
    layer.open({
        type: 1,
        content:
            '<div style="width: 360px;height: 180px;"><form class="layui-form" action="#" style="padding: 30px 10px">\n' +
            '<div class="layui-form-item"><span style="font-size: 16px;color: #f0221d;padding:5px 0 20px 10px;">你确定要删除吗,删除操作是不可逆的？</span></div>\n' +
            '  <div class="layui-form-item" style="margin-top: 30px;line-height: 35px;">\n' +
            '    <label class="col-sm-4">计算结果:</label>\n' +
            '    <div class="col-sm-8" style="display: flex">\n' +
            '     <div style="flex:2;font-size: 18px;">' + _num1 + ' + ' + _num2 + ' =</div><div style="flex: 2"><input type="text" id="verify-delete-number" class="layui-input"></div>\n' +
            '    </div>\n' +
            '  </div></form></div>',
        btn: ['确定', '取消'],
        offset: getOffsetHeight(400),
        btn1: function (index, layero) {
            var num3 = $("#verify-delete-number").val();
            if (!num3 && num3 != _num2 + _num1) {
                layer.msg("请输入正确的计算结果,再确认");
                return false;
            }
            console.log('批量删除2:', params);
            var ly1 = layer.load(1);
            if (check_loanIds.length > 0 || params['type'] == 2) {
                $.ajax({
                    type: 'POST',
                    data: params,
                    url: '/Loan/DeleteLoan',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status == 1) {
                            layer.close(ly1);
                            layer.msg(response.info);
                        } else {
                            layer.close(ly1);
                            layer.msg(response.info);
                        }
                        location.reload();
                    },
                    error: function (e) {
                        layer.close(ly1);
                        layer.msg("发起请求出错" + e)
                    }
                });

            } else {
                layer.msg('请先选择内容')
            }

        },
        btn2: function (index, layero) {
            layer.close(index)
        }
    })
}

function exportLoanOrder(event) {
    event.preventDefault();
    params = getQueryString();
    delete params.m;
    delete params.a;
    var _query = Object.keys(params).map(function (k) {
        return [encodeURIComponent(k), encodeURIComponent(params[k])].join("=");
    }).join("&");
    var audit_state = $("input[name='current_status']").val();
    var pendingType = $("input[name='__pendingType__']").val();
    _query += '&audit_state=' + audit_state;
    if (parseInt(pendingType) == 2) {
        _query += '&__type__=2'
    } else if (parseInt(pendingType) == 1) {
        _query += '&__type__=1'
    }
    if (window.matchMedia("(max-width: 720px)").matches) {
        var offset = '30vh'
    } else {
        var offset = '35vh'
    }
    var dl = layer.load(1, {offset: offset});
    window.location.href = "/Loan/export?" + _query;
    setTimeout(function () {
        layer.close(dl);
    }, 3000);
}

function exportOrderService(event, _type, base_url) {
    event.preventDefault();
    console.log("exportPayOrder", _type);
    params = getQueryString();
    delete params.m;
    delete params.a;
    params["__type__"] = _type;
    var _query = Object.keys(params).map(function (k) {
        return [encodeURIComponent(k), encodeURIComponent(params[k])].join("=");
    }).join("&");
    if (window.matchMedia("(max-width: 720px)").matches) {
        var offset = '30vh'
    } else {
        var offset = '35vh'
    }
    var dl = layer.load(1, {offset: offset});
    console.log(_query);
    window.location.href = base_url + _query;
    setTimeout(function () {
        layer.close(dl);
    }, 3000);
}


function exportPayOrder(event, _type) {
    exportOrderService(event, _type, "/Pay/export?")
}

function exportDepayPayOrder(event, _type) {
    exportOrderService(event, _type, "/DelayPay/export?")
}

function exportPayProofOrder(event, _type) {
    event.preventDefault();
    console.log("exportPayOrder", _type);
    params = getQueryString();
    delete params.m;
    delete params.a;
    params["__type__"] = _type;
    var _query = Object.keys(params).map(function (k) {
        return [encodeURIComponent(k), encodeURIComponent(params[k])].join("=");
    }).join("&");
    if (window.matchMedia("(max-width: 720px)").matches) {
        var offset = '30vh'
    } else {
        var offset = '35vh'
    }
    var dl = layer.load(1, {offset: offset});
    console.log(_query);
    window.location.href = "/Pay/proofExport?" + _query;
    setTimeout(function () {
        layer.close(dl);
    }, 3000);
}

var payUrls = {};

function customRepay(orderId, money, repaymentMoney, uid) {
    console.log("params:", '' + orderId, money, repaymentMoney)
    var _html = "<div class='modal-custom-repay'><form class='layui-form' action=''>\n" +
        "  <div class='layui-form-item'>\n" +
        "    <label class='layui-form-label'>订单号</label>\n" +
        "    <div class='layui-input-block'>\n" +
        "      <input type='text' name='orderId' value='" + orderId + "' class='layui-input' readonly>\n" +
        "    </div>\n" +
        "  </div><div class='layui-form-item'>\n" +
        "    <label class='layui-form-label'>类型</label>\n" +
        "    <div class='layui-input-block'>\n" +
        "      <select name='pay_type'>\n" +
        "        <option value='1'>部分付款</option>\n" +
        "        <option value='2'>延期/全部还款</option>\n" +
        "      </select>\n" +
        "    </div></div>\n" +
        "  <div class='layui-form-item input-amount'>\n" +
        "    <label class='layui-form-label'>金额</label>\n" +
        "    <div class='layui-input-block'>\n" +
        "        <div class='input-group-addons'>\n" +
        "            <span class='prend-input'>-</span>\n" +
        "            <input type='text' class='input-group-text' name='amount' />\n" +
        "            <span class='append-input'>+</span>\n" +
        "        </div>\n" +
        "    </div></div>\n" +
        "  <div class='layui-form-item layui-form-text'>\n" +
        "    <label class='layui-form-label'>支付地址</label>\n" +
        "    <div class='layui-input-block'>\n" +
        "      <textarea id='repay_url' name='repay_url' class='layui-textarea'></textarea>\n" +
        "    </div>\n" +
        "  </div>\n" +
        "</form></div>";
    layer.open({
        type: 1,
        title: "自定义还款",
        content: _html,
        btn: ["确定", "复制网址", "取消"],
        offset: getOffsetHeight(400),
        btn1: function (index, layero) {
            var amount = $(".input-amount .input-group-text").val();
            var pay_type = $("select[name='pay_type']").val();
            if (pay_type == 1 && amount <= 0) {
                layer.msg("请输入正确的金额");
                return false;
            }
            // if (pay_type == 1 && amount > repaymentMoney) {
            //     layer.msg("付款金额不能大于应还金额");
            //     return false;
            // }
            var _saveKey = "order_" + orderId + "_" + amount;
            console.log(payUrls, payUrls[_saveKey]);
            if (payUrls[_saveKey]) {
                $("textarea[name='repay_url']").text(payUrls[_saveKey]);
                return false;
            }
            var dl = layer.load(1, {offset: getOffsetHeight()});
            $.ajax({
                type: "POST",
                url: "/Loan/generatePayUrl",
                data: {pay_type: pay_type, amount: amount, orderId: orderId, uid: uid},
                success: function (response) {
                    if (response.status == 1) {
                        if (response.info) {
                            payUrls[_saveKey] = response.info;
                            $("textarea[name='repay_url']").text(response.info);
                        } else {
                            layer.msg('生成失败，请联系工作人员', {offset: getOffsetHeight()});
                        }
                    } else {
                        layer.msg(response.info, {offset: getOffsetHeight()});
                    }
                    layer.close(dl);
                },
                error: function () {
                    layer.close(dl);
                }
            })
        },
        btn2: function (index, layero) {
            var range = document.createRange();
            range.selectNode(document.getElementById("repay_url"));
            var selection = window.getSelection();
            if (selection.rangeCount > 0) selection.removeAllRanges();
            selection.addRange(range);
            document.execCommand("copy");
            var lm = layer.msg("复制成功", {offset: getOffsetHeight()});
            selection.removeRange(range);
            return false;
        },
        btn3: function (index, layero) {
            layer.close(index)
        }
    })
    $("select[name='pay_type']").change(function () {
        var pay_type = $(this).val();
        if (pay_type == 1) {
            $(".input-amount").show();
        } else {
            $(".input-amount .input-group-text").val("");
            $(".input-amount").hide();
        }
    })
    $(".input-group-addons .prend-input").click(function () {
        var _val = $(".input-group-addons .input-group-text").val();
        if (!_val) _val = 0;
        $(".input-group-addons .input-group-text").val(parseInt(_val) - 50);
    })
    $(".input-group-addons .append-input").click(function () {
        var _val = $(".input-group-addons .input-group-text").val();
        if (!_val) _val = 0;
        $(".input-group-addons .input-group-text").val(parseInt(_val) + 50);
    })
}

function checkPhotoPreview(_url, imgHeight = 0) {
    layer.photos({
        photos: {
            "title": "", //相册标题
            "id": 123, //相册id
            "start": 0, //初始显示的图片序号，默认0
            "data": [   //相册包含的图片，数组格式
                {
                    "alt": "图片名",
                    "pid": 666, //图片id
                    "src": _url, //原图地址
                    "thumb": _url //缩略图地址
                }
            ]
        },//选择器,
        anim: 5,
        offset: getOffsetHeight(imgHeight),
        success:function (){
            $(document).on("mousewheel",".layui-layer-photos",function(ev){
                var oImg = this;
                var ev = event || window.event;//返回WheelEvent
                //ev.preventDefault();
                var delta = ev.detail ? ev.detail > 0 : ev.wheelDelta < 0;
                var ratioL = (ev.clientX - oImg.offsetLeft) / oImg.offsetWidth,
                    ratioT = (ev.clientY - oImg.offsetTop) / oImg.offsetHeight,
                    ratioDelta = !delta ? 1 + 0.1 : 1 - 0.1,
                    w = parseInt(oImg.offsetWidth * ratioDelta),
                    h = parseInt(oImg.offsetHeight * ratioDelta),
                    l = Math.round(ev.clientX - (w * ratioL)),
                    t = Math.round(ev.clientY - (h * ratioT));
                $(".layui-layer-photos").css({
                    width: w, height: h
                    ,left: l, top: t
                });
                $("#layui-layer-photos").css({width: w, height: h});
                $("#layui-layer-photos>img").css({width: w, height: h});
            });
        }
    });
}
