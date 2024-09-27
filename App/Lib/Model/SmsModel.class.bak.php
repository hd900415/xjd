<?php
class SmsModel extends Model
{
	public function makeCode()
	{
		return rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
	}
	public function addInfo($tel, $type, $code, $content, $status)
	{
		$arr = array("telnum" => $tel, "type" => $type, "code" => $code, "content" => $content, "send_time" => time(), "status" => $status, "isuse" => 0);
		return $this->add($arr);
	}
	public function isOften($tel, $type)
	{
		$result = $this->where(array("telnum" => $tel, "type" => $type, "status" => 0))->order("send_time Desc")->find();
		if ($result && time() - $result["send_time"] < 60) {
			return true;
		}
		return false;
	}
	public function getInfo($tel, $type)
	{
		$result = $this->where(array("telnum" => $tel, "type" => $type))->order("send_time Desc")->find();
		if (!$result) {
			return false;
		}
		if ($result["isuse"]) {
			$this->where(array("id" => $result["id"]))->save(array("isuse" => 1));
		}
		return $result;
	}
	public function getInfoMy($tel, $code,$type)
	{
		$result = $this->where(array("telnum" => $tel,"code"=>$code, "type" => $type))->order("send_time Desc")->find();
		if (!$result) {
			return false;
		}
		if ($result["isuse"]) {
			$this->where(array("id" => $result["id"]))->save(array("isuse" => 1));
		}
		return $result;
	}		
	public function sendSms($number, $content)
	{
		/*$status = 0;
		$mess = "未知错误";
		$content = "【" . C("sms_name") . "】" . $content;
		$result = curl("http://www.xauguo.cn/Api/Sms/index/", array("mobile" => $number, "content" => $content, "appkey" => C("ugappkey")), 1);
		if ($result) {
			$arr = json_decode($result, true);
			if ($arr) {
				if ($arr["code"] != 0) {
					$mess = $arr["data"];
				} else {
					$mess = "发送成功";
					$status = 1;
				}
			} else {
				$mess = "解析数据失败";
			}
		} else {
			$mess = "请求失败";
		}*/
		$statusStr = array(
		"0" => "短信发送成功",
		"-1" => "参数不全",
		"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
		"30" => "密码错误",
		"40" => "账号不存在",
		"41" => "余额不足",
		"42" => "帐户已过期",
		"43" => "IP地址限制",
		"50" => "内容含有敏感词"
		);
		$smsapi = "http://api.smsbao.com/";
		$user = C("sms_user"); //短信平台帐号
		$pass = md5(C("sms_password")); //短信平台密码
		//var_dump(C("sms_password"));die;
		$content=$content;//要发送的短信内容
		$phone = $number;//要发送短信的手机号码
		$sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
		$result =file_get_contents($sendurl);
		$status = $result;
		$mess = $statusStr[$result];		
		$arr = array("telnum" => $number, "type" => "", "code" => "0", "content" => $content, "send_time" => time(), "status" => $status, "isuse" => 1);
		$this->add($arr);
		return array("status" => $status, "mess" => $mess);
	}
}