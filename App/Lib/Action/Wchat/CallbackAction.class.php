<?php
class CallbackAction extends CommonAction
{
	public function mobileAuthCallback()
	{
		$data = htmlspecialchars_decode(I("post.data"));
		$arr = json_decode($data, true);
		if (!$arr) {
			exit("feid");
		}
		if (!$arr["data"]["callid"]) {
			exit("feid");
		}
		$infoauthModel = D("Infoauth");
		$infoauthModel->setAuthData($arr["data"]["callid"], $data);
		$callid = $arr["data"]["callid"];
		$AuthInfo = $infoauthModel->getData($callid, "mobile");
		if (!$AuthInfo) {
			exit("feid");
		}
		$infoModel = D("Info");
		if (!$infoModel->setMobile($AuthInfo["uid"], $callid)) {
			exit("feid");
		}
		exit("success");
	}
	public function taobaoAuthCallback()
	{
		$data = htmlspecialchars_decode(I("post.data"));
		$arr = json_decode($data, true);
		if (!$arr) {
			exit("feid");
		}
		if (!$arr["data"]["callid"]) {
			exit("feid");
		}
		$infoauthModel = D("Infoauth");
		$infoauthModel->setAuthData($arr["data"]["callid"], $data);
		$callid = $arr["data"]["callid"];
		$AuthInfo = $infoauthModel->getData($callid, "taobao");
		if (!$AuthInfo) {
			exit("feid");
		}
		$infoModel = D("Info");
		if (!$infoModel->setTaobao($AuthInfo["uid"], $callid)) {
			exit("feid");
		}
		exit("success");
	}
}