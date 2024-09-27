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
    <title>已拒绝订单</title>
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
        <h5>驳回借款</h5>
    </div>
    <form method="get" id="seachForm">
        <input type="hidden" name="m" value="Loan"/>
        <input type="hidden" name="a" value="refuse"/>
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
                            <input type="hidden"  name="stimeEnd" value="<?php echo ($_GET['stimeEnd']); ?>"/>
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
                <th>申请时间</th>
                <th>收款银行</th>
                <th>银行卡号</th>
                <th>开户名称</th>
                <th>驳回原因</th>
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
                    <td><?php echo (date("Y/m/d H:i:s",$vo["start_time"])); ?></td>
                    <td><?php echo ($vo["bankname"]); ?></td>
                    <td><?php echo ($vo["banknum"]); ?></td>
                    <td><?php echo ($vo["name"]); ?></td>
                    <td><?php echo ($vo["error"]); ?></td>
                    <td class="text-left">
                        <a href="javascript:delLoanOrder('<?php echo ($vo["id"]); ?>');">删除订单</a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <label><input type="checkbox" name="check_all"
                      title="全选搜索后的所有数据">&nbsp;全选搜索后的所有数据</label>
    </div>
    <div class="table-pagin-container">
        <div class="pull-left page-box">
            <input type="hidden" name="current_status" value="2">
            <button type="button" class="btn btn-primary btn-audit" onclick="batchAuditAction()">批量修改</button>
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
                        <option value="0">待审核</option>
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
    function delLoanOrder(id) {
        layer.confirm(
            '订单删除后不可恢复,请确认？',
            {
                btn: ['确认删除', '取消']
            }, function () {
                cvphp.post(
                    "<?php echo U('Loan/delLoanOrder');?>",
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