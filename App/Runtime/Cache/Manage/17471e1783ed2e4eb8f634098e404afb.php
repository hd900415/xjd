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
    <script src="__PUBLIC__/Manage/js/layer/layui.js"></script>
    <link rel="stylesheet" href="__PUBLIC__/Manage/js/layer/css/layui.css">
    <link rel="stylesheet" href="__PUBLIC__/Manage/css/table.css">
    <title>用户列表</title>
    <style>
        .redText {
            color: red;
            font-size: 20px;
        }

        body .daterangepicker {
            z-index: 29991014 !important;
        }
        .cr{
            font-size:14px;
            color:red;
        }
    </style>
</head>
<body>
<div class="nestable">
    <div class="console-title console-title-border drds-detail-title clearfix">
        <h5>用户列表</h5>
    </div>
    <form method="get" id="seachForm">
        <input type="hidden" name="m" value="User"/>
        <input type="hidden" name="a" value="index"/>
        <div class="public-selectArea" id="pxs">
            <div class="clearfix">
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">用户ID：</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" name="id" value="<?php echo ($_GET['id']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">用户名/电话：</label>
                        <div class="col-xs-8">
                            <input type="text" name="username" class="form-control" value="<?php echo ($_GET['username']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">注册时间：</label>
                        <div class="col-xs-8">
                            <input type="text" class="time-input form-control" id="s-time"
                                   format="yyyy-MM-dd HH:mm:ss ~ yyyy-MM-dd HH:mm:ss"
                                   value="<?php echo ($_GET['timeStart']); ?> ~ <?php echo ($_GET['timeEnd']); ?>">
                            <i class="layui-icon layui-icon-close-fill clear-icon-btn"></i>
                            <input type="hidden" name="timeStart"
                                   value="<?php echo ($_GET['timeStart']); ?>"/>
                            <input type="hidden" name="timeEnd" value="<?php echo ($_GET['timeEnd']); ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btnArea">
                <a href='javascript:$("#seachForm").submit();' class="btn btn-sereachBg">
                    <i class="glyphicon glyphicon-search public-ico"></i>
                    <span class="public-label">查询</span>
                </a>
                <a href="<?php echo U('User/create');?>" class="btn btn-sereachBg">添加用户</a>
                <button class="btn btn-primary" id="import-user-btn"
                        onclick="javascript:openImportUserLayer();return false;">导入用户
                </button>
                <button class="btn btn-info" id="import-order-btn"
                        onclick="javascript:openImportOrderLayer();return false;">导入订单
                </button>
            </div>
        </div>
    </form>
    <div class="scroll-bar-table">
        <div class="info" style="padding:20px;">
            <span>登录成功的人数：</span><span class="cr"><?php echo ($login_count); ?></span>
            <span>点过还款按钮的人数：</span><span class="cr"><?php echo ($reply_count); ?></span>
            <span>拉单过单的人数：</span><span class="cr"><?php echo ($get_order); ?></span>
            <span>支付成功数：</span><span class="cr"><?php echo ($pay_count); ?></span>
            <span>登录2次或以上人数：</span><span class="cr"><?php echo ($login2time); ?></span>
            <span>点还款按钮2次或以上人数：</span><span class="cr"><?php echo ($reply2time); ?></span>
            <span>拉单2次或以上人数：</span><span class="cr"><?php echo ($get2order); ?></span>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th><input type="checkbox" value="0" lay-skin="primary" id="checkall"
                           onclick="javascript:checkAll(this);"></th>
                <th>用户ID</th>
                <th>用户名/手机号</th>
                <th>级别</th>
                <th>额度</th>
                <th>状态</th>
                <th>注册时间</th>
                <th>借款统计</th>
                <th>前端APP统计</th>
                <th>账单统计</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php $status=array('禁用','启用'); ?>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="list-<?php echo ($vo["id"]); ?>">
                    <td><input type="checkbox" name="uid[]" class="check-lid" value="<?php echo ($vo["id"]); ?>"></td>
                    <td align="center"><?php echo ($vo["id"]); ?></td>
                    <td><?php echo ($vo["telnum"]); ?></td>
                    <td><?php echo ($vo["viptitle"]); ?></td>
                    <td><span style="color: red;"><?php echo ($vo["quota"]); ?></span> <?php echo (C("CURRENCY")); ?></td>
                    <td class="td-status">
                        <a href="javascript:resetStatus('<?php echo ($vo["id"]); ?>','<?php echo ($vo["status"]); ?>');"><?php echo ($status[$vo['status']]); ?></a>
                    </td>
                    <td><?php echo (date("Y-m-d H:i:s",$vo["reg_time"])); ?></td>
                    <td>
                        成功借款<span class="redText"><?php echo ($vo["succLoan"]); ?></span>次，驳回<span
                            class="redText"><?php echo ($vo["errLoan"]); ?></span>次，逾期<span class="redText"><?php echo ($vo["overdueNum"]); ?></span>次，已有<span
                            class="redText"><?php echo ($vo["unpaid"]); ?></span>笔订单还款完成
                    </td>
                    <td>
                        <span>首登陆时间<?php echo ($vo["login_time"]); ?></span><span>登录次数:<?php echo ($vo["login_count"]); ?></span><span>点还款按钮次数:<?php echo ($vo["reply_count"]); ?></span>
                        <span>拉单数:<?php echo ($vo["get_order"]); ?></span><span>支付成功数<?php echo ($vo["pay_count"]); ?></span>
                    </td>
                    <td>
                        累计借款<span class="redText"><?php echo ($vo["loanMoney"]); ?></span><?php echo (C("CURRENCY")); ?>，未还金额<span
                            class="redText"><?php echo ($vo["notrepayMoney"]); ?></span><?php echo (C("CURRENCY")); ?>，逾期金额<span
                            class="redText"><?php echo ($vo["overdueMoney"]); ?></span><?php echo (C("CURRENCY")); ?>
                    </td>
                    <td class="text-left">
                        <a href="<?php echo U('Info/view',array('uid'=>$vo['id']));?>" target="_blank">查看资料</a>
                        <a href="javascript:resetQuota('<?php echo ($vo["id"]); ?>','<?php echo ($vo["telnum"]); ?>');">调整额度</a>
                        <a href="javascript:resetBank('<?php echo ($vo["id"]); ?>','<?php echo ($vo["bank"]); ?>');">修改银行卡号</a>
                        <a href="javascript:resetTel('<?php echo ($vo["id"]); ?>','<?php echo ($vo["telnum"]); ?>');">修改手机号</a>
                        <a href="javascript:resetPass('<?php echo ($vo["id"]); ?>','<?php echo ($vo["telnum"]); ?>');">重置密码</a>
                        <a href="javascript:delUser('<?php echo ($vo["id"]); ?>','<?php echo ($vo["telnum"]); ?>');">删除会员</a>
                        <a href="javascript:addUserOrder('<?php echo ($vo["id"]); ?>');">生成订单</a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <div>
            <label><input type="checkbox" name="check_all"
                          title="全选搜索后的所有数据">&nbsp;全选搜索后的所有数据</label>
        </div>
        <div class="table-pagin-container">
            <div class="pull-left page-box">
                <!--            <button type="button" class="btn btn-primary btn-audit" onclick="batchAudit()">批量审核</button>-->
                <button type="button" class="btn btn-danger btn-delete" onclick="batchDelete()">
                    批量删除搜索后的所有数据
                </button>

            </div>
            <div class="pull-right page-box">
                <?php echo ($page); ?>
            </div>
        </div>
    </div>
</body>
<div id="upload-user-box" style="display: none;padding:50px 0 0 50px;">
    <div class="layui-form-item">
        <Button class="layui-btn" id="upload-excel-btn"><i class="layui-icon">&#xe67c;</i>上传excel文件</Button>
        <span class="layui-blue hide" id="import-user-excel-success">文件上传成功,请点击导入按钮导入</span>
        <input type="hidden" id="import-user-excel-path"/>
    </div>
    <div class="layui-form-item" style="padding-right: 50px;padding-top: 20px;">
        <label class="layui-form-label">还款日期</label>
        <div class="layui-input-block">
            <input type="text" name="last_repaytime" id="last_repaytime" placeholder="还款日期" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item" style="padding-right: 50px;padding-top: 10px;">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <input type="text" name="remark" id="remark" placeholder="备注" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item" style="padding-right: 50px;padding-top: 10px;">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <input type="radio" name="data_type" value="1" title="跳过重复数据" checked>&nbsp;跳过重复数据<br/>
            <input type="radio" name="data_type" value="2" title="覆盖重复数据(删除原账号及其数据)" >&nbsp;覆盖重复数据(删除原账号及其数据)
        </div>
    </div>
</div>
<div id="upload-order-box" style="display: none;padding:50px 0 0 50px;">
    <Button class="layui-btn" id="upload-order-excel-btn"><i class="layui-icon">&#xe67c;</i>上传excel文件
    </Button>
    <span class="layui-blue hide" id="import-order-excel-success">文件上传成功,请点击导入按钮导入</span>
    <input type="hidden" id="import-order-excel-path"/>
</div>
<script>

    $('#last_repaytime').daterangepicker({
        singleDatePicker: true,
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
        if (moment(start).format("YYYY-MM-DD") == '1970-08-01' || moment(end).format("YYYY-MM-DD") == '1970-08-01') {
            $('#last_repaytime').val("");
        }
    });

    //账户启用禁用
    function resetStatus(id, status) {
        if (status == 1) status = 0;
        else status = 1;
        cvphp.post(
            "<?php echo U('User/resetStatus');?>",
            {
                id: id,
                status: status
            },
            function (data) {
                if (data.status != 1) {
                    layer.msg(data.info);
                } else {
                    var html;
                    if (status == 1) {
                        layer.msg("账户已启用");
                        html = '<a href="javascript:resetStatus(' + "'" + id + "','1'" + ');">启用</a>';
                    } else {
                        layer.msg("账户已禁用");
                        html = '<a href="javascript:resetStatus(' + "'" + id + "','0'" + ');">禁用</a>';
                    }
                    $("#list-" + id + " .td-status").html(html);
                }
            }
        );
    }

    //调整额度
    function resetQuota(id, name) {
        layer.prompt(
            {
                title: '输入用户  [' + name + '] 的新额度',
                formType: 0,
                offset: getOffsetHeight() - 200
            },
            function (quota, index) {
                cvphp.post(
                    "<?php echo U('User/resetQuota');?>",
                    {
                        id: id,
                        quota: quota
                    },
                    function (data) {
                        if (data.status != 1) {
                            layer.msg(data.info);
                        } else {
                            layer.close(index);
                            layer.msg("用户资料已保存");
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        }
                    }
                );
            }
        );
    }

    //修改账户手机号
    function resetTel(id, name) {
        layer.prompt(
            {
                title: '输入用户  [' + name + '] 的新手机号',
                formType: 0,
                offset: getOffsetHeight() - 200
            },
            function (tel, index) {
                cvphp.post(
                    "<?php echo U('User/resetTel');?>",
                    {
                        id: id,
                        tel: tel
                    },
                    function (data) {
                        if (data.status != 1) {
                            layer.msg(data.info,{offset: '50vh'});
                        } else {
                            layer.close(index);
                            layer.msg("用户资料已保存", {offset: '50vh'});
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        }
                    }
                );
            }
        );
    }

    //修改账户银行卡号
    function resetBank(id, name) {
        layer.prompt(
            {
                title: '输入用户新银行卡号',
                formType: 0,
                offset: getOffsetHeight() - 200
            },
            function (bank, index) {
                cvphp.post(
                    "<?php echo U('User/resetBank');?>",
                    {
                        id: id,
                        bank: bank
                    },
                    function (data) {
                        if (data.status != 1) {
                            layer.msg(data.info);
                        } else {
                            layer.close(index);
                            layer.msg("用户资料已保存");
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        }
                    }
                );
            }
        );
    }

    //重置密码方法
    function resetPass(id, name) {
        layer.confirm(
            '确认要重置用户[ ' + name + ' ]登录密码吗？',
            {
                btn: ['确认', '取消'],
                offset: getOffsetHeight() - 200
            }, function () {
                cvphp.post(
                    "<?php echo U('User/resetPass');?>",
                    {
                        id: id
                    },
                    function (data) {
                        if (data.status != 1) {
                            layer.msg(data.info);
                        } else {
                            layer.alert("用户 [" + name + "] 密码已修改为  [" + data.info + "]");
                        }
                    }
                );
            }
        );
    }

    //删除会员方法
    function delUser(id, name) {
        layer.confirm(
            '用户[ ' + name + ' ]删除后不可恢复且用户附带账单订单资料等同步删除不可恢复,确认要删除吗？',
            {
                btn: ['确认删除', '取消'],
                offset: getOffsetHeight()
            }, function () {
                cvphp.post(
                    "<?php echo U('User/del');?>",
                    {
                        id: id
                    },
                    function (data) {
                        if (data.status != 1) {
                            layer.msg(data.info);
                        } else {
                            $("#list-" + id).remove();
                            layer.msg("操作成功");
                        }
                    }
                );
            }
        );
    }

    //打开导入会员modal
    function openImportUserLayer() {
        $('#import-user-excel-success').addClass('hide');
        $('#import-user-excel-path').val('');

        layer.open({
            type: 1,
            title: '导入用户列表',
            content: $("#upload-user-box"),
            area: ['500px', '480px'],
            offset: getOffsetHeight() - 200,
            btnAlign: 'r',
            btn: ['导入', '关闭'],
            btn1: function (index, layero) {

                var excel = $("#import-user-excel-path").val();
                if (!excel) {
                    layer.msg("请先点击上传excel文件按钮上传文件")
                    return false;
                }
                loading = layer.open({
                    type: 3
                });
                cvphp.post(
                    "<?php echo U('User/importUserFromExcel');?>",
                    {
                        path: excel,
                        last_repaytime: $("#last_repaytime").val(),
                        remark: $("#remark").val(),
                        data_type:$("input[name=data_type]:checked").val()
                    },
                    function (data) {
                        layer.close(loading);
                        if (data.status == 1) {
                            layer.msg(data.info);
                        } else {
                            layer.msg("操作成功");
                        }
                        layer.close(index); //如果设定了yes回调，需进行手工关闭
                    }
                );

            },
            btn2: function (index, layero) {
                layer.close(index)
            },

        })
    }

    function openImportOrderLayer() {

        $('#import-order-excel-success').addClass('hide');
        $('#import-user-excel-path').val('');
        layer.open({
            type: 1,
            title: '导入订单列表',
            content: $("#upload-order-box"),
            area: ['500px', '300px'],
            offset: getOffsetHeight() - 200,
            btnAlign: 'r',
            btn: ['导入', '关闭'],
            btn1: function (index, layero) {
                var excel = $("#import-order-excel-path").val()
                if (!excel) {
                    layer.msg("请先点击上传excel文件按钮上传文件")
                    return false;
                }
                loading = layer.open({
                    type: 3
                });
                cvphp.post(
                    "<?php echo U('Loan/importOrder');?>",
                    {
                        path: excel,

                    },
                    function (data) {
                        layer.close(loading)
                        if (data.status == 1) {
                            layer.msg(data.info);
                        } else {
                            layer.msg("操作成功");
                        }
                        layer.close(index); //如果设定了yes回调，需进行手工关闭
                    }
                );

            },
            btn2: function (index, layero) {
                layer.close(index)
            },

        })
    }

    function addUserOrder(_uid) {
        cvphp.post(
            "<?php echo U('User/addOrder');?>",
            {
                uid: _uid
            },
            function (data) {
                if (data.status == 1) {
                    layer.msg(data.info);
                } else {
                    layer.msg("操作成功");
                }
                layer.close(index); //如果设定了yes回调，需进行手工关闭
            }
        );
    }

    layui.use('upload', function () {
        var upload = layui.upload;

        //执行实例
        loading = null;
        var uploadInst = upload.render({
            elem: '#upload-excel-btn', //绑定<?php echo (C("CURRENCY")); ?>素
            url: "<?php echo U('Setting/uploadImg');?>", //上传接口
            accept: "file",
            exts: 'xls|xlsx',
            before: function () {
                loading = layer.open({
                    type: 3,
                    offset: getOffsetHeight() - 200
                });
            },
            data: {"fileName": "file"},
            done: function (res) {
                //上传完毕回调
                layer.close(loading)
                console.log(res);
                if (res.status == 1) {
                    $('#import-user-excel-success').removeClass('hide');
                    $('#import-user-excel-path').val(res.info);
                } else {
                    layer.msg("上传失败");
                }
            }
            , error: function () {
                //请求异常回调
            }
        });
        loading2 = null;
        var uploadInst = upload.render({
            elem: '#upload-order-excel-btn', //绑定<?php echo (C("CURRENCY")); ?>素
            url: "<?php echo U('Setting/uploadImg');?>", //上传接口
            accept: "file",
            exts: 'xls|xlsx',
            before: function () {
                loading2 = layer.open({
                    type: 3,
                    offset: getOffsetHeight() - 200
                });
            },
            data: {"fileName": "file"},
            done: function (res) {
                //上传完毕回调
                layer.close(loading2)
                console.log(res);
                if (res.status == 1) {
                    $('#import-order-excel-success').removeClass('hide');
                    $('#import-order-excel-path').val(res.info);
                } else {
                    layer.msg("上传失败");
                }
            }
            , error: function () {
                //请求异常回调
            }
        });
    });

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

    function batchDelete() {
        layer.confirm('你确定要删除吗,删除操作是不可逆的？', {offset: getOffsetHeight() - 200},
            function () {
                var params = {};
                var check_all = $("input[name=check_all]").prop('checked')
                console.log('check_all:', check_all)
                if (!check_all) { //不为true选择当前页
                    var check_loanIds = [];

                    $("input.check-lid").each(function () {
                        if ($(this).is(":checked")) {
                            var _id = parseInt($(this).val());
                            check_loanIds.push(_id);
                            params = {ids: check_loanIds, type: 1}
                        }
                    });
                } else {
                    // console.log();
                    var params = getQueryString();
                    check_loanIds = '0000';
                    delete params.m;
                    delete params.a;
                    params['type'] = 2;
                    params['ids'] = check_loanIds;
                }
                console.log('params: ', params);
                if (check_loanIds.length > 0) {
                    console.log(params);
                    cvphp.post("<?php echo U('User/batchDelete');?>", params, function (reponse) {
                        if (reponse.status == 1) {
                            layer.msg(reponse.info);
                        } else {
                            layer.msg(reponse.info);
                        }
                        location.reload();
                    })
                } else {
                    layer.msg('请先选择内容')
                }
            }
        )
    }
</script>
</html>