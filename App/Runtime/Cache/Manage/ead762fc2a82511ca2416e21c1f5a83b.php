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
		<title>自由块列表-汇码网（www.huimaw.com）</title>
	</head>
	<body>
		<div class="nestable">
			<div class="console-title console-title-border drds-detail-title clearfix">
				<h5>自由块列表</h5>
			</div>
			<div class="public-btnArea">
	        	<a href="<?php echo U('Block/add');?>" class="btn btn-grayBg">
	            	<i class="glyphicon glyphicon-plus public-ico"></i>
	                <span class="public-label">添加自由块</span>
	            </a>
        	</div>
			<div class="scroll-bar-table">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>序号</th>
							<th>调用方式</th>
							<th>类型</th>
							<th>备注</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
<?php $type=array('文本','图片','HTML内容'); ?>
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="list-<?php echo ($vo["id"]); ?>">
							<td><?php echo ($vo["id"]); ?></td>
							<td>
								<?php echo htmlspecialchars('<block') . ' name="' . $vo['name'] . htmlspecialchars('" />'); ?>
							</td>
							<td><?php echo ($type[$vo['type']-1]); ?></td>
							<td><?php echo ($vo["remarks"]); ?></td>
							<td class="text-left">
								<a href="<?php echo U('Block/edit',array('id'=>$vo['id']));?>">编辑</a>
								<a href="javascript:delBlock('<?php echo ($vo["id"]); ?>');">删除</a>
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
		function delBlock(id){
			layer.confirm(
				'数据删除后不可恢复,请确认模板中对应调用以修改？',
				{
					btn: ['确认删除','取消']
				},function(){
					cvphp.post(
						"<?php echo U('Block/delBlock');?>",
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