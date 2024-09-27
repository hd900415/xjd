<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE>
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
	<script type="text/javascript" src="__PUBLIC__/Manage/js/wangEditor/wangEditor.min.js"></script>
	<title>基本设置</title>
	<style type="text/css">
		.layer-anim{
			top:180px !important;
		}
	</style>
</head>

<body>
<div class="nestable">
	<div class="console-title console-title-border drds-detail-title clearfix">
		<h5>基本设置</h5>
	</div>
	<div class="public-selectArea public-selectArea1 margin_10">
		<form action="<?php echo U('Setting/index');?>" method="post">
			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>时区：</dt>
						<dd>
							<input type="text" name="DEFAULT_TIMEZONE" value="<?php echo ((C("DEFAULT_TIMEZONE"))?(C("DEFAULT_TIMEZONE")):''); ?>" />
						</dd>

					</dl>
					<dl>
						<dt></dt><dd>当前时间:<?php echo date('Y-m-d H:i:s');?></dd>
					</dl>
				</div>
			</div>
			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>网站名称：</dt>
						<dd>
							<input type="text" name="siteName" value="<?php echo ((C("siteName"))?(C("siteName")):''); ?>" />
						</dd>
						<em class="tishi">网站名称如   “**贷”  、 “**金融” </em>
					</dl>
				</div>
			</div>

			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>网站标题：</dt>
						<dd>
							<input type="text" name="siteTitle" value="<?php echo ((C("siteTitle"))?(C("siteTitle")):''); ?>" />
						</dd>
					</dl>
				</div>
			</div>

			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>站点关键字：</dt>
						<dd>
							<input type="text" name="siteKeywords" value="<?php echo ((C("siteKeywords"))?(C("siteKeywords")):''); ?>" />
						</dd>
					</dl>
				</div>
			</div>

			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>站点描述：</dt>
						<dd>
							<textarea rows="3" style="height: 100px;" name="siteDescription"><?php echo ((C("siteDescription"))?(C("siteDescription")):''); ?></textarea>
						</dd>
					</dl>
				</div>
			</div>

			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>公司名称：</dt>
						<dd>
							<input type="text" name="siteCorporate" value="<?php echo ((C("siteCorporate"))?(C("siteCorporate")):''); ?>" />
						</dd>
					</dl>
				</div>
			</div>

			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>开启网站访问：</dt>
						<dd>
							<label><input type="radio" name="siteClose" value="0" <?php if(C("siteClose")== 0): ?>checked<?php endif; ?> >是</label>
							<label><input type="radio" name="siteClose" value="1" <?php if(C("siteClose")== 1): ?>checked<?php endif; ?> >否</label>
						</dd>
					</dl>
				</div>
			</div>
			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>官方电话：</dt>
						<dd>
							<input type="text" name="phone_number" value="<?php echo ((C("phone_number"))?(C("phone_number")):''); ?>" />
						</dd>
					</dl>
				</div>
			</div>
			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>whatapp：</dt>
						<dd>
							<input type="text" name="whatapp" value="<?php echo ((C("whatapp"))?(C("whatapp")):''); ?>" />
						</dd>
					</dl>
				</div>
			</div>
			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>客服电话：</dt>
						<dd>
							<input type="text" name="siteServicenum" value="<?php echo ((C("siteServicenum"))?(C("siteServicenum")):''); ?>" />
						</dd>
					</dl>
				</div>
			</div>
			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>客服电话：</dt>
						<dd>
							<input type="text" name="siteServicenum" value="<?php echo ((C("siteServicenum"))?(C("siteServicenum")):''); ?>" />
						</dd>
					</dl>
				</div>
			</div>

			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>扩展项目地址：</dt>
						<dd>
							<input type="text" name="public_url" value="<?php echo ((C("public_url"))?(C("public_url")):''); ?>" />
						</dd>
					</dl>
				</div>
			</div>

			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>允许上传文件类型：</dt>
						<dd>
							<?php $arr = C('fileSuffix');if($arr) $str = implode(',',$arr); ?>
							<input type="text" name="fileSuffix" value="<?php echo (($str)?($str):''); ?>" />
						</dd>
						<em class="tishi">多种文件类型以逗号隔开 如 （jpg,png,gif,jpeg,zip,doc）</em>
					</dl>
				</div>
			</div>

			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>允许上传文件大小：</dt>
						<dd>
							<input type="text" name="fileSize" value="<?php echo ((C("fileSize"))?(C("fileSize")):''); ?>" />
						</dd>
						<em class="tishi">单位 （MB）</em>
					</dl>
				</div>
			</div>

			<div class="clearfix clearfix1">
				<div class="wp_box  col-xs-8">
					<dl>
						<dt>关于我们：</dt>
						<?php $siteAbout = empty(C('siteAbout'))?'':htmlspecialchars_decode(htmlspecialchars_decode(C('siteAbout'))); ?>
						<dd>
							<input type="hidden" name="siteAbout" value="<?php echo (($siteAbout)?($siteAbout):''); ?>" />
							<div class="editor" id="editor"><?php echo (($siteAbout)?($siteAbout):''); ?></div>
						</dd>
					</dl>
				</div>
			</div>
			<input type="hidden" name="siteLogo" value="<?php echo ((C("siteLogo"))?(C("siteLogo")):''); ?>" />
		</form>
		<div class="clearfix clearfix1">
			<div class="wp_box  col-xs-8">
				<dl>
					<dt>网站LOGO：</dt>
					<dd>
						<div class="upimg">
							<form action="<?php echo U('Setting/uploadImg');?>" method="post" enctype="multipart/form-data">
								<input type="file" name="logo" />
								<input type="hidden" name="fileName" value="logo" />
							</form>
						</div>
					</dd>
					<em class="tishi">建议上传PNG背景透明图片</em>
				</dl>
			</div>
		</div>




		<div class="btnArea margin_20">
			<a href="javascript:;" class="btn btn-grayBg">
				<span class="public-label">提交</span>
			</a>
		</div>
	</div>
</div>
</body>
<script type="text/javascript">
    $(function(){
        var E = window.wangEditor;
        var editor = new E('#editor');
        editor.create();
        $(".img-view").remove();
        var logoImg = $("input[name='siteLogo']").val();
        if(logoImg.length > 0){
            $(".upimg").after("<div class='upimg img-view'><img src='" + logoImg + "' /></div>");
        }

        $(".btnArea a").on('click',function(){
            $("input[name='siteAbout']").val(editor.txt.html());
            cvphp.submit($("form"),function(data){
                if(data.status!=1){
                    layer.msg(data.info);
                }else{
                    layer.alert('保存成功');
                }
            });
        });

        $("input[type='file']").on('change',function(){
            var value = $(this).val();
            if(value.length == 0){
                layer.msg("请选择上传文件");
                return false;
            }
            //上传图片
            var obj = $(this).parent();
            var fileinputObj = this;
            cvphp.submit($(obj),function(data){
                if(data.status != 1){
                    layer.msg(data.info);
                    return false;
                }else{
                    var imgUrl = "__PUBLIC__/Upload/" + data.info;
                    $("input[name='siteLogo']").val(imgUrl);
                    $(".img-view").remove();
                    $(".upimg").after("<div class='upimg img-view'><img src='" + imgUrl + "' /></div>");
                }
            });
        });
    });
</script>
</html>