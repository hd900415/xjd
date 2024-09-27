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
    <title>用户反馈</title>
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
        <h5>用户反馈</h5>
    </div>
    <form method="get" id="seachForm">
        <input type="hidden" name="m" value="Feedback"/>
        <input type="hidden" name="a" value="index"/>
        <div class="public-selectArea" id="pxs">
            <div class="clearfix">
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">用户名/手机号：</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" name="s-name" value="<?php echo ($_GET['s-name']); ?>">
                        </div>
                    </div>
                </div>
                <div class="wp_box col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-4 control-label">反馈时间：</label>
                        <div class="col-xs-8">
                            <input type="text" class="time-input form-control" id="s-time"
                                   value="<?php echo ($_GET['s-timeStart']); ?> ~ <?php echo ($_GET['s-timeEnd']); ?>">
                            <i class="layui-icon layui-icon-close-fill clear-icon-btn"></i>
                            <input type="hidden" name="s-timeStart"
                                   value="<?php echo ($_GET['s-start_time']); ?>"/>
                            <input type="hidden" name="s-timeEnd" value="<?php echo ($_GET['s-end_time']); ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btnArea">
                <a href='javascript:$("#seachForm").submit();' class="btn btn-sereachBg">
                    <i class="glyphicon glyphicon-search public-ico"></i>
                    <span class="public-label">查询</span>
                </a>
                <!--                <button onclick="exportLoanOrder(event)" class="btn btn-primary">-->
                <!--                    <span class="public-label">导出订单</span>-->
                <!--                </button>-->
            </div>
        </div>
    </form>
    <div class="scroll-bar-table" style="margin-top: 10px">
        <table class="table table-hover">
            <thead>
            <tr>
                <th><input type="checkbox" value="0" lay-skin="primary" id="checkall"
                           onclick="javascript:checkAll(this);"></th>
                <th>用户ID</th>
                <th>用户名/手机号</th>
                <th>内容</th>
                <th>图片其他信息</th>
                <th>联系方式</th>
                <th>反馈时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="list-<?php echo ($vo["id"]); ?>">
                    <td><input type="checkbox" name="lid[]" class="check-lid" value="<?php echo ($vo["id"]); ?>"></td>
                    <td><?php echo ($vo["uid"]); ?></td>
                    <td><?php echo ($vo["uname"]); ?></td>
                    <td><?php echo ($vo["content"]); ?></td>
                    <td>
                        <?php if($vo["attachment"] != null): ?><div class="layer-check-image"><img src="<?php echo ($vo["attachment"]); ?>" layer-src="<?php echo ($vo["attachment"]); ?>"
                                                                style="height: 120px" class="check-preview"/></div><?php endif; ?>
                    </td>
                    <td><?php echo ($vo["contact"]); ?></td>
                    <td><?php echo (date("Y/m/d H:i:s H:i:s",$vo["add_time"])); ?></td>
                   <td>
                       <a href="javascript:deleteFacebook('<?php echo ($vo["id"]); ?>');" class="btn">删除</a>
                   </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
    <div class="table-pagin-container">
        <!--        <div class="pull-left page-box">-->
        <!--            <input type="hidden" name="current_status" value="3">-->
        <!--            <button type="button" class="btn btn-primary btn-audit" onclick="batchAudit()">批量修改</button>-->
        <!--&lt;!&ndash;            &lt;!&ndash;&ndash;&gt;<button type="button" class="btn btn-primary btn-audit" onclick="batchAudit('all')">全部修改</button>&ndash;&gt;-->
        <!--         -->
        <!--        </div>-->
        <button type="button" class="btn btn-danger btn-delete" onclick="batchDeleteAction()">批量删除</button>
        <div class="pull-right page-box">
            <?php echo ($page); ?>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    function deleteFacebook(id) {
        layer.confirm(
            '建议保留反馈,反馈删除后不可恢复,请确认？',
            {
                btn: ['确认删除', '取消'],
                offset: ['45wh']
            }, function () {
                cvphp.post(
                    "<?php echo U('Feedback/remove');?>",
                    {
                        id: id
                    },
                    function (data) {
                        if (data.status != 1) {
                            layer.msg(data.info,{offset: ['45wh']});
                        } else {
                            $("#list-" + id).remove();
                            layer.msg("操作成功",{offset: ['45wh']});
                        }
                    }
                );
            }
        );
    }
    function batchDeleteAction() {
        var check_all = $("input[name=check_all]").prop('checked')
        var checkType = 1;
        if (check_all == true) checkType = 2;
        layer.confirm(
            '建议保留反馈,反馈删除后不可恢复,请确认？',
            {
                btn: ['确认删除', '取消'],
                offset: ['45wh']
            }, function () {
                // batchDelete('', checkType)
                var ids=[];

                $(".check-lid:checked").each(function (index,ele){
                    ids.push($(ele).val());
                });
                cvphp.post(
                    "<?php echo U('Feedback/remove');?>",
                    {
                        id: ids
                    },
                    function (data) {
                        if (data.status != 1) {
                            layer.msg(data.info,{offset: ['45wh']});
                        } else {
                            ids.forEach(function(v){
                                console.log(v);
                                $("#list-" + v).remove();
                            });
                            layer.msg("操作成功",{offset: ['45wh']});
                        }
                    }
                );
            }
        );

    }
    $(".check-preview").click(function () { //查看图片大图
        console.log('hello world');
        var _url = $(this).attr('src');
        var imgHeight = $(this)[0].naturalHeight;
        console.log($(this), 'imgHeight:', imgHeight);
        checkPhotoPreview(_url, imgHeight);
    })
</script>
</html>