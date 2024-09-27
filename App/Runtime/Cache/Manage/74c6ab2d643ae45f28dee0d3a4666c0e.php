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
    <script src="__PUBLIC__/Manage/js/layer/layui.js"></script>
    <link rel="stylesheet" href="__PUBLIC__/Manage/js/layer/css/layui.css">
    <title>贷款已处理列表</title>
    <style>
        .modal-audit-status-content {
            padding-top: 20px;
        }

        .modal-audit-status-content .layui-form-item .layui-col-md4 {
            width: 30%;
            height: 80px;
        }

        .modal-audit-status-content .layui-form-item .layui-col-md8 {
            width: 70%;
            height: 80px;
            margin: 0;
            padding-right: 10px;
        }
    </style>
</head>
<body>
<div class="nestable">
    <div class="console-title console-title-border drds-detail-title clearfix">
        <h5>贷款已处理列表</h5>
    </div>
    <form method="get" id="seachForm2" action="<?php echo U('Loan/process');?>">
        <input type="hidden" name="m" value="Loan"/>
        <input type="hidden" name="a" value="process"/>
        <input type="hidden" name="current_status" value="1">
        <div class="public-selectArea" id="pxs">
            <div class="clearfix">
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">订单号：</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" name="oid" value="<?php echo ($_GET['oid']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">申请时间：</label>
                        <div class="col-xs-8">
                            <input type="text" class="time-input form-control" id="s-time"
                                   value="<?php echo ($_GET['stimeStart']); ?> ~ <?php echo ($_GET['stimeEnd']); ?>">
                            <i class="layui-icon layui-icon-close-fill clear-icon-btn"></i>
                            <input type="hidden" name="stimeStart" value="<?php echo ($_GET['stimeStart']); ?>"/>
                            <input type="hidden" name="stimeEnd" value="<?php echo ($_GET['stimeEnd']); ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btnArea">
                <a href='javascript:$("#seachForm2").submit();' class="btn btn-sereachBg">
                    <i class="glyphicon glyphicon-search public-ico"></i>
                    <span class="public-label">查询</span>
                </a>
                <button onclick="exportLoanOrder(event)" class="btn btn-primary">
                    <span class="public-label">导出订单</span>
                </button>
            </div>
        </div>
    </form>
    <div class="scroll-bar-table">
        <table class="table table-hover">
            <thead>
            <tr>
                <th><input type="checkbox" value="0" lay-skin="primary" id="checkall"
                           onclick="javascript:checkAll(this);"></th>
                <th>订单号</th>
                <th>用户名</th>
                <th>借款金额</th>
                <th>借款期限</th>
                <th>预期收益</th>
                <th>申请时间</th>
                <th>收款银行</th>
                <th>银行卡号</th>
                <th>开户名称</th>
                <th>情况说明</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="list-<?php echo ($vo["id"]); ?>">
                    <td><input type="checkbox" name="lid[]" class="check-lid" value="<?php echo ($vo["id"]); ?>"></td>
                    <td><?php echo ($vo["oid"]); ?></td>
                    <td><?php echo ($vo["user"]["telnum"]); ?></td>
                    <td><?php echo ($vo["money"]); echo (C("CURRENCY")); ?></td>
                    <td>
                        <?php echo ($vo["time"]); ?>
                        <?php if($vo['timetype'] == 1): ?>个月
                            <?php else: ?>
                            天<?php endif; ?>
                    </td>
                    <td><?php echo ($vo["interest_money"]); echo (C("CURRENCY")); ?></td>
                    <td><?php echo (date("Y/m/d H:i:s",$vo["start_time"])); ?></td>
                    <td><?php echo ($vo["bankname"]); ?></td>
                    <td><?php echo ($vo["banknum"]); ?></td>
                    <?php $statusText="处理中"; $reason="处理中,请等待"; if ($vo.pending==-1){ $statusText="处理中"; $reason=""; }elseif($vo.pending==-2){ $statusText="下单失败"; $reason=$vo.reqerror; }elseif($vo.pending==-3){ $statusText="回调失败"; $reason="上游回调失败"; } ?>
                    <td><?php echo ($vo["name"]); ?></td>
                    <td><?php echo ($reason); ?></td>

                    <td><?php echo ($statusText); ?></td>
                    <td class="text-left">
                        <?php if(($vo["pending"] == -2) OR ($vo["pending"] == -3)): ?><a href="javascript:reGenerateOrder('<?php echo ($vo["id"]); ?>');" class="layui-btn layui-btn layui-btn-sm">重新生成订单并提交</a><?php endif; ?>
                        <?php if($vo["pending"] == -3): ?><a href="javascript:setPendingStatus('<?php echo ($vo["id"]); ?>',2);"
                               class="layui-btn layui-btn-danger layui-btn-sm">驳回申请</a><?php endif; ?>
                        <a href="javascript:checkOrderLog('<?php echo ($vo["id"]); ?>');" class="layui-btn layui-btn-normal layui-btn-sm">查看日志</a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
    <div class="table-pagin-container">
        <!--        <div class="pull-left page-box">-->
        <!--            <button type="button" class="btn btn-primary btn-audit" onclick="batchAudit()">批量审核</button>-->
        <!--        </div>-->
        <div class="pull-right page-box">
            <?php echo ($page); ?>
        </div>
    </div>
</div>
</body>
<div id="loan-order" style="display:none">
    <table class="layui-table">
        <thead>
        <tr style="background:#1E9FFF;color:white">
            <th>错误类型</th>
            <th>错误原因</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal-audit-status" id="modal-audit-status" style="display: none;">
    <div class="modal-audit-status-content">
        <form class="layui-form" action="">
            <div class="layui-form-item layui-inline layui-col-md12">
                <label class="layui-col-md4 layui-form-label ">审核状态</label>
                <div class="layui-input-block layui-col-md8">
                    <select name="change_status">
                        <option value="0">待审核</option>
                        <option value="1">审核通过</option>
                        <option value="2">审核拒绝</option>
                        <option value="3">已放款</option>
                        <option value="4">已逾期</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    function setPendingStatus(id, status) {
        var title;
        if (status == 1) {
            title = '请先检验合同内容及签名并确认打款已成功再确认';
        } else if (status == 2) {
            title = '驳回申请需剪短描述驳回原因';
        } else {
            return;
        }
        layer.confirm(
            title,
            {
                btn: ['下一步', '取消']
            }, function () {
                layer.closeAll();
                var errorStr;
                if (status == 2) {
                    layer.prompt(
                        {
                            title: "请输入驳回原因",
                            formType: 0
                        },
                        function (str, index) {
                            errorStr = str;
                            layer.close(index);
                            cvphp.post(
                                "<?php echo U('Loan/setPendingStatus');?>",
                                {
                                    status: status,
                                    error: errorStr,
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
                } else {
                    cvphp.post(
                        "<?php echo U('Loan/setPendingStatus');?>",
                        {
                            status: status,
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
            }
        );
    }

    //重新生成订单并提交
    function reGenerateOrder(id) {
        cvphp.post(
            "<?php echo U('Loan/reGenerateOrder');?>",
            {id: id},
            function (data) {
                if (data.status != 1) {
                    layer.msg(data.info);
                } else {
                    layer.msg(data.info)
                }
            }
        );
    }

    //查看订单详细日志
    function checkOrderLog(id) {
        cvphp.post(
            "<?php echo U('Loan/ajaxView');?>",
            {
                id: id
            },
            function (data) {
                if (parseInt(data.status) == 1) {
                    layer.msg(data.info);
                } else {
                    var _html = "<tr><td>驳回理由</td><td>" + data.error + "</td></tr>"
                    _html += "<tr><td>提交失败</td><td>" + data.reqerror + "</td></tr>"
                    if (data.notify) {
                        _html += "<tr><td>回调失败</td><td>" + data.notify.trade_status + "</td></tr>"
                    }
                    console.log(_html);
                    $("#loan-order table tbody").html(_html)
                    layer.open({
                        type: 1,
                        content: $("#loan-order"),
                        area: ['500px', '300px'],
                        btn: ['关闭'],
                        btn1: function (index, layero) {
                            layer.close(index)
                            return false;
                        }
                    });

                }
            }
        );
    }


</script>
</html>