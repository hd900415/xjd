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
    <link rel="stylesheet" href="__PUBLIC__/Manage/js/layer/css/layui.css">
    <script src="__PUBLIC__/Manage/js/layer/layui.js"></script>
    <?php if($pendingType == 2): ?><title>复借待审核</title>
        <?php else: ?>
        <title>待审核列表</title><?php endif; ?>
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
        <?php if($pendingType == 2): ?><h5>复借待审核</h5>
            <?php else: ?>
            <h5>借款审核</h5><?php endif; ?>

    </div>
    <form method="get" id="seachForm">
        <input type="hidden" name="m" value="Loan"/>
        <input type="hidden" name="a" value="pending"/>
        <input type="hidden" name="__type__" value="<?php echo ($pendingType); ?>"/>
        <input type="hidden" name="__pendingType__" value="<?php echo ($pendingType); ?>"/>

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
                            <input type="hidden" name="stimeStart"
                                   value="<?php echo ($_GET['stimeStart']); ?>"/>
                            <input type="hidden" name="stimeEnd" value="<?php echo ($_GET['stimeEnd']); ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btnArea">

                <a href='javascript:$("#seachForm").submit();' class="btn btn-sereachBg">
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
                <th>预期用户到账金额</th>
                <th>预期用户应还金额</th>
                <th>申请时间</th>
                <th>收款银行</th>
                <th>银行卡号</th>
                <th>开户名称</th>
                <!--							<th>合同</th>-->
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
                    <td><?php echo ($vo["get_money"]); echo (C("CURRENCY")); ?></td>
                    <td><?php echo ($vo["repay_money"]); echo (C("CURRENCY")); ?></td>
                    <td><?php echo (date("Y/m/d H:i:s",$vo["start_time"])); ?></td>
                    <td><?php echo ($vo["bankname"]); ?></td>
                    <td><?php echo ($vo["banknum"]); ?></td>
                    <td><?php echo ($vo["name"]); ?></td>
                    <!--							<td>-->
                    <!--								<a href="<?php echo U('Loan/viewContract',array('id'=>$vo['id']));?>" title="点击查看合同" target="_blank">查看合同</a>-->
                    <!--							</td>-->
                    <td class="text-left">
                        <a href="javascript:setPendingStatus('<?php echo ($vo["id"]); ?>',1);">确认打款并生成账单</a>
                        <a href="javascript:setPendingStatus('<?php echo ($vo["id"]); ?>',2);">驳回申请</a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <label><input type="checkbox" name="check_all"
                      title="全选搜索后的所有数据">&nbsp;全选搜索后的所有数据</label>
    </div>
    <div class="table-pagin-container">
        <div class="pull-left page-box">
            <input type="hidden" name="current_status" value="0">
            <button type="button" class="btn btn-primary btn-audit" onclick="batchAuditAction()">批量修改</button>
            <!--            <button type="button" class="btn btn-primary btn-audit" onclick="batchAudit('all')">全部修改</button>-->
            <button type="button" class="btn btn-danger btn-delete" onclick="batchDeleteAction()">批量删除</button>
        </div>
        <div class="pull-right page-box">
            <?php echo ($page); ?>
        </div>
    </div>
</div>
<div class="modal-audit-status" id="modal-audit-status" style="display: none;">
    <div class="modal-audit-status-content">
        <form class="layui-form" action="">
            <div class="layui-form-item layui-inline layui-col-md12">
                <label class="layui-col-md4 layui-form-label ">审核状态</label>
                <div class="layui-input-block layui-col-md8">
                    <select name="change_status">
                        <option value="2">已拒绝</option>
                        <option value="6">已逾期</option>
                        <option value="3">还款中</option>
                        <option value="4">续期中</option>
                        <option value="5">已还清</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
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
                btn: ['下一步', '取消'],
                offset: ['45wh']
            },

            function () {
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
    function batchAuditAction() {
        var check_all = $("input[name=check_all]").prop('checked')
        var checkType = '';
        if (check_all) checkType = 'all';
        batchAudit('', checkType)
    }

    function batchDeleteAction() {
        var check_all = $("input[name=check_all]").prop('checked')
        var checkType = 1;
        if (check_all == true) checkType = 2;

        batchDelete('', checkType)
    }
</script>
</html>