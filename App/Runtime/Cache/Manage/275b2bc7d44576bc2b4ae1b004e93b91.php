<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
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
    <link rel="stylesheet" href="__PUBLIC__/Manage/css/style.css">
    <link href="__PUBLIC__/Manage/css/mobile.css" rel="stylesheet"/>
    <script src="__PUBLIC__/Manage/js/index.js"></script>
    <title>工作台 - CvPHP管理系统</title>
    <style>


        .header-collapse {
            width: 67px;
            height: 67px;
            margin-left: 200px;
            z-index: 99999;
            text-align: center;
            vertical-align: middle;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .btn-collapse {
            color: white;
            font-size: 30px;
        }

        .toggle-menu {
            position: relative;
            overflow: hidden;
        }
    </style>
</head>
<body>
<div class="app-container">
    <div class="head" onclick="toggleMenu()">
        <div class="header-collapse">
            <i class="layui-icon layui-icon-shrink-right btn-collapse"></i>
        </div>
        <div class="head_right">
            <ul>
                <li>
                    <span>欢迎你，<em><?php echo ($adminInfo["username"]); ?></em></span>
                    <!--<span>唯一官方QQ群：512473867</span>-->
                </li>
                <li>
                    <a href="javascript:if(confirm('您确定要退出登录吗？'))window.location.href='<?php echo U('Index/logout');?>';"
                       class="zhuxi">注销</a>
                </li>
                <li>
                    <a href="<?php echo U('Index/changepass');?>" target="iframe" class="xiugai">修改密码</a>
                </li>

            </ul>
        </div>
    </div>
    <div class="content">
        <div class="nav">
            <div class="logo">
                <a href="<?php echo U('Index/index');?>"><img src="__PUBLIC__/Manage/images/logo.png"></a>
            </div>
            <ul class="nav_list">
                <?php if(ISADMIN == 1): ?><li class="yiji" data-title="工作台">
                        <a href="javascript:;" data-url="<?php echo U('Index/main');?>" class="one">工作台</a>
                    </li><?php endif; ?>
                <li class="yiji" data-title="借款管理">
                    <a href="javascript:void(0)" class="two action">借款管理</a>
                    <ul class="erji">
                        <li data-title="借款列表">
                            <a href="javascript:;" data-url="<?php echo U('Loan/all');?>">借款列表</a>
                        </li>
                        <li data-title="借款审核">
                            <a href="javascript:;" data-url="<?php echo U('Loan/pending');?>">待审核</a>
                        </li>
                        <li data-title="复借待审核">
                            <a href="javascript:;" data-url="<?php echo U('Loan/pending',array('__type__'=>2));?>">复借待审核</a>
                        </li>
                        <li data-title="借款审核">
                            <a href="javascript:;" data-url="<?php echo U('Loan/process');?>">已处理</a>
                        </li>
                        <li data-title="驳回借款">
                            <a href="javascript:;" data-url="<?php echo U('Loan/refuse');?>">已拒绝</a>
                        </li>
                        <li data-title="逾期借款">
                            <a href="javascript:;" data-url="<?php echo U('Loan/overdue');?>">已逾期</a>
                        </li>
                        <li data-title="借款列表">
                            <a href="javascript:;" data-url="<?php echo U('Loan/index');?>">还款中</a>
                        </li>
                        <li data-title="续期中">
                            <a href="javascript:;" data-url="<?php echo U('Loan/delay');?>">续期中</a>
                        </li>
                        <li data-title="回款借款">
                            <a href="javascript:;" data-url="<?php echo U('Loan/payoff');?>">已还清</a>
                        </li>
                        <?php if(ISADMIN == 1): ?><li data-title="账单列表">
                                <a href="javascript:;" data-url="<?php echo U('Loan/bill');?>">账单表</a>
                            </li><?php endif; ?>
                    </ul>
                </li>
                <?php if(ISADMIN == 1): ?><li class="yiji" data-title="支付订单">
                        <a href="javascript:;" class="three action">支付订单</a>
                        <ul class="erji">
                            <li data-title="还款支付订单">
                                <a href="javascript:;" data-url="<?php echo U('Pay/index');?>">还款支付订单</a>
                            </li>
                            <li data-title="延期支付订单">
                                <a href="javascript:;" data-url="<?php echo U('DelayPay/index');?>">延期支付订单</a>
                            </li>
                        </ul>
                    </li>
                    <li class="yiji" data-title="还款凭证">
                        <a href="javascript:;" data-url="<?php echo U('Pay/proof');?>" class="three">还款凭证</a>
                    </li>
                    <li class="yiji" data-title="自由块">
                        <a href="javascript:;" data-url="<?php echo U('Block/index');?>" class="Four">自由块</a>
                    </li>

                    <li class="yiji" data-title="用户管理">
                        <a href="javascript:void(0)" class="six action">用户管理</a>
                        <ul class="erji">

                            <li data-title="用户列表">
                                <a href="javascript:;" data-url="<?php echo U('User/index');?>">用户列表</a>
                            </li>
                            <li data-title="管理员">
                                <a href="javascript:;" data-url="<?php echo U('Admin/index');?>">管理员</a>
                            </li>

                            <li data-title="资料审核">
                                <a href="javascript:;" data-url="<?php echo U('Info/index');?>">资料审核</a>
                            </li>
                        </ul>
                    </li><?php endif; ?>
                <?php if(ISADMIN == 0): ?><li class="yiji" data-title="资料审核">
                        <a href="javascript:;" data-url="<?php echo U('Info/index');?>" class="six">资料审核</a>
                    </li><?php endif; ?>
                <?php if(ISADMIN == 1): ?><li class="yiji" data-title="系统设置">
                        <a href="javascript:void(0)" class="seven action">系统设置</a>
                        <ul class="erji">
                            <li data-title="基本设置">
                                <a href="javascript:;" data-url="<?php echo U('Setting/index');?>">基本设置</a>
                            </li>
                            <li data-title="API设置">
                                <a href="javascript:;" data-url="<?php echo U('Setting/api');?>">API设置</a>
                            </li>
                            <li data-title="借款设置">
                                <a href="javascript:;" data-url="<?php echo U('Setting/loan');?>">借款设置</a>
                            </li>
                            <li data-title="合同设置">
                                <a href="javascript:;" data-url="<?php echo U('Setting/contract');?>">合同设置</a>
                            </li>
                            <li data-title="信征商品">
                                <a href="javascript:;" data-url="<?php echo U('Product/index');?>">信征商品</a>
                            </li>
                            <li data-title="数据清理">
                                <a href="javascript:;" data-url="<?php echo U('Setting/clear');?>">数据清理</a>
                            </li>
                        </ul>
                    </li>
                    <li class="yiji" data-title="常见问题">
                        <a href="javascript:void(0)" data-url="<?php echo U('Help/index');?>" class="Five action">常见问题</a>
                        <ul class="erji">
                            <li data-title="基本设置">
                                <a href="javascript:;" data-url="<?php echo U('Feedback/index');?>">用户反馈</a>
                            </li>
                            <li data-title="基本设置">
                                <a href="javascript:;" data-url="<?php echo U('Help/index');?>">问题列表</a>
                            </li>
                        </ul>
                    </li><?php endif; ?>
            </ul>
        </div>
        <div class="con">
            <iframe src="<?php echo U('Index/main');?>" id="iframe" onload="changeFrameHeight();" name="iframe"></iframe>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    function toggleMenu() {
        $(".app-container").toggleClass("toggle-menu");
        if ($(".app-container").hasClass("toggle-menu")) {
            $(".header-collapse").css({'margin-left': 0})
            $(".content .nav").css({'position': 'absolute', left: '-200px'})
            $(".content .con").css({'margin-left': 0})
            $(this).css({'position': 'absolute', left: '-200px'})

        } else {
            $(".header-collapse").css({'margin-left': '200px'})
            $(".content .nav").css({'position': 'absolute', left: 0})
            $(".content .con").css({'margin-left': '200px'})
            $(this).css({'position': 'absolute', left: 0})
        }
    }
</script>
</html>