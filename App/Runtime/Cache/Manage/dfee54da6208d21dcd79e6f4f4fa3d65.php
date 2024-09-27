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
		<title>资料审核-汇码网（www.huimaw.com）</title>
		<style>
			.redText{
				color: red;
			}
			.resetInfo-item{
			    min-width: 165px;
			    font-size: 14px;
			}
			.resetInfo-item h3{
				font-size: 20px;
				margin-left: 20px;
			}
			.resetInfo-item select{
				margin-left: 20px;
				margin-top: 8px;
			}
			.resetInfo-item button{
				margin: 20px 0 10px 20px;
				width: 120px;
			    color: #fff;
			    background-color: #51a0f1;
			    border: 0;
			    border-radius: 4px;
			}
		</style>
	</head>
	<body>
		<div class="nestable">
			<div class="console-title console-title-border drds-detail-title clearfix">
				<h5>资料审核</h5>
			</div>
			<form method="get" id="seachForm">
				<input type="hidden" name="m" value="Info" />
				<input type="hidden" name="a" value="index" />
				<div class="public-selectArea" id="pxs">
					<div class="clearfix">
						<div class="wp_box col-sm-4">
							<div class="form-group">
								<label class="col-xs-4 control-label">用户名/电话：</label>
								<div class="col-xs-8">
									<input type="text" name="s-username" class="form-control" value="<?php echo ($_GET['s-username']); ?>">
								</div>
							</div>
						</div>
						<div class="wp_box col-sm-4">
							<div class="form-group">
								<label class="col-xs-4 control-label">资料状态：</label>
								<div class="col-xs-8">
									<select class="select form-control" name="status">
										<option value="" selected="selected">全部</option>
										<option value="0">不完善</option>
										<option value="1">待审核</option>
										<option value="2">审核通过</option>
										<option value="-1">不通过</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="btnArea">
						<a href='javascript:$("#seachForm").submit();' class="btn btn-sereachBg">
							<i class="glyphicon glyphicon-search public-ico"></i>
							<span class="public-label">查询</span>
						</a>
					</div>
				</div>
			</form>
			<div class="scroll-bar-table">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>序号</th>
							<th>用户名/手机号</th>
							<th>审核结果</th>
							<th>资料</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="list-<?php echo ($vo["id"]); ?>">
							<td align="center"><?php echo ($vo["id"]); ?></td>
							<td><?php echo ($vo["user"]["telnum"]); ?></td>
							<td>
								<?php if($vo['status'] == 0): ?>资料未完善<?php endif; ?>
								<?php if($vo['status'] == 1): ?><span class="redText">等待审核</span><?php endif; ?>
								<?php if($vo['status'] == -1): ?>未通过审核<?php endif; ?>
								<?php if($vo['status'] == 2): ?>审核通过<?php endif; ?>
							</td>
							<td class="td-status">
								<a href="<?php echo U('Info/view',array('uid'=>$vo['uid']));?>" target="_blank">点击查看</a>
							</td>
							<td class="text-left">
								<?php if($vo['status'] == 0): endif; ?>
								<?php if($vo['status'] == 1): ?><a href="javascript:Adopt('<?php echo ($vo["uid"]); ?>','<?php echo ($vo["user"]["telnum"]); ?>');">通过审核并授权额度</a>
									<a href="javascript:Refuse('<?php echo ($vo["uid"]); ?>','<?php echo ($vo["user"]["telnum"]); ?>');">不符合借款条件</a><?php endif; ?>
								<?php if($vo['status'] == -1): ?><a href="javascript:resetInfo('<?php echo ($vo["id"]); ?>');">重置资料</a>
									<a href="javascript:Adopt('<?php echo ($vo["uid"]); ?>','<?php echo ($vo["user"]["telnum"]); ?>');">二审通过并授权额度</a><?php endif; ?>
								<?php if($vo['status'] == 2): ?><a href="javascript:resetInfo('<?php echo ($vo["id"]); ?>');">重置资料</a><?php endif; ?>
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
			<div class="resetInfo" style="display: none;">
				<form action="<?php echo U('Info/resetInfo');?>" method="post">
					<input type="hidden" name="id" value="" id="resetInfoId" />
					<div class="resetInfo-item">
						<h3>选择重置项</h3>
						<select name="action">
							<option value="all">全部</option>
							<option value="identity">身份信息</option>
							<option value="contacts">联系人信息</option>
							<option value="bank">收款银行信息</option>
							<option value="addess">联系地址及其他</option>
							<option value="mobile">运营商数据</option>
							<option value="taobao">淘宝数据</option>
						</select>
					</div>
					<div class="resetInfo-item">
						<button type="button">确定</button>
					</div>
				</form>
			</div>
		</div>
	</body>
	<script>
		//资料审核通过并设置用户额度
		function Adopt(uid,name){
			layer.prompt(
				{
					title: '为用户  ['+name+'] 设定额度',
					formType: 0
				},
				function(quota,index){
					cvphp.post(
						"<?php echo U('Info/adopt');?>",
						{
							uid:uid,
							quota:quota
						},
						function(data){
							if(data.status!=1){
								layer.msg(data.info);
							}else{
								layer.close(index);
								layer.msg("审核完成");
								setTimeout(function(){location.reload();},1000);
							}
						}
					);
				}
			);
		}
		//资料不通过
		function Refuse(uid,name){
			layer.confirm(
				'确认将用户[ '+name+' ]资料设置为不通过吗？',
				{
					btn: ['确认','取消']
				},function(){
					cvphp.post(
						"<?php echo U('Info/refuse');?>",
						{
							uid:uid
						},
						function(data){
							if(data.status!=1){
								layer.msg(data.info);
							}else{
								layer.alert("用户资料处理完成");
								setTimeout(function(){location.reload();},1000);
							}
						}
					);
				}
			);
		}
		//重置资料
		function resetInfo(id){
			$("#resetInfoId").val(id);
			layer.open({
				type: 1,
				shade: false,
				title: false, //不显示标题
				content: $('.resetInfo'),
			});
		}

		$(function(){
			$(".resetInfo-item button").on('click',function(){
				cvphp.submit($(".resetInfo form"),function(data){
					if(data.status!=1){
						layer.msg(data.info);
					}else{
						layer.alert("用户资料处理完成");
						setTimeout(function(){location.reload();},1000);
					}
				});
			});
		});
	</script>
</html>