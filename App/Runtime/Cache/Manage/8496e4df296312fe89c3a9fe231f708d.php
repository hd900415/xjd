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
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>还款中订单</title>
    <style>
        html {
            font-size: 16px;
        }

        input.repay-money-input {
            width: 80px;
            display: inline-block;
        }

        input.repay-money-input-readonly:visited, input.repay-money-input-readonly {
            background: none !important;
            border: none;
            outline: none;
        }
    </style>
</head>
<body>
<div class="nestable">
    <div class="console-title console-title-border drds-detail-title clearfix">
        <h5>借款列表</h5>
    </div>
    <form method="get" id="seachForm">
        <input type="hidden" name="m" value="Loan"/>
        <input type="hidden" name="a" value="all"/>
        <div class="public-selectArea" id="pxs">
            <div class="clearfix">
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">订单号：</label>
                        <div class="col-xs-8">
                            <input type="text" name="oid" class="form-control" value="<?php echo ($_GET['oid']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">用户名/手机号：</label>
                        <div class="col-xs-8">
                            <input type="text" name="telnum" class="form-control" value="<?php echo ($_GET['telnum']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">状态：</label>
                        <div class="col-xs-8">
                            <select name="audit_state" class="form-control">
                                <option value="-100">---请选择---</option>
                                <option value="-100" selected="selected"
                                <?php if(isset($_GET['audit_state']) == false): ?>selected<?php endif; ?>
                                >全部</option>
                                <option value="0"
                                <?php if(isset($_GET['audit_state'])&&$_GET['audit_state']== 0): ?>selected="selected"<?php endif; ?>
                                >待审核</option>
                                <option value="1"
                                <?php if($_GET['audit_state']== 1): ?>selected="selected"<?php endif; ?>
                                >代付处理中</option>
                                <option value="2"
                                <?php if($_GET['audit_state']== 2): ?>selected="selected"<?php endif; ?>
                                >已拒绝</option>
                                <option value="3"
                                <?php if($_GET['audit_state']== 3): ?>selected="selected"<?php endif; ?>
                                >还款中</option>
                                <option value="6"
                                <?php if($_GET['audit_state']== 6): ?>selected="selected"<?php endif; ?>
                                >已逾期</option>
                                <option value="4"
                                <?php if($_GET['audit_state']== 4): ?>selected="selected"<?php endif; ?>
                                >续期中</option>
                                <option value="5"
                                <?php if($_GET['audit_state']== 5): ?>selected="selected"<?php endif; ?>
                                >已还清</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">申请时间：</label>
                        <div class="col-xs-8">
                            <input type="text" class="time-input form-control" id="stime"
                                   value="<?php echo ($_GET['stimeStart']); ?> ~ <?php echo ($_GET['stimeEnd']); ?>">
                            <i class="layui-icon layui-icon-close-fill clear-icon-btn"></i>
                            <input type="hidden" name="stimeStart"
                                   value="<?php echo ($_GET['stimeStart']); ?>"/>
                            <input type="hidden" name="stimeEnd" value="<?php echo ($_GET['stimeEnd']); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">放款时间：</label>
                        <div class="col-xs-8">
                            <input type="text" class="time-input form-control" id="p-time"
                                   value="<?php echo ($_GET['p_start_time']); ?> ~ <?php echo ($_GET['p_end_time']); ?>">
                            <i class="layui-icon layui-icon-close-fill clear-icon-btn"></i>
                            <input type="hidden" name="p_start_time"
                                   value="<?php echo ($_GET['p_start_time']); ?>"/>
                            <input type="hidden" name="p_end_time" value="<?php echo ($_GET['p_end_time']); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">到期时间：</label>
                        <div class="col-xs-8">
                            <input type="text" class="time-input form-control" id="repay-time"
                                   value="<?php echo ($_GET['repay_timeStart']); ?> ~ <?php echo ($_GET['repay_timeEnd']); ?>">
                            <i class="layui-icon layui-icon-close-fill clear-icon-btn"></i>
                            <input type="hidden" name="repay_timeStart"
                                   value="<?php echo ($_GET['repay_timeStart']); ?>"/>
                            <input type="hidden" name="repay_timeEnd"
                                   value="<?php echo ($_GET['repay_timeEnd']); ?>"/>
                        </div>
                    </div>

                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">导入时间：</label>
                        <div class="col-xs-8">
                            <input type="text" class="time-input form-control" id="import-time"
                                   value="<?php echo ($_GET['add_timeStart']); ?> ~ <?php echo ($_GET['add_timeEnd']); ?>">
                            <i class="layui-icon layui-icon-close-fill clear-icon-btn"></i>
                            <input type="hidden" name="add_timeStart" class='add-timeStart'
                                   value="<?php echo ($_GET['add_timeStart']); ?>"/>
                            <input type="hidden" name="add_timeEnd" class="add-timeEnd"
                                   value="<?php echo ($_GET['add_timeEnd']); ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btnArea">
                <a href='#' onclick="filterRepeat(event)" class="btn btn-info">
                    <span class="public-label">显示重复数据</span>
                </a>
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
                <th>到账金额</th>
                <th>应还金额</th>
                <th>导入时间</th>
                <th>放款时间</th>
                <th>到期时间</th>
                <th>复借(是/否)</th>
                <th>订单笔数</th>
                <th>续期</th>
                <th>状态</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): $k = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><tr id="list-<?php echo ($vo["id"]); ?>">
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
                    <td><?php echo ($vo["get_money"]); echo (C("CURRENCY")); ?></td>
                    <td><?php echo ($vo["repay_money"]); echo (C("CURRENCY")); ?></td>
                    <td><?php echo (date("Y/m/d H:i:s",$vo["add_time"])); ?></td>
                    <td><?php echo (date("Y/m/d H:i:s",$vo["start_time"])); ?></td>
                    <td><?php echo (date("Y/m/d H:i:s",$vo["end_time"])); ?></td>
                    <td><?php echo ($vo['loan_total']>0?'是':'否'); ?></td>
                    <td><?php echo ($vo['loan_total']>0?$vo['loan_total']:1); ?></td>
                    <td><?php echo ($vo['delay_num']>0?'是':'否'); ?></td>
                    <?php $statusText=""; if ($vo['pending']==0){ if($vo['loan_total']>1){ $statusText="复借待审核"; }else{ $statusText="待审核"; } }else if ($vo['pending']==2){ $statusText="已拒绝"; }else if ($vo['pending']==1){ if($vo['status']==0||$vo['status']==5){ $statusText=$vo['delay']>=1?"续期中":"还款中"; }elseif($vo['status']==1){ $statusText="<span class='overdue-warn'>已逾期</span>"; }elseif($vo['status']==2){ $statusText="已还清"; }elseif($vo['status']==4){ $statusText="已续期"; } } ?>
                    <td><?php echo ($statusText); ?></td>
                    <td><?php echo ($vo["remark"]); ?></td>
                    <td class="text-left">
                        <?php $orderInfo=json_encode($vo); ?>
                        <a href="#" onclick="checkLoanOrderDetail(<?php echo ($k); ?>)" class="check-loan-order-detail"
                           data-order_info='<?php echo ($orderInfo); ?>'>订单详情</a>
                        <?php if(($vo["status"] != 2) and ($vo["pending"] == 1)): ?><a
                                href="javascript:customRepay('<?php echo ($vo["oid"]); ?>',<?php echo ($vo["money"]); ?>,<?php echo ($vo["to_money"]); ?>,<?php echo ($vo["uid"]); ?>);">自定义还款</a><?php endif; ?>
                        <a href="#" onclick="checkLoanRecord('CLLA<?php echo ($vo["oid"]); ?>')"
                           class="check-loan-order-record">操作记录</a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>

    </div>
    <div class="table-pagin-container">
        <div>
            <label><input type="checkbox" name="check_all"
                          title="全选搜索后的所有数据">&nbsp;全选搜索后的所有数据</label>
        </div>
        <div class="pull-left page-box col-xs-12 col-sm-6">
            <input type="hidden" name="current_status" value="<?php echo (($_GET['audit_state'])?($_GET['audit_state']):-1000); ?>">
            <button type="button" class="btn btn-primary btn-audit" onclick="batchAuditAction()">批量修改</button>
            <!--            <button type="button" class="btn btn-primary btn-audit" onclick="batchAudit('all')">全部修改</button>-->
            <button type="button" class="btn btn-danger btn-delete" onclick="batchDeleteAction()">批量删除</button>
            <!--            <button type="button" class="btn btn-danger btn-audit" onclick="batchDelete('all')">删除搜索</button>-->
        </div>
        <div class="pull-right page-box">
            <?php echo ($page); ?>
        </div>
    </div>
</div>
<div class="modal-audit-status" id="modal-audit-status" style="display: none;">
    <div class="modal-audit-status-content">
        <form class="layui-form" action="">
            <div class="layui-form-item layui-inline col-sm-12">
                <label class="col-xs-4 col-sm-4">修改状态</label>
                <div class="col-xs-8 col-sm-8">
                    <select name="change_status">
                        <option value="0">待审核</option>
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
<div class="modal-loan-info" id="modal-loan-info" style="display: none;">
    <table id="loan_info" lay-filter="table">
        <!--        <thead>-->
        <!--        <tr>-->
        <!--        <th lay-data="{field:'field', width:80}" rowspan="2">联系人</th>-->
        <!--        <th lay-data="{field:'value', width:120}" rowspan="2">金额</th>-->
        <!--        </tr>-->
        <!--        </thead>-->
    </table>
</div>
<div class="modal-loan-record" id="modal-loan-record" style="display: none;padding: 20px 10px">
    <table id="loan_record" lay-even lay-skin="line" lay-size="lg">
    </table>
</div>
<script type="text/html" id="toolEventEditRepayment">
    {{#  if(d.id){ }}
    <a class="layui-btn layui-btn-xs" lay-event="check">审核</a>
    {{#  } }}
</script>
</body>

<script type="text/javascript">
    var loan_list = <?php echo json_encode($list); ?> || [];
    function filterRepeat(evt){
        var query=$("#seachForm").serialize();
        console.log(query);
        location.href="/Loan/all?filter=1&"+query;
    }
    function checkLoanOrderDetail(k) {
        console.log(k);
        var order_info = loan_list[k - 1];
        console.log(order_info);
        checkLoanOrder(order_info);
    }

    function checkLoanOrder(data) {
        if (!data['overdue_fee']) data['overdue_fee'] = 0;
        var _repay_money = data['money'] + '+' + data['repay_fee'] + '+' + data['overdue_fee'] + '=';
        _repay_money += (parseInt(data['money']) + parseInt(data['repay_fee']) + parseInt(data['overdue_fee']));
        var overdue = 0;
        if (data['status'] == 1) {
            overdue = 1;
        }
        layui.use('table', function () {
            var table = layui.table;
            var _data = [
                {'name': '借款手续费', 'value': data['interest_money']},
                {'name': '还款手续费', 'value': data['repay_fee']},
                {'name': '续期累计费用', 'value': data['delay_fee']},
                {'name': '用户姓名', 'value': data['name']},
                {'name': '收款银行', 'value': data['bankname']},
                {'name': '银行卡号', 'value': data['banknum']},
                {'name': '情况说明', 'value': data['error']},
                {'name': '期数', 'value': data['BillNum']},
                {'name': '还款进度', 'value': data['hasBillNum']},
                {'name': '是否逾期', 'value': overdue},
                {'name': '逾期累计费用', 'value': data['overdue_fee']},
                {'name': '驳回原因', 'value': data['error']},
                //<a href="<?php echo U('Loan/viewContract',array('id'=>$vo['id']));?>" title="点击查看合同" target="_blank">查看合同</a>
            ];
            if (data['pending'] != 0 && data['pending'] != 2) {
                var _money = '<input type="number" value="' + data['repaid_money'] + '" readonly="readonly" class="repay-money-input repay-money-input-readonly form-control"> &nbsp;&nbsp;' +
                    '<span class="glyphicon glyphicon-pencil edit-repay-money-btn" data-lid="' + data['id'] + '"></span>' +
                    '<span class="glyphicon glyphicon-saved save-repay-money-btn" data-lid="' + data['id'] + '" style="display: none"></span>';
                _data.unshift({'name': '已还金额', 'value': _money});
                _data.unshift({'name': '总应还金额', 'value': _repay_money});
            }
            //第一个实例
            table.render({
                elem: '#loan_info',
                limit: 100,
                width: '360px',
                cellMinWidth: '180px',
                cols: [[ //标题栏
                    {field: 'name', title: 'Key', width: 200, sort: true, fixed: 'left'},
                    {field: 'value', title: 'value', width: 200}
                ]],
                data: _data
            });
            if (data['pending'] != 0 && data['pending'] != 2) {
                $(".edit-repay-money-btn").click(function (evt) {
                    evt.preventDefault();
                    $(this).hide();
                    $(this).prev().removeAttr('readonly')
                        .removeClass('repay-money-input-readonly')
                        .addClass('repay-money-input-edit');
                    $(this).next('.save-repay-money-btn').show();
                })
                $(".save-repay-money-btn").click(function (evt) {
                    evt.preventDefault();
                    var _lid = $(this).data('lid');
                    var _money = $(this).prevAll('.repay-money-input').val();
                    $(this).prevAll(".repay-money-input").attr('readonly', 'readonly')
                        .addClass('repay-money-input-readonly')
                        .removeClass('repay-money-input-edit');
                    $(this).prev('.edit-repay-money-btn').show();


                    $(this).hide();
                    $.ajax({
                        type: 'POST',
                        url: '/Loan/changeRepayMoney',
                        data: {lid: _lid, money: _money},
                        dataType: 'json',
                        success: function (resp) {
                            console.log(resp.info)
                            if (resp.status == 1) {

                            }
                            layer.msg(resp.info, {offset: getOffsetHeight()});
                        }
                    })

                })
            }

        });
        layer.open({
            type: 1,
            offset: '30vh',
            area: ['420px', '560px'],
            content: $('#modal-loan-info') //这里content是一个普通的String
        });

    }

    function checkLoanRecord(oid) {
        console.log(oid);
        var oid = oid.replace('CLLA', '');
        console.log(oid);
        layui.use('table', function () {
            var table = layui.table;

            //第一个实例
            table.render({
                elem: '#loan_record',
                url: '/Loan/ajaxRecord?order_id=' + oid + '', //数据接口
                cols: [[ //标题栏
                    {"field": "oid", title: "订单ID", "minWidth": 220},
                    {"field": "org_status", title: "原来状态", "minWidth": 120},
                    {"field": "action", title: "操作类型", "minWidth": 120},
                    {"field": "position", title: "操作位置"},
                    {"field": "sid", title: "操作用户ID"},
                    {"field": "created_at", title: "操作时间", "minWidth": 180},
                ]],
            });

        });
        var width = $(window.top).width();
        console.log(width);
        layer.open({
            type: 1,
            offset: [getOffsetHeight(), (width - 900) / 2 - 200],
            // area:['640px','360px'],
            content: $('#modal-loan-record') //这里content是一个普通的String
        });
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