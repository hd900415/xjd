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
		<title>API设置</title>
		<style type="text/css">
			.layer-anim{
				top:180px !important;
			}
		</style>
	</head>

	<body>
		<div class="nestable">
			<div class="console-title console-title-border drds-detail-title clearfix">
				<h5>API设置</h5>
			</div>
			<div class="public-selectArea public-selectArea1 margin_10">
				<form action="<?php echo U('Setting/api');?>" method="post">
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>上游商户ID：</dt>
								<dd>
									<input type="text" name="MERCHANT_ID" value="<?php echo ((C("MERCHANT_ID"))?(C("MERCHANT_ID")):'8001060'); ?>" />
								</dd>
								<!--<em class="tishi">调用接口数据账户分配的appkey，若无请至<a href="http://www.xauguo.cn" target="_blank">优果数据</a>注册申请</em>-->
							</dl>
						</div>
					</div>
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>上游支付名称：</dt>
								<dd>
									<input type="text" name="MERCHANT_NAME" value="<?php echo ((C("MERCHANT_NAME"))?(C("MERCHANT_NAME")):'GFpay'); ?>" />
								</dd>
								<!--<em class="tishi">调用接口数据账户分配的appkey，若无请至<a href="http://www.xauguo.cn" target="_blank">优果数据</a>注册申请</em>-->
							</dl>
						</div>
					</div>
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>商户密钥：</dt>
								<dd>
									<input type="text" name="MERCHANT_SECRET" value="<?php echo ((C("MERCHANT_SECRET"))?(C("MERCHANT_SECRET")):'RRIG1RKZo8AG6JAReKgcywig22DGjDAR'); ?>" />
								</dd>
								<!--<em class="tishi">调用接口数据账户分配的appkey，若无请至<a href="http://www.xauguo.cn" target="_blank">优果数据</a>注册申请</em>-->
							</dl>
						</div>
					</div>
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>收款URL：</dt>
								<dd>
									<input type="text" name="MERCHANT_PAY_URL" value="<?php echo ((C("MERCHANT_PAY_URL"))?(C("MERCHANT_PAY_URL")):'https://gfpay199.com/submit.php'); ?>" />
								</dd>
								<!--<em class="tishi">调用接口数据账户分配的appkey，若无请至<a href="http://www.xauguo.cn" target="_blank">优果数据</a>注册申请</em>-->
							</dl>
						</div>
					</div>
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>代付URL：</dt>
								<dd>
									<input type="text" name="MERCHANT_DAILIPAY_URL" value="<?php echo ((C("MERCHANT_DAILIPAY_URL"))?(C("MERCHANT_DAILIPAY_URL")):'https://gfpay199.com/submitpay.php'); ?>" />
								</dd>
								<!--<em class="tishi">调用接口数据账户分配的appkey，若无请至<a href="http://www.xauguo.cn" target="_blank">优果数据</a>注册申请</em>-->
							</dl>
						</div>
					</div>
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>帐户：</dt>
								<dd>
									<input type="text" name="sms_user" value="<?php echo ((C("sms_user"))?(C("sms_user")):''); ?>" />
								</dd>
								<!--<em class="tishi">调用接口数据账户分配的appkey，若无请至<a href="http://www.xauguo.cn" target="_blank">优果数据</a>注册申请</em>-->
							</dl>
						</div>
					</div>
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>接口密码：</dt>
								<dd>
									<input type="text" name="sms_password" value="<?php echo ((C("sms_password"))?(C("sms_password")):''); ?>" />
								</dd>
								<!--<em class="tishi">调用接口数据账户分配的appkey，若无请至<a href="http://www.xauguo.cn" target="_blank">优果数据</a>注册申请</em>-->
							</dl>
						</div>
					</div>					
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>接入码:</dt>
								<dd>
									<input type="text" name="sms_extno" value="<?php echo ((C("sms_extno"))?(C("sms_extno")):''); ?>" />
								</dd>
								<!--<em class="tishi">调用接口数据账户分配的appkey，若无请至<a href="http://www.xauguo.cn" target="_blank">优果数据</a>注册申请</em>-->
							</dl>
						</div>
					</div>		
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>短信签名：</dt>
								<dd>
									<input type="text" name="sms_name" value="<?php echo ((C("sms_name"))?(C("sms_name")):''); ?>" />
								</dd>
								<em class="tishi">签名以汉字组成，常常代表网站名称或机构名称</em>
							</dl>
						</div>
					</div>
	
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>支付宝商户号：</dt>
								<dd>
									<input type="text" name="alipayPartner" value="<?php echo ((C("alipayPartner"))?(C("alipayPartner")):''); ?>" />
								</dd>
							</dl>
						</div>
					</div>
	
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>支付宝密钥：</dt>
								<dd>
									<input type="text" name="alipayKey" value="<?php echo ((C("alipayKey"))?(C("alipayKey")):''); ?>" />
								</dd>
							</dl>
						</div>
					</div>
	
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>用户注册短信模板：</dt>
								<dd>
									<textarea rows="3" style="height: 60px;width: 325px;" name="reg_code_tpl"><?php echo ((htmlspecialchars_decode(C("reg_code_tpl")))?(htmlspecialchars_decode(C("reg_code_tpl"))):''); ?></textarea>
								</dd>
								<em class="tishi">使用（<@>）代替验证码</em>
							</dl>
						</div>
					</div>
	
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>找回密码短信模板：</dt>
								<dd>
									<textarea rows="3" style="height: 60px;width: 325px;" name="find_code_tpl"><?php echo ((htmlspecialchars_decode(C("find_code_tpl")))?(htmlspecialchars_decode(C("find_code_tpl"))):''); ?></textarea>
								</dd>
								<em class="tishi">使用（<@>）代替验证码</em>
							</dl>
						</div>
					</div>
					
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>资料通过短信模板：</dt>
								<dd>
									<textarea rows="3" style="height: 60px;width: 325px;" name="info_adopt"><?php echo ((htmlspecialchars_decode(C("info_adopt")))?(htmlspecialchars_decode(C("info_adopt"))):''); ?></textarea>
								</dd>
								<em class="tishi">网站名称使用（<@sitename@>）代替，信用额度使用（<@quota@>）代替</em>
							</dl>
						</div>
					</div>

					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>资料驳回短信模板：</dt>
								<dd>
									<textarea rows="3" style="height: 60px;width: 325px;" name="info_refuse"><?php echo ((htmlspecialchars_decode(C("info_refuse")))?(htmlspecialchars_decode(C("info_refuse"))):''); ?></textarea>
								</dd>
								<em class="tishi">网站名称使用（<@sitename@>）代替</em>
							</dl>
						</div>
					</div>

					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>资料重置短信模板：</dt>
								<dd>
									<textarea rows="3" style="height: 60px;width: 325px;" name="info_reset"><?php echo ((htmlspecialchars_decode(C("info_reset")))?(htmlspecialchars_decode(C("info_reset"))):''); ?></textarea>
								</dd>
								<em class="tishi">网站名称使用（<@sitename@>）代替</em>
							</dl>
						</div>
					</div>

					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>借款提交短信模板：</dt>
								<dd>
									<textarea rows="3" style="height: 60px;width: 325px;" name="loan_submit"><?php echo ((htmlspecialchars_decode(C("loan_submit")))?(htmlspecialchars_decode(C("loan_submit"))):''); ?></textarea>
								</dd>
								<em class="tishi">借款订单号使用（<@>）代替</em>
							</dl>
						</div>
					</div>
					
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>借款通过短信模板：</dt>
								<dd>
									<textarea rows="3" style="height: 60px;width: 325px;" name="loan_adopt"><?php echo ((htmlspecialchars_decode(C("loan_adopt")))?(htmlspecialchars_decode(C("loan_adopt"))):''); ?></textarea>
								</dd>
								<em class="tishi">借款订单号使用（<@>）代替</em>
							</dl>
						</div>
					</div>
					
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>借款驳回短信模板：</dt>
								<dd>
									<textarea rows="3" style="height: 60px;width: 325px;" name="loan_refuse"><?php echo ((htmlspecialchars_decode(C("loan_refuse")))?(htmlspecialchars_decode(C("loan_refuse"))):''); ?></textarea>
								</dd>
								<em class="tishi">借款订单号使用（<@>）代替，网站名称使用（<@sitename@>）代替</em>
							</dl>
						</div>
					</div>
					
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>账单提醒短信模板：</dt>
								<dd>
									<textarea rows="3" style="height: 60px;width: 325px;" name="bill_remind"><?php echo ((htmlspecialchars_decode(C("bill_remind")))?(htmlspecialchars_decode(C("bill_remind"))):''); ?></textarea>
								</dd>
								<em class="tishi">借款订单号使用（<@>）代替，账单期数使用（<@num@>）代替，设置后短信将在用户账单到期前三天内发送一次</em>
							</dl>
						</div>
					</div>
					
					<div class="clearfix clearfix1">
						<div class="wp_box  col-xs-8">
							<dl>
								<dt>账单逾期短信模板：</dt>
								<dd>
									<textarea rows="3" style="height: 60px;width: 325px;" name="bill_overdue"><?php echo ((htmlspecialchars_decode(C("bill_overdue")))?(htmlspecialchars_decode(C("bill_overdue"))):''); ?></textarea>
								</dd>
								<em class="tishi">借款订单号使用（<@>）代替，账单期数使用（<@num@>）代替，设置后短信将在用户账单到期后发送一次</em>
							</dl>
						</div>
					</div>
					
				</form>
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
			$(".btnArea a").on('click',function(){
				cvphp.submit($("form"),function(data){
					if(data.status!=1){
						layer.msg(data.info);
					}else{
						layer.alert('保存成功');
					}
				});
			});
		});
	</script>
</html>