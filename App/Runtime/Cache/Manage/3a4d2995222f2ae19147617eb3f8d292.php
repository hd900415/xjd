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
		<title>常见问题</title>
		<style>
			.redText{
				color: red;font-size: 20px;
			}
		</style>
	</head>
	<body>
		<div class="nestable">
			<div class="console-title console-title-border drds-detail-title clearfix">
				<h5>常见问题</h5>
			</div>
			<div class="public-btnArea">
	        	<a href="javascript:$('.alert').show();" class="btn btn-grayBg">
	            	<i class="glyphicon glyphicon-plus public-ico"></i>
	                <span class="public-label">添加常见问题</span>
	            </a>
        	</div>
			<div class="scroll-bar-table">
				<table class="table table-hover">
					<thead>
						<tr>
							<th width="8%">序号</th>
							<th>标题</th>
							<th width="8%">排序</th>
							<th width="20%">操作</th>
						</tr>
					</thead>
					<tbody>
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="list-<?php echo ($vo["id"]); ?>">
							<td align="center"><?php echo ($vo["id"]); ?></td>
							<td><?php echo ($vo["title"]); ?></td>
							<td><?php echo ($vo["sort"]); ?></td>
							<td class="text-left">
								<a href="javascript:edit('<?php echo ($vo["id"]); ?>');" target="_blank">编辑</a>
								<a href="javascript:del('<?php echo ($vo["id"]); ?>','<?php echo ($vo["title"]); ?>');">删除</a>
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
	    <div class="alert" style="display: none;">
	    	<div class="win">
				<form action="<?php echo U('Help/add');?>" method="post">
		        	<ul>
		            	<li><label>标题：</label><input type="text" name="title"></li>
		            	<li><label>排序：</label><input type="text" name="sort"></li>
		                <li>
		                	<label>内容：</label>
		                    <textarea class="alert_con" name="content" id="content"></textarea>
		                </li>
		                <li class="anniu">
		                	<a href="javascript:$('.alert').hide();" class="but">取消</a>
		                    <a href="javascript:formSubmit();" class="but" style="background-color: #007EE5;color: #fff;">提交</a>
		                </li>
		            </ul>
				</form>
	        </div>
	    </div>
	</body>
	<script>
		function formSubmit(){
			cvphp.submit($("form"),function(data){
				if(data.status!=1){
					layer.msg(data.info);
				}else{
					$(".alert").hide();
					layer.alert('操作成功');
					location.reload();
				}
			});
		}
		function edit(id){
			//获取问题详情
			cvphp.get(
				"<?php echo U('Help/view');?>",
				{
					id:id
				},
				function(data){
					if(data.status!=1){
						layer.msg(data.info);
					}else{
						var data = data.info;
						$(".alert input[name='title']").val(data.title);
						$(".alert input[name='sort']").val(data.sort);
						$("#content").val(data.content);
						$(".alert form").attr('action',data.editurl);
						$(".alert").show();
					}
				}
			);
		}
		function del(id,title){
			layer.confirm(
				'数据删除后不可恢复,请确认？',
				{
					btn: ['确认删除','取消']
				},function(){
					cvphp.post(
						"<?php echo U('Help/del');?>",
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