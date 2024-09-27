<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,minimal-ui">
<link rel="stylesheet" href="__PUBLIC__/Manage/css/bootstrap.css">
<link rel="stylesheet" href="__PUBLIC__/Manage/fonts/web-icons/web-icons.css">
<link rel="stylesheet" href="__PUBLIC__/Manage/fonts/font-awesome/font-awesome.css">
<link href="__PUBLIC__/Manage/js/layer/css/layui.css" rel="stylesheet"/>
<link rel="stylesheet" href="__PUBLIC__/Manage/css/mobile.css">
<link rel="stylesheet" href="__PUBLIC__/Manage/css/myapp.css">
<link rel="stylesheet" href="__PUBLIC__/Manage/css/daterangepicker.css">
<script src="__PUBLIC__/Manage/js/jquery.js"></script>
<script src="__PUBLIC__/Manage/js/jquery.form.js"></script>
<script src="__PUBLIC__/Manage/js/bootstrap.js"></script>
<script src="__PUBLIC__/Manage/js/layer/layer.js"></script>
<script src="__PUBLIC__/Manage/js/cvphp.js"></script>
<script src="__PUBLIC__/Manage/js/layer/layui.js"></script>
<script src="__PUBLIC__/Manage/js/moment.min.js"></script>
<script src="__PUBLIC__/Manage/js/daterangepicker.js"></script>
<script src="__PUBLIC__/Manage/js/app.js"></script>
    <link rel="stylesheet" href="__PUBLIC__/Manage/css/table.css">
    <title>数据清理</title>
    <style type="text/css">
        .modal-clear-select-content {
            margin-top: 20px
        }

        .public-btnArea button {
            margin-right: 20px
        }

        select.select_date {
            display: inline-block;
        }

        .clear-select-date .layui-unselect, .clear-select-date span {
            display: inline-block;
        }

        .clear-select-date span {
            padding: 0 10px;
        }

        .input-select-date {
            display: inline-block;
            width: 80px;
            margin: 0 8px;
        }
    </style>
</head>
<body>
<div class="nestable">
    <div class="console-title console-title-border drds-detail-title clearfix">
        <h5>数据清理</h5>
    </div>
    <div class="public-btnArea">
        <button class="btn btn-danger clearfix" onclick="batchClear('pay_order')">清理还款支付订单</button>
        <button class="btn btn-danger clearfix" onclick="batchClear('delay_pay')">清理延期支付订单</button>
        <button class="btn btn-danger clearfix" onclick="batchClear('loan_order')">清理借款订单</button>
        <button class="btn btn-danger clearfix" onclick="batchClear('loan_bill')">清理账单</button>
        <button class="btn btn-danger clearfix" onclick="batchClear('pay_proof')">清理还款凭证</button>
        <button class="btn btn-danger clearfix" onclick="batchClear('user')">清理用户数据</button>
    </div>
</div>
</body>
<div class="modal-clear-select" id="modal-clear-select" style="display: none;">
    <div class="modal-clear-select-content">
        <form class="layui-form" action="">
            <input type="hidden" name="date-select-type" value="1"/>
            <input type="hidden" name="date-select-input" value="15"/>
            <div class="clear-select-date layui-form-item layui-inline layui-col-md12">
                <label class="layui-col-md4 layui-form-label ">选择</label>
                <div class="layui-col-md8">
                    <select class="select-date-type" lay-filter="selectDate">
                        <option value="1">日期</option>
                        <option value="2">自定义</option>
                    </select></div>
            </div>
            <div id="select-dropdown-date" class="clear-select-date layui-form-item layui-inline layui-col-md12">
                <label class="layui-col-md4 layui-form-label ">清理</label>
                <div class="layui-col-md8"><select name="select_date" lay-filter="selectDateValue">
                    <option value="7">7</option>
                    <option value="10">10</option>
                    <option value="15" selected>15</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                </select><span>天前数据</span></div>
            </div>
            <div id="custom-select-date" class="clear-select-date layui-form-item layui-inline layui-col-md12"
                 style="display: none">
                <label class="layui-col-md4 layui-form-label ">清理</label>
                <div class="layui-col-md8"><input type="text" class="layui-input input-select-date"/>天前数据</span>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    var form = layui.form;

    function batchClear(type = '') {
        if (window.matchMedia("(max-width: 720px)").matches) {
            var areas = ['20rem', '25rem'];
            var offset = '30vh'
        } else {
            var areas = ['30rem', '26rem'];
            var offset = '35vh'
        }
        layer.open({
            type: 1,
            title: "清理数据",
            area: areas,
            offset: offset,
            content: $('#modal-clear-select'),
            btn: ['关闭', '确定'],
            btn1: function (index, layero) {
                layer.close(index);
                return false;
            },
            btn2: function (index, layero) {
                var dateType = $('input[name=date-select-type]').val();
                var date = 15;
                if (parseInt(dateType) == 1) {
                    date = $('input[name=date-select-input]').val();
                    date = parseInt(date);
                } else {
                    date = $(".input-select-date").val();
                    date = parseInt(date);
                }
                clearData(type, date)
            }
        });
    }

    form.on('select(selectDateValue)', function (data) {
        console.log(data, data.value);
        var date = parseInt(data.value);
        $('input[name=date-select-input]').val(date);
    })
    form.on('select(selectDate)', function (data) {
        if (data.value == 1) {
            $('#select-dropdown-date').show();
            $('#custom-select-date').hide();
            $('input[name=date-select-type]').val(1);
        } else {
            $('#select-dropdown-date').hide();
            $('#custom-select-date').show();
            $('input[name=date-select-type]').val(2);
        }
    });

    function clearData(type, date) {
        if (!type || !date) {
            layer.msg('请选择要删除的内容');
            return false;
        }
        var _num1 = Math.ceil((Math.random() * 100) % 10);
        var _num2 = Math.ceil((Math.random() * 1000) % 100);
        var lay1 = layer.open({
            type: 1,
            content:
                '<div style="width: 380px;height: 200px;"><form class="layui-form" action="#" style="padding: 30px 10px">\n' +
                '<div class="layui-form-item"><span style="font-size: 16px;color: #f0221d;padding:5px 0 20px 10px;">你确定要清理' + date + '天前的数据吗,清理操作是不可恢复的？</span></div>\n' +
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
                var ly1 = layer.load(1);
                $.ajax({
                    type: 'POST',
                    data: {"type": type, "date": date},
                    url: '/Setting/clearData',
                    dataType: 'json',
                    success: function (reponse) {
                        if (reponse.status == 1) {
                            layer.msg(reponse.info, {time: 3000});
                            layer.close(ly1);
                            layer.close(lay1);
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
            },
            btn2: function (index, layero) {
                layer.close(index)
            }
        })
    }
</script>
</html>