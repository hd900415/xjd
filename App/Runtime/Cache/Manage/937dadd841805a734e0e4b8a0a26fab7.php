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
    <title>还款凭证列表</title>
    <style type="text/css">
    .layui-layer-photos .layui-layer-phimg img{
        width: 50%;
    }
    </style>
</head>
<body>
<div class="nestable">
    <div class="console-title console-title-border drds-detail-title clearfix">
        <h5>还款凭证</h5>
    </div>
    <form method="get" id="seachForm">
        <input type="hidden" name="m" value="Pay"/>
        <input type="hidden" name="a" value="proof"/>
        <div class="public-selectArea" id="pxs">
            <div class="clearfix">
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
                        <label class="col-xs-4 control-label">凭证提交时间：</label>
                        <div class="col-xs-8">
                            <input type="text" class="time-input form-control" id="import-time"
                                   value="<?php echo ($_GET['add_timeStart']); ?> ~ <?php echo ($_GET['add_timeEnd']); ?>">
                            <i class="layui-icon layui-icon-close-fill clear-icon-btn"></i>
                            <input type="hidden" name="add_timeStart" class='add_timeStart'
                                   value="<?php echo ($_GET['add_timeStart']); ?>"/>
                            <input type="hidden" name="add_timeEnd" class="add_timeEnd"
                                   value="<?php echo ($_GET['add_timeEnd']); ?>"/>
                        </div>
                    </div>
                </div>

            </div>
            <div class="btnArea">
                <a href="<?php echo U('/Pay/proof');?>?status=-4" class="pull-left" style="margin-top: 10px;color:red"><span
                        class="glyphicon glyphicon-trash" style="margin:0 10px "></span>查看回收站</a>
                <a href="javascript:proofTrash()" class="pull-left"
                   style="margin-top: 10px;color:#f9c400"><span class="glyphicon glyphicon-trash"
                                                                style="margin:0 10px "></span>清空回收站</a>
                <a href='javascript:$("#seachForm").submit();' class="btn btn-sereachBg">
                    <i class="glyphicon glyphicon-search public-ico"></i>
                    <span class="public-label">查询</span>
                </a>
                <button onclick="exportPayProofOrder(event,2)" class="btn btn-primary">
                    <span class="public-label">导出支付凭证</span>
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
                <th>订单流水号</th>
                <th>关联贷款订单号</th>
                <th>支付单号</th>
                <th>姓名</th>
                <th>身份证号</th>
                <th>用户名</th>
                <th>订单金额</th>
                <th>UTR/VA</th>
                <th>支付图片</th>
                <th>创建时间</th>
                <th>贷款订单状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="list-<?php echo ($vo["id"]); ?>">
                    <td><input type="checkbox" name="lid[]" class="check-lid" value="<?php echo ($vo["id"]); ?>"></td>
                    <td><?php echo ($vo["id"]); ?></td>
                    <td><a href="<?php echo U('Loan/all');?>?oid=<?php echo ($vo["order_id"]); ?>" target="_blank"><?php echo ($vo["order_id"]); ?></a></td>
                    <td>
                       还( <a href="<?php echo U('Pay/index');?>?toid=<?php echo ($vo["order_id"]); ?>&source_type=proof" target="_blank"><?php echo (($vo["pay_total"])?($vo["pay_total"]):0); ?></a>)
                       续( <a href="<?php echo U('DelayPay/index');?>?toid=<?php echo ($vo["order_id"]); ?>&source_type=proof" target="_blank"><?php echo (($vo["delay_total"])?($vo["delay_total"]):0); ?></a>)
                    </td>
                    <td><?php echo ($vo["realname"]); ?></td>
                    <td><?php echo ($vo["idcard"]); ?></td>
                    <td><?php echo ($vo["uname"]); ?></td>
                    <td><?php echo ($vo["money"]); echo (C("CURRENCY")); ?></td>
                    <td><?php echo ($vo["utr"]); ?></td>

                    <td>
                        <?php if($vo['repay_image']): $imgs=explode(',',$vo['repay_image']); $imgcontent=""; foreach($imgs as $img){ $imgcontent.="<img src='".$img."' style='height:120px;' class='check-preview' />"; } echo ($imgcontent); endif; ?>
                    </td>
                    <td><?php echo (date("Y/m/d H:i:s",$vo["add_time"])); ?></td>
                    <td>
                        <?php $statusText=""; if($vo['order_status']==0||$vo['order_status']==5){ $statusText=$vo['delay']>=1?"续期中":"还款中"; }elseif($vo['order_status']==1){ $statusText="<span class='overdue-warn'>已逾期</span>"; }elseif($vo['order_status']==2){ $statusText="已还清"; }elseif($vo['order_status']==4){ $statusText="续期中"; } ?>
                        <?php echo ($statusText); ?>
                    </td>
                    <td class="text-left">
                        <?php if($_GET['status']== 0): ?><a
                                href="javascript:delPayOrder('<?php echo ($vo["id"]); ?>',-4);">移动到回收站</a><?php endif; ?>
                        <?php if($_GET['status']== -4): ?><a href="javascript:delPayOrder('<?php echo ($vo["id"]); ?>',0);">恢复</a>
                            <a href="javascript:delPayOrder('<?php echo ($vo["id"]); ?>',-1);">彻底删除</a><?php endif; ?>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
    <div class="table-pagin-container">
        <div class="pull-left page-box col-xs-12 col-sm-6">
            <?php if($_GET['status']== 0): ?><button type="button" class="btn btn-danger btn-delete" onclick="batchPayDelete()">批量移动到回收站
                </button><?php endif; ?>
        </div>
        <div class="pull-right page-box">
            <?php echo ($page); ?>
        </div>
    </div>
</div>
</body>
<script>
    function delPayOrder(id, _status) {
        if (_status == -4) _text = '建议保留凭证,请确认？'
        if (_status == 0) _text = '此操作将凭证恢复,请确认？'
        if (_status == -1) _text = '此操作将删除凭证,建议保留，如果确定删除请确认？'
        layer.confirm(
            _text,
            {
                btn: ['确认', '取消']
            }, function () {
                cvphp.post(
                    "<?php echo U('Pay/delProof');?>",
                    {
                        id: id,
                        status: _status
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

    function proofTrash() {
        layer.confirm(
            '确认清空回收站吗',
            {
                btn: ['确认清空', '取消']
            }, function () {
                cvphp.post(
                    "<?php echo U('/Pay/proofTrash');?>?status=-4",
                    {},
                    function (data) {
                        if (data.status != 1) {
                            layer.msg(data.info);
                        } else {
                            window.location.reload();
                            layer.msg("操作成功");
                        }
                    }
                );
            }
        );
    }

    function getPayIds() {
        var check_loanIds = [];
        $("input.check-lid").each(function () {
            if ($(this).is(":checked")) {
                var _id = parseInt($(this).val());
                check_loanIds.push(_id);
            }
        });
        return check_loanIds;
    }

    function batchPayDelete(type = '') {
        var check_loanIds = getPayIds();
      
        layer.confirm('你确定要移动到回收站吗？', {offset: '40vh'}, function () {
            var ly1 = layer.load(1);
            var params = {};
            params = getQueryString();
            console.log(params);
            params['type'] = 1;
            delete params.m;
            delete params.a;
            if (check_loanIds.length > 0) {
                if ($("#checkall").is(':checked')) {
                    params['type'] = 2;
                }
            }
            params['ids'] = check_loanIds;
            console.log('批量删除:', params);
            if (check_loanIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    data: params,
                    url: '/Pay/batchPayProofDelete',
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
         
        })
    }

    $(".check-preview").click(function () { //查看图片大图
        console.log('hello world');
        var _url = $(this).attr('src');
        console.log(_url);
        var imgHeight = $(this)[0].naturalHeight;
        console.log($(this), 'imgHeight:', imgHeight);
        checkPhotoPreview(_url, imgHeight*0.5);
    })
</script>
</html>