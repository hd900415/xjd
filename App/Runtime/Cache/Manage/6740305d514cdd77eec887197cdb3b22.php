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
		<title>管理员列表</title>
	</head>
	<body>
		<div class="nestable">
			<div class="console-title console-title-border drds-detail-title clearfix">
				<h5>管理员列表</h5>
			</div>
			<div class="public-btnArea">
	        	<a href="<?php echo U('Admin/add');?>" class="btn btn-grayBg">
	            	<i class="glyphicon glyphicon-plus public-ico"></i>
	                <span class="public-label">添加管理员</span>
	            </a>
        	</div>
			<div class="scroll-bar-table">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>序号</th>
							<th>用户名</th>
							<th>最后登录时间</th>
							<th>最后登录IP</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
<?php $status = array('禁用','启用'); ?>
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="list-<?php echo ($vo["id"]); ?>">
							<td><?php echo ($vo["id"]); ?></td>
							<td><?php echo ($vo["username"]); ?></td>
							<td><?php echo (date("Y-m-d H:i:s",$vo["last_time"])); ?></td>
							<td><?php echo ($vo["last_ip"]); ?></td>
							<td><?php echo ($status[$vo['status']]); ?></td>
							<td class="text-left">
								<a href="<?php echo U('Admin/edit',array('id'=>$vo['id']));?>">编辑</a>
								<a href="javascript:delAdmin('<?php echo ($vo["id"]); ?>','<?php echo ($vo["username"]); ?>');">删除</a>
								<a href="<?php echo U('Admin/view',array('id'=>$vo['id']));?>">查看</a>
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
		function delAdmin(id,name){
			layer.confirm(
				'确定要删除管理员:'+name+'吗？',
				{
					btn: ['确认删除','取消']
				},function(){
					cvphp.post(
						"<?php echo U('Admin/del');?>",
						{
							id:id
						},
						function(data){
							if(data.status!=1){
								layer.msg(data.info);
							}else{
								$("#list-"+id).remove();
								layer.msg("操作成功");
							}
						}
					);
				}
			);
		}
	</script>
</html>