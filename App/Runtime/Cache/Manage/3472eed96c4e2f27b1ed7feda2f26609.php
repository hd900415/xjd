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
    <script src="__PUBLIC__/Manage/js/wangEditor/wangEditor.min.js"></script>
    <title>添加用户</title>
</head>
<body>
<div class="nestable">
    <div class="console-title console-title-border drds-detail-title clearfix">
        <h5>添加用户</h5>
    </div>
    <div class="public-selectArea public-selectArea1">
        <form action="<?php echo U('User/create');?>" method="post">
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>用户名/手机号：</dt>
                        <dd>
                            <input type="text" name="telnum">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>密码：</dt>
                        <dd>
                            <input type="password" name="password">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>确认密码：</dt>
                        <dd>
                            <input type="password" name="repassword">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>邮箱：</dt>
                        <dd>
                            <input type="text" name="email">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>firstname：</dt>
                        <dd>
                            <input type="text" name="firstname">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>lastname：</dt>
                        <dd>
                            <input type="text" name="lastname">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>额度:</dt>
                        <dd>
                            <input type="text" name="quota" value="<?php echo ((C("DEFAULT_QUOTA"))?(C("DEFAULT_QUOTA")):'50000'); ?>">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>信用度:</dt>
                        <dd>
                            <input type="text" name="credit">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>借款期限:</dt>
                        <dd>
                            <input type="text" name="loan_time" value="7">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>开户银行名字:</dt>
                        <dd>
                            <input type="text" name="bank_name">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>银行卡号:</dt>
                        <dd>
                            <input type="text" name="bank_num">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>开户人真实姓名:</dt>
                        <dd>
                            <input type="text" name="real_name">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>开户人身份证号:</dt>
                        <dd>
                            <input type="text" name="idcard">
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>vippid:</dt>
                        <dd>
                            <select class="select" name="type">
                                <option value="1">101</option>
                            </select>
                        </dd>
                    </dl>
                </div>
            </div>
            
            <div class="clearfix">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>状态：</dt>
                        <dd>
                            <select class="select" name="status">
                                <option value="1" selected="selected">启用</option>
                                <option value="0">禁用</option>
                            </select>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="btnArea">
                <a href="javascript:;" class="btn btn-sereachBg" id="submitBtn">
                    <span class="public-label">提交</span>
                </a>
            </div>
        </form>
    </div>
</div>
</body>
<script>
    $(function(){
        $("#submitBtn").on('click',function(){
            cvphp.submit($("form"),function(data){
                if(data.status!=1){
                    layer.msg(data.info);
                }else{
                    layer.msg('添加成功');
                    setTimeout(function(){
                        window.location.href="<?php echo U('User/index');?>";
                    },2000);
                }
            });
        });
    });
</script>
</html>