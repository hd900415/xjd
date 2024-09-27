<?php
class PayAction extends CommonAction
{
	public function alipay()
	{
		$order = I("order");
		//获取支付方式
		$paytype = I("paytype");
		if (!$order) {
			$this->error("illegal operation");
		}
		if (!$paytype) {
			$this->error("illegal operation");
		}		
		$payorderModel = D("Payorder");
		$info = $payorderModel->where(array("id" => $order))->find();
		if (!$info) {
			$this->error("order does not exist");
		}
		if ($info["status"] == 1) {
			$this->error("The order has been paid, please do not pay again");
		}
		/*if (isWchat()) {
			$this->display();
			exit(0);
		}*/
		$out_trade_no = $order;
		$total_fee = number_format($info["money"], 2, ".", "");
		//创建支付开始
		$codepay_id="76487";//这里改成码支付ID
		$codepay_key="MZwcywDX7aikdiWKZC3akyp0U1Y4wKzs"; //这是您的通讯密钥

		$data = array(
			"id" => $codepay_id,//你的码支付ID
			"pay_id" => $out_trade_no, //唯一标识 可以是用户ID,用户名,session_id(),订单ID,ip 付款后返回
			"type" => $paytype,//1支付宝支付 3微信支付 2QQ钱包
			"price" => $total_fee,//金额100元
			"param" => "",//自定义参数
			"notify_url"=>"http://" . $_SERVER["HTTP_HOST"] . U("Pay/notify_alipay"),//通知地址
			"return_url"=>"http://" . $_SERVER["HTTP_HOST"] . U("Repay/index"),//跳转地址
		); //构造需要传递的参数

		ksort($data); //重新排序$data数组
		reset($data); //内部指针指向数组中的第一个元素

		$sign = ''; //初始化需要签名的字符为空
		$urls = ''; //初始化URL参数为空

		foreach ($data AS $key => $val) { //遍历需要传递的参数
			if ($val == ''||$key == 'sign') continue; //跳过这些不参数签名
			if ($sign != '') { //后面追加&拼接URL
				$sign .= "&";
				$urls .= "&";
			}
			$sign .= "$key=$val"; //拼接为url参数形式
			$urls .= "$key=" . urlencode($val); //拼接为url参数形式并URL编码参数值
		}
		$query = $urls . '&sign=' . md5($sign .$codepay_key); //创建订单所需的参数
		$url = "http://api2.fateqq.com:52888/creat_order/?{$query}"; //支付页面
		header("Location:{$url}"); //跳转到支付页面	
	}
    public function rd($url,$statu='302'){
        header("HTTP/1.1 ".$statu." Moved Permanently");
        header("Location:".$url);
        die("");
    }
	public function notify_alipay()
	{
		//业务处理开始
		
		ksort($_POST); //排序post参数
		reset($_POST); //内部指针指向数组中的第一个元素
		$codepay_key="MZwcywDX7aikdiWKZC3akyp0U1Y4wKzs"; //这是您的密钥
		$sign = '';//初始化
		foreach ($_POST AS $key => $val) { //遍历POST参数
			if ($val == '' || $key == 'sign') continue; //跳过这些不签名
			if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
			$sign .= "$key=$val"; //拼接为url参数形式
		}
		if (!$_POST['pay_no'] || md5($sign . $codepay_key) != $_POST['sign']) { //不合法的数据
			exit('fail');  //返回失败 继续补单
		} else { //合法的数据
			//业务处理
			$id = $_POST['pay_id']; //需要充值的ID 或订单号 或用户名
			//$money = (float)$_POST['money']; //实际付款金额
			//$price = (float)$_POST['price']; //订单的原价
			//$param = $_POST['param']; //自定义参数
			//$pay_no = $_POST['pay_no']; //流水号
			$payorderModel = D("Payorder");
			$info = $payorderModel->where(array("id" => $id))->find();
			if ($info && !$info["status"]) {
				if ($payorderModel->where(array("id" => $id))->save(array("status" => 1, "pay_time" => time()))) {
					$billList = json_decode($info["billlist"], true);
					if ($billList) {
						$loanbillModel = D("Loanbill");
						$loanorderModel = D("Loanorder");
						$i = 0;
						while ($i < count($billList)) {
							$bill = $loanbillModel->where(array("id" => $billList[$i]))->find();
							if ($bill && $bill["status"] != 2 && $bill["status"] != 3 && $bill["status"] != 4) {
								$tmp = array("status" => 2, "repay_time" => time());
								if ($bill["status"] == 1) {
									$tmp["status"] = 3;
								}
								$loanbillModel->where(array("id" => $billList[$i]))->save($tmp);
								if (!$loanbillModel->where(array("toid" => $bill["toid"], "status" => array("IN", "0,1")))->count()) {
									$loanorderModel->where(array("id" => $bill["toid"]))->save(array("status" => 1));
								}
							}
							$i = $i + 1;
						}
					}
					exit('success'); 
					/*else{
						$vipd = D("user");
						if ($price==588){
							$vipid=1;
						}else if($price==888){
							$vipid=2;
						}else{
							$vipid=0;
						}
						$vipd->where(array("id" => $info['uid']))->save(array("vipid" =>$vipid));
					}*/
				}
			}else{
				exit('success'); 
			}			
			//返回成功 不要删除哦
		}		
	}
	//续期支付
	public function alipayxq()
	{
		$order = I("order");
		//获取支付方式
		$paytype = I("paytype");
		if (!$order) {
			$this->error("illegal operation");
		}
		if (!$paytype) {
			$this->error("illegal operation");
		}		
		$payorderxqModel = D("Payorderxq");
		$info = $payorderxqModel->where(array("id" => $order))->find();
		if (!$info) {
			$this->error("order does not exist");
		}
		if ($info["status"] == 1) {
			$this->error("The order has been paid, please do not pay again");
		}
		/*if (isWchat()) {
			$this->display();
			exit(0);
		}*/
		$out_trade_no = $order;
		$total_fee = number_format($info["money"], 2, ".", "");
		//创建支付开始
		$codepay_id="76487";//这里改成码支付ID
		$codepay_key="MZwcywDX7aikdiWKZC3akyp0U1Y4wKzs"; //这是您的通讯密钥

		$data = array(
			"id" => $codepay_id,//你的码支付ID
			"pay_id" => $out_trade_no, //唯一标识 可以是用户ID,用户名,session_id(),订单ID,ip 付款后返回
			"type" => $paytype,//1支付宝支付 3微信支付 2QQ钱包
			"price" => $total_fee,//金额100元
			"param" => "",//自定义参数
			"notify_url"=>"http://" . $_SERVER["HTTP_HOST"] . U("Pay/notify_alipayxq"),//通知地址
			"return_url"=>"http://" . $_SERVER["HTTP_HOST"] . U("Repay/order"),//跳转地址
		); //构造需要传递的参数

		ksort($data); //重新排序$data数组
		reset($data); //内部指针指向数组中的第一个元素

		$sign = ''; //初始化需要签名的字符为空
		$urls = ''; //初始化URL参数为空

		foreach ($data AS $key => $val) { //遍历需要传递的参数
			if ($val == ''||$key == 'sign') continue; //跳过这些不参数签名
			if ($sign != '') { //后面追加&拼接URL
				$sign .= "&";
				$urls .= "&";
			}
			$sign .= "$key=$val"; //拼接为url参数形式
			$urls .= "$key=" . urlencode($val); //拼接为url参数形式并URL编码参数值
		}
		$query = $urls . '&sign=' . md5($sign .$codepay_key); //创建订单所需的参数
		$url = "http://api2.fateqq.com:52888/creat_order/?{$query}"; //支付页面
		header("Location:{$url}"); //跳转到支付页面	
	}
	
	public function notify_alipayxq()
	{
		//业务处理开始
		
		ksort($_POST); //排序post参数
		reset($_POST); //内部指针指向数组中的第一个元素
		$codepay_key="MZwcywDX7aikdiWKZC3akyp0U1Y4wKzs"; //这是您的密钥
		$sign = '';//初始化
		foreach ($_POST AS $key => $val) { //遍历POST参数
			if ($val == '' || $key == 'sign') continue; //跳过这些不签名
			if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
			$sign .= "$key=$val"; //拼接为url参数形式
		}
		if (!$_POST['pay_no'] || md5($sign . $codepay_key) != $_POST['sign']) { //不合法的数据
			exit('fail');  //返回失败 继续补单
		} else { //合法的数据
			//业务处理
			$id = $_POST['pay_id']; //需要充值的ID 或订单号 或用户名
			//$money = (float)$_POST['money']; //实际付款金额
			//$price = (float)$_POST['price']; //订单的原价
			//$param = $_POST['param']; //自定义参数
			//$pay_no = $_POST['pay_no']; //流水号
			$payorderxqModel = D("Payorderxq");
			$info = $payorderxqModel->where(array("id" => $id))->find();
			if ($info && !$info["status"]) {
				if ($payorderxqModel->where(array("id" => $id))->save(array("status" => 1, "pay_time" => time()))) {
					$billList = json_decode($info["billlist"], true);
					if ($billList) {
						$loanbillModel = D("Loanbill");	
						$loanorderModel = D("Loanorder");						
						$i = 0;
						while ($i < count($billList)) {
							$bill = $loanbillModel->where(array("id" => $billList[$i]))->find();
							if(!$bill['overdue_xq']){
								exit("success");
							}							
							$loanorder = $loanorderModel->where(array("id" => $bill["toid"]))->find();
							$data['overdue'] = 0;
							$data['repayment_time'] = strtotime("+".$loanorder['time']." day");
							$data['status'] = 0;
							$data['overdue_settime'] = 0;
							$data['overdue_xq'] = 0;
							$loanbillModel->where(array("id" => $billList[$i]))->save($data);
							$i = $i + 1;
						}
					}
					exit('success'); 
					/*else{
						$vipd = D("user");
						if ($price==588){
							$vipid=1;
						}else if($price==888){
							$vipid=2;
						}else{
							$vipid=0;
						}
						$vipd->where(array("id" => $info['uid']))->save(array("vipid" =>$vipid));
					}*/
				}
			}else{
				exit('success'); 
			}			
			//返回成功 不要删除哦
		}		
	}	
}