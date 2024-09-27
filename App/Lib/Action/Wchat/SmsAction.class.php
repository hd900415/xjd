<?php
class SmsAction extends CommonAction
{
	public function sendcode()
	{
		$verify = I("verify");
		if (!$verify) {
			$this->error("Please enter the graphic verification code");
		}
		if ($_SESSION["verify"] != md5($verify)) {
			$this->error("Graphic verification code error");
		}
		$telnum = I("user");
		if (!isMobile($telnum)) {
			$this->error("The mobile number is not standardized");
		}
		$type = I("type");
		if (!$type) {
			$this->error("wrong parameter");
		}
		$smsModel = D("Sms");
		if ($smsModel->isOften($telnum, $type)) {
			$this->error("Verification codes are sent frequently, please wait");
		}
		$code = $smsModel->makeCode();
		if (!$code) {
			$this->error("Failed to generate verification code");
		}
		$tpl_cont = htmlspecialchars_decode(htmlspecialchars_decode(C($type . "_code_tpl")));
		//$content = "【" . C("sms_name") . "】" . $tpl_cont;
		$content = $tpl_cont;
		$content = str_replace("<@>", $code, $content);
		$content = str_replace("《@》", $code, $content);
		//$result = curl("http://www.xauguo.cn/Api/Sms/index/", array("mobile" => $telnum, "content" => $content, "appkey" => C("ugappkey")), 1);
		$result = $smsModel->sendSms($telnum,$content);
		
		if ($result['status'] != 0) {
			$this->error("Request failed");
		}
		//$arr = json_decode($result, true);
		$arr = $result;
		if (!$arr) {
			$this->error("Failed to parse data");
		}
		if ($arr["status"] != 0) {
			$mess = $arr["mess"];
			$succ = 0;
		} else {
			$mess = "Sent successfully";
			$succ = 1;
		}
		$smsModel->addInfo($telnum, $type, $code, $content, $arr["mess"]);
		if (!$succ) {
			$this->error($mess);
		}
		$this->success($mess);
	}
}