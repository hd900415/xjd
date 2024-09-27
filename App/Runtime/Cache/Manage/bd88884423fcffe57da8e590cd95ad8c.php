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
    <title>支付订单列表</title>
    <style>
        .pay-info {
            height: 30px;
            font-size: 14px;
            margin: 20px 10px 10px;
        }

        .pay-info span.label-title {
            font-weight: 600;
        }

        span.pay-info-money {
            color: #ff2223;
        }
    </style>
</head>
<body>
<div class="nestable">
    <div class="console-title console-title-border drds-detail-title clearfix">
        <h5>支付订单</h5>
    </div>
    <form method="get" id="seachForm">
        <input type="hidden" name="m" value="Pay"/>
        <input type="hidden" name="a" value="index"/>
        <?php if($_GET['source_type']== 'proof'): ?><input type="hidden" name="source_type" value="proof"/><?php endif; ?>
        <div class="public-selectArea" id="pxs">
            <div class="clearfix">
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">用户ID：</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" name="uid" value="<?php echo ($_GET['uid']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">用户名/手机号：</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" name="uname" value="<?php echo ($_GET['uname']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">贷款订单号：</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" name="toid" value="<?php echo ($_GET['toid']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">支付订单号：</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" name="payid" value="<?php echo ($_GET['payid']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">上游订单号：</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" name="upid" value="<?php echo ($_GET['upid']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">账单状态：</label>
                        <div class="col-xs-8">
                            <select class="select form-control" name="status">
                                <option value="-100" selected="selected"
                                <?php if(isset($_GET['status']) == false): ?>selected<?php endif; ?>
                                >全部</option>
                                <option value="0"
                                <?php if(isset($_GET['status'])&&$_GET['status']== 0): ?>selected<?php endif; ?>
                                >未支付</option>
                                <option value="1"
                                <?php if($_GET['status']== 1): ?>selected<?php endif; ?>
                                >已支付</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">支付创建时间：</label>
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
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">支付完成时间：</label>
                        <div class="col-xs-8">
                            <input type="text" class="time-input form-control" id="pay-time"
                                   value="<?php echo ($_GET['pay_timeStart']); ?> ~ <?php echo ($_GET['pay_timeEnd']); ?>">
                            <i class="layui-icon layui-icon-close-fill clear-icon-btn"></i>
                            <input type="hidden" name="pay_timeStart" class='pay-timeStart'
                                   value="<?php echo ($_GET['pay_timeStart']); ?>"/>
                            <input type="hidden" name="pay_timeEnd" class="pay-timeEnd"
                                   value="<?php echo ($_GET['pay_timeEnd']); ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btnArea">
                <a href='javascript:$("#seachForm").submit();' class="btn btn-sereachBg">
                    <i class="glyphicon glyphicon-search public-ico"></i>
                    <span class="public-label">查询</span>
                </a>
                <button onclick="exportPayOrder(event,1)" class="btn btn-primary">
                    <span class="public-label">导出支付订单</span>
                </button>
            </div>
        </div>
    </form>
    <div class="scroll-bar-table">
        <div class="layui-row pay-info">
            <div class="layui-col-md9">
                <?php if($_GET['source_type']!= 'proof'): ?><span class="label-title">提交订单数</span>:<span
                        class="pay-info-money"><?php echo (($allSubmitTotal)?($allSubmitTotal):0); ?></span>,
                    <span class="label-title">支付订单数</span>:<span
                        class="pay-info-money"><?php echo (($allPayTotal)?($allPayTotal):0); ?></span>,
                    <span class="label-title">当前总还款金额</span>:<span class="pay-info-money"><?php echo (($totalPayMoney)?($totalPayMoney):0); ?></span>,
                    <span class="label-title">今日还款金额</span>:<span class="pay-info-money"><?php echo (($todayPayMoney)?($todayPayMoney):0); ?></span>,
                    <span class="label-title">昨日还款金额</span>:<span class="pay-info-money"><?php echo (($yestdayPayMoney)?($yestdayPayMoney):0); ?></span><?php endif; ?>
            </div>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>订单流水号</th>
                <th>关联贷款订单号</th>
                <th>支付单号</th>
                <th>上游单号</th>
                <th>用户名</th>
                <th>提交金额</th>
                <th>回调金额</th>
                <!--							<th>UTR</th>-->
                <!--							<th>UTR图片</th>-->
                <!--							<th>支付图片</th>-->
                <th>创建时间<br/>
                    <p style="color:red">回调时间</p></th>
                <th>类型</th>
                <th>状态</th>
                <th>备注</th>
                <!--							<th>支付时间</th>-->
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="list-<?php echo ($vo["id"]); ?>">
                    <td><?php echo ($vo["id"]); ?></td>
                    <td>
                        <?php if($vo["loid"] != null): ?><a href="<?php echo U('Loan/all');?>?oid=<?php echo ($vo["loid"]); ?>" target="_blank"><?php echo ($vo["loid"]); ?></a>
                            <?php else: ?>
                            <?php echo ($vo["order_id"]); endif; ?>
                    </td>
                    <td><?php echo ($vo["oid"]); ?></td>
                    <td><?php echo ($vo["upid"]); ?><br/><span style="color:#0a53be"><?php echo ($vo["api_trade_no"]); ?></span></td>
                    <td><?php echo ($vo["user"]["telnum"]); ?></td>
                    <td><?php echo ($vo["money"]); echo (C("CURRENCY")); ?></td>
                    <td><?php echo ($vo["tomoney"]); echo (C("CURRENCY")); ?></td>
                    <!--							<td><?php echo ($vo["utr"]); ?></td>-->
                    <!--							<td><?php if($vo['utr_image']): ?><img src="<?php echo ($vo["utr_image"]); ?>" style="height: 120px"><?php endif; ?></td>-->
                    <!--							<td><?php if($vo['repay_image']): ?><img src="<?php echo ($vo["repay_image"]); ?>" style="height: 120px"><?php endif; ?></td>-->
                    <td><?php echo (date("Y/m/d H:i:s H:i:s",$vo["add_time"])); ?><br/>
                        <p style="color:#0aa8e4">
                            <?php if($vo["pay_time"] != 0): echo (date("Y/m/d H:i:s",$vo["pay_time"])); endif; ?>
                        </p>
                    </td>
                    <td>
                        <?php if($vo["type"] == 1): ?>部分还款
                            <?php else: ?>
                            全额还款<?php endif; ?>
                    </td>
                    <td>
                        <?php if($vo['status'] == 1): ?>已支付
                            <?php else: ?>
                            未支付<?php endif; ?>
                    </td>
                    <!--							<td>-->
                    <!--								<?php if($vo['status'] == 1): echo (date("Y/m/d H:i:s",$vo["pay_time"])); else: ?> -<?php endif; ?>-->
                    <!--							</td>-->
                    <td><?php echo ($vo["remark"]); ?></td>
                    <td class="text-left">
                        <a href="javascript:delPayOrder('<?php echo ($vo["id"]); ?>');">删除订单</a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
    <div class="table-pagin-container">
        <div class="pull-right page-box">
            <?php echo ($page); ?>
        </div>
    </div>
</div>
</body>
<script>
    function delPayOrder(id) {
        layer.confirm(
            '建议保留订单,订单删除后不可恢复,请确认？',
            {
                btn: ['确认删除', '取消']
            }, function () {
                cvphp.post(
                    "<?php echo U('Pay/delOrder');?>",
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
</script>
</html>