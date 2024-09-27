<?php
class InfoAction extends CommonAction
{
	//立木征信APPKEY
	private $appkey = "";//这里填写APPKEY
	private	$version = "1.2.0";
	public function check()
	{
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		$uid = $userInfo["id"];
		if (!$infoModel->hasSetIdentity($uid)) {
			$this->redirect("Info/identityAuth");
		}
		if (!$infoModel->hasSetContacts($uid)) {
			$this->redirect("Info/contactsAuth");
		}
		if (!$infoModel->hasSetBank($uid)) {
			$this->redirect("Info/bankAuth");
		}
		if (!$infoModel->hasSetAddess($uid)) {
			$this->redirect("Info/addessAuth");
		}
		if (!$infoModel->hasSetMobile($uid)) {
			$this->redirect("Info/mobileAuth");
		}
		if (!$infoModel->hasSetTaobao($uid)) {
			$this->redirect("Info/taobaoAuth");
		}
		if ($infoModel->getStatus($uid) == 0) {			
			$infoModel->setStatus($userInfo["id"], 1);
		}
		$this->redirect("Index/index");
	}
	public function uploadImg()
	{
		if ($this->isPost()) {
			$fileName = I("fileName");
			if (!$fileName) {
				$this->error("Incorrect submission parameters");
			}
			$fileModel = D("File");
			$File = $fileModel->getFile($fileName);
			if (!$File) {
				$this->error("file upload error");
			}
			if (!$File["status"]) {
				$this->error($File["error"]);
			}
			$this->success($File["url"]);
		}
		$this->error("illegal operation");
	}
    public function ajaxUpload()
    {
        if ($this->isPost()) {
            $fileName = I("fileName");
            if (!$fileName) {
                $this->ajaxReturn(array(
                    "status"=>300,
                    "message" =>'Incorrect submission parameters',
                    "data"=>""
                ));
            }
            $fileModel = D("File");
            $File = $fileModel->getFile($fileName);
            if (!$File) {
                $this->ajaxReturn(array(
                    "status"=>300,
                    "message" =>'file upload error',
                    "data"=>""
                ));
            }
            if (!$File["status"]) {
                $this->ajaxReturn(array(
                    "status"=>500,
                    "message" =>$File["error"],
                    "data"=>""
                ));
            }
            $baseUrl=$this->getWebUrl();
            $this->ajaxReturn(array(
                "status"=>200,
                "message" =>"SUCCEED",
                "data"=>$baseUrl."/Public/Upload/".$File["url"]
            ));
        }
        $this->ajaxReturn(array(
            "status"=>405,
            "message" =>'Unsupported request Method',
            "data"=>""
        ));
    }
	public function before()
	{
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		$uid = $userInfo["id"];
		$infoModel->checkInfo($uid);
		$userAuth = $infoModel->getAuthInfo($uid);
		$this->assign("auth", $userAuth);
	}

	public function identityAuth()
	{
		$this->before();
		$userInfo = $this->isLogin();
		$infoModel = D("Info");
		if ($infoModel->hasSetIdentity($userInfo["id"])) {
			$this->error("Identity information submitted", U("Info/check"));
		}
		if ($this->isPost()) {
			$frontImg = I("front");
			$backImg = I("back");
			$personImg = I("person");
			if (!$frontImg) {
				$this->error("Please upload a front photo of your ID card");
			}
			if (!$backImg) {
				$this->error("Please upload a photo of the reverse side of your ID card");
			}
//			if (!$personImg) {
//				$this->error("Please bring your ID card");
//			}
			$frontSuffix = substr(strrchr($frontImg, "."), 1);
			$backSuffix = substr(strrchr($backImg, "."), 1);
			$personSuffix = substr(strrchr($personImg, "."), 1);
			$suffix = array("jpg", "png", "jpeg", "gif");
			if (!in_array($frontSuffix, $suffix)) {
				$this->error("The photo on the front of the ID card is of the wrong type");
			}
			if (!in_array($backSuffix, $suffix)) {
				$this->error("The photo type on the back of the ID card is wrong");
			}
			if (!in_array($personSuffix, $suffix)) {
				$this->error("Wrong type of photo holding ID card");
			}			
			$name = I("realName");
			$idcard = strtoupper(I("idCard"));
			if (!$name || !isChineseName($name)) {
				$this->error("please enter your real name");
			}
			if (!$idcard || !isIdCard($idcard)) {
				$this->error("Please enter the standard ID number");
			}


			// 去除立木征信第三方
            /**
			$zhengModel = D('Zheng');
			$result = $zhengModel->ihttpPost(array("method"=>"api.identity.idcheck","apiKey"=>$this->appkey,"version"=>$this->version,"name" => $name, "identityNo" => $idcard));
			
			if (!$result) {
				$this->error("Request failed");
			}
			$arr = json_decode($result, true);

			if (!$arr) {
				$this->error("Failed to parse data");
			}
			if ($arr["code"] != '0000') {
				$this->error($arr["msg"]);
			}
			$arr = $arr["data"];
			//if ($arr["status"] == 1) {
			//	$this->error($arr["data"]["msg"]);
			//}
			if ($arr["resultCode"] == "R002") {
				$this->error("Identity authentication information does not match");
			}
			if ($arr["resultCode"] == "R003") {
				$this->error("ID number does not exist");
			}
			**/
			$result = $infoModel->setIdentity($userInfo["id"], array("name" => $name, "idcard" => $idcard, "frontimg" => $frontImg, "backimg" => $backImg,"personimg"=>$personImg));
			if (!$result) {
				$this->error("Failed to save information");
			}
			$this->success("Successfully saved", U("Index/more"));
		}
		$this->display();
	}
    public function identity()
    {
        $this->before();
        $userInfo = $this->isLogin();
        $infoModel = D("Info");
//        if ($infoModel->hasSetIdentity($userInfo["id"])) {
//            $this->error("Identity information submitted", U("Info/check"));
//        }
        if ($this->isPost()) {
            $frontImg = I("front");
            $backImg = I("back");
            $personImg = I("person");
            if (!$frontImg) {
                $this->error("Please upload a front photo of your ID card");
            }
            if (!$backImg) {
                $this->error("Please upload a photo of the reverse side of your ID card");
            }
//            if (!$personImg) {
//                $this->error("Please bring your ID card");
//            }
            $frontSuffix = substr(strrchr($frontImg, "."), 1);
            $backSuffix = substr(strrchr($backImg, "."), 1);
            $personSuffix = substr(strrchr($personImg, "."), 1);
            $suffix = array("jpg", "png", "jpeg", "gif");
            if (!in_array($frontSuffix, $suffix)) {
                $this->error("The photo on the front of the ID card is of the wrong type");
            }
            if (!in_array($backSuffix, $suffix)) {
                $this->error("The photo type on the back of the ID card is wrong");
            }
//            if (!in_array($personSuffix, $suffix)) {
//                $this->error("Wrong type of photo holding ID card");
//            }
            $name = I("realName");
            $idcard = strtoupper(I("idCard"));
            if (!$name || !isChineseName($name)) {
                $this->error("please enter your real name");
            }
            if (!$idcard || !isIdCard($idcard)) {
                $this->error("Please enter the standard ID number");
            }
            $result = $infoModel->setIdentity($userInfo["id"], array("name" => $name, "idcard" => $idcard, "frontimg" => $frontImg, "backimg" => $backImg,"personimg"=>$personImg));
            if (!$result) {
                $this->error("Failed to save information");
            }
            $this->success("Successfully saved", U("Index/more"));
        }
        $this->display();
    }
	public function contactsAuth()
	{
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if (!$infoModel->hasSetIdentity($userInfo["id"])) {
			$this->redirect("Info/identityAuth");
		}
		if ($infoModel->hasSetContacts($userInfo["id"])) {
			$this->error("Contact information submitted", U("Info/check"));
		}
		if ($this->isPost()) {
			$zhishuRelation = I("zhishuRelation");
			$zhishuName = I("zhishuName");
			$zhishuPhone = I("zhishuPhone");
			$jinjiRelation = I("jinjiRelation");
			$jinjiName = I("jinjiName");
			$jinjiPhone = I("jinjiPhone");
			if (!$zhishuRelation || !$jinjiRelation) {
				$this->error("Please select a contact relationship");
			}
			if (!$zhishuName || !isChineseName($zhishuName)) {
				$this->error("please enter your real name");
			}
			if (!$zhishuPhone || !isMobile($zhishuPhone)) {
				$this->error("Please enter a valid mobile phone number");
			}
			if (!$jinjiRelation || !$jinjiRelation) {
				$this->error("Please select a contact relationship");
			}
			if (!$jinjiName || !isChineseName($jinjiName)) {
				$this->error("please enter your real name");
			}
			if (!$jinjiPhone || !isMobile($jinjiPhone)) {
				$this->error("Please enter a valid mobile phone number");
			}
			$data = array("zhishuRelation" => $zhishuRelation, "zhishuName" => $zhishuName, "zhishuPhone" => $zhishuPhone, "jinjiRelation" => $jinjiRelation, "jinjiName" => $jinjiName, "jinjiPhone" => $jinjiPhone);
			$result = $infoModel->setContacts($userInfo["id"], $data);
			if (!$result) {
				$this->error("Failed to save information");
			}
			$this->success("Successfully saved", U("Info/check"));
		}
		$this->display();
	}
    public function bankcard()
    {
        $this->before();
        $infoModel = D("Info");
        $userInfo = $this->isLogin();
//        if (!$infoModel->hasSetContacts($userInfo["id"])) {
//            $this->redirect("Info/contactsAuth");
//        }
//        if ($infoModel->hasSetBank($userInfo["id"])) {
//            $this->error("Bank card information has been submitted", U("Info/check"));
//        }
        if ($this->isPost()) {
            $bankNum = I("bankNum");
            $bankPhone = I("bankPhone");
            $bankName = I("bankName");
            if (!$bankName) {
                $this->error("Please enter the bank");
            }
            if (!$bankNum) {
                $this->error("Please enter the bank");
            }
            if (!$bankPhone) {
                $this->error("Please enter reserved mobile number");
            }
//            if (!isMobile($bankPhone)) {
//                $this->error("Please enter a valid mobile phone number");
//            }
            $idcard = json_decode($infoModel->getAuthInfo($userInfo["id"], "identity"), true);
            if (!$idcard) {
                $this->error("Failed to recall data");
            }
            $realname = $idcard["name"];
            $cardno = $idcard["idcard"];
            $result = $infoModel->setBank($userInfo["id"], array("bankName" => $bankName, "bankNum" => $bankNum, "bankPhone" => $bankPhone));
            if (!$result) {
                $this->error("Failed to save information");
            }
            $this->success("Successfully saved", U("Info/check"));
        }
        $this->display();
    }
	public function bankAuth()
	{
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if (!$infoModel->hasSetContacts($userInfo["id"])) {
			$this->redirect("Info/contactsAuth");
		}
		if ($infoModel->hasSetBank($userInfo["id"])) {
			$this->error("Bank card information has been submitted", U("Info/check"));
		}
		if ($this->isPost()) {
			$bankNum = I("bankNum");
			$bankPhone = I("bankPhone");
			$bankName = I("bankName");
			if (!$bankName) {
				$this->error("Please enter the bank");
			}			
			if (!$bankNum) {
				$this->error("Please enter the bank");
			}
			if (!$bankPhone) {
				$this->error("Please enter reserved mobile number");
			}
			if (!isMobile($bankPhone)) {
				$this->error("Please enter a valid mobile phone number");
			}
			$idcard = json_decode($infoModel->getAuthInfo($userInfo["id"], "identity"), true);
			if (!$idcard) {
				$this->error("Failed to recall data");
			}
			$realname = $idcard["name"];
			$cardno = $idcard["idcard"];

			// 去除立木征信第三方
            /**
			$zhengModel = D('Zheng');
			//$result = curl("http://www.xauguo.cn/Api/Bank/index/", array("realname" => $realname, "cardno" => $cardno, "bankcard" => $bankNum, "mobile" => $bankPhone, "appkey" => C("ugappkey")), 1);
			$result = $zhengModel->ihttpPost(array("method"=>"api.identity.bankcard4check","apiKey"=>$this->appkey,"version"=>$this->version,"bankCardNo"=>$bankNum,"mobileNo"=>$bankPhone,"name" => $realname, "identityNo" => $cardno));
			//var_dump($result);die;
			$arr = json_decode($result, true);
			if (!$arr) {
				$this->error("Failed to parse data");
			}
			if ($arr["code"] != '0000') {
				$this->error($arr["msg"]);
			}

			//if ($arr["data"]["status"] == 1) {
			//	$this->error($arr["data"]["msg"]);
			//}

			$arr = $arr["data"];
			if ($arr["resultCode"] == "R002") {
				$this->error("Bank card information does not match");
			}
			if ($arr["resultCode"] == "R003") {
				$this->error("Bank card does not exist");
			}
			**/

			//$result = $arr["data"]["result"];
			//$bankName = $result["information"]["bankname"];



			$result = $infoModel->setBank($userInfo["id"], array("bankName" => $bankName, "bankNum" => $bankNum, "bankPhone" => $bankPhone));
			if (!$result) {
				$this->error("Failed to save information");
			}
			$this->success("Successfully saved", U("Info/check"));
		}
		$this->display();
	}
	public function addessAuth()
	{
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if (!$infoModel->hasSetBank($userInfo["id"])) {
			$this->redirect("Info/bankAuth");
		}
		if ($infoModel->hasSetAddess($userInfo["id"])) {
			$this->error("Contact information submitted", U("Info/check"));
		}
		if ($this->isPost()) {
			$marriage = I("marriage");
			$education = I("education");
			$industry = I("industry");
			$addess = I("addess");
			$addessMore = I("addessMore");
			if (!$marriage) {
				$this->error("Please select marital status");
			}
			if (!$education) {
				$this->error("Please select highest degree");
			}
			if (!$industry) {
				$this->error("Please enter your industry");
			}
			if (!$addess) {
				$this->error("Please select your city of residence");
			}
			if (!$addessMore) {
				$this->error("Please enter residential address");
			}
			$result = $infoModel->setAddess($userInfo["id"], array("marriage" => $marriage, "education" => $education, "industry" => $industry, "addess" => $addess, "addessMore" => $addessMore));
			if (!$result) {
				$this->error("Failed to save information");
			}
			$this->success("Successfully saved", U("Info/check"));
		}
		$this->display();
	}
	
	public function next(){
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();		
		if($this->isPost()){
			$name = I('name');
			if($name == 'mobile' || $name == 'taobao'){
				//$infoModel = D("Info");
				if($name == 'mobile'){
					if (!$infoModel->setMobile($userInfo["id"], "next")) {
						$this->error('Failed to obtain authorization');
					}else{
						$this->success('Obtain authorization successfully');
					}
				}
				if($name == 'taobao'){
					if (!$infoModel->setTaobao($userInfo["id"], "next")) {
						$this->error('Failed to obtain authorization');
					}else{
						$this->success('Obtain authorization successfully');
					}	
				}
			}else{
				$this->error("Parameter error");
			}
		}
	} 	
	public function mobileAuth()
	{
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if (!$infoModel->hasSetAddess($userInfo["id"])) {
			$this->redirect("Info/addessAuth");
		}
		if ($infoModel->hasSetMobile($userInfo["id"])) {
			$this->error("Carrier data submitted", U("Info/check"));
		}
		if ($this->isPost()) {
			//$mobile = I("mobile");
			//$fwpass = I("fwpass");
			//$verifycode = I("verifycode");
			$token = I("token");
			/*if (!$mobile) {
				$this->error("Please enter phone number");
			}			
			if (!$fwpass) {
				$this->error("Please enter service password");
			}
			if (!$verifycode) {
				$this->error("Please enter the reserved verification code");
			}
			if (!isMobile($mobile)) {
				$this->error("Please enter a valid mobile phone number");
			}*/
			if(!$token){
				$this->error("Please get the verification code first");
			}			
			$zhengModel = D('Zheng');
			
			
			/*$result = $zhengModel->ihttpPost(array("method"=>"api.common.getStatus","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "bizType" => 'mobile'));
			$arr = json_decode($result, true);
			if($arr['code'] == '0006'){
				$result = $zhengModel->ihttpPost(array("method"=>"api.common.input","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "input" => $verifycode));
				do{
					$result = $zhengModel->ihttpPost(array("method"=>"api.common.getResult","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "bizType" => 'mobile'));
					$arr = json_decode($result, true);
					if($arr['code'] == '0000'){
						break;
					}
				}while(true);
			}*/
			$result = $zhengModel->ihttpPost(array("method"=>"api.common.getResult","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "bizType" => 'mobile'));
			$result1 = $zhengModel->ihttpPost(array("method"=>"api.common.getReport","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "bizType" => 'mobile'));
			/*do{
				$result = $zhengModel->ihttpPost(array("method"=>"api.common.getStatus","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "bizType" => 'mobile'));
				$arr = json_decode($result, true);
				if($arr["code"] == '0010'){
					$result = $zhengModel->ihttpPost(array("method"=>"api.common.getResult","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "bizType" => 'mobile'));
					$arr = json_decode($result, true);					
					break;
				}
			}while(true);*/
			//$result = $zhengModel->ihttpPost(array("method"=>"api.common.getResult","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "bizType" => 'mobile'));
			$arr1 = json_decode($result, true);
			$arr2 = json_decode($result1, true);
			
			$data = json_encode(array_merge($arr1['data'],$arr2['data']));
			//var_dump($data);die;
			$infoModel = D("Info");
			if (!$infoModel->setMobile($userInfo["id"], $data)) {
				$this->error('Failed to obtain authorization');
			}else{
				$this->success('Obtain authorization successfully');
			}
			//$this->ajaxReturn($arr,'JSON');
			/*var_dump($result);die;
			$result = $infoModel->setBank($userInfo["id"], array("bankName" => $bankName, "bankNum" => $bankNum, "bankPhone" => $bankPhone));
			if (!$result) {
				$this->error("Failed to save information");
			}
			$this->success("Successfully saved", U("Info/check"));*/
		}
		$this->display();
		exit(0);
	}
	public function mobile(){
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if ($this->isPost()) {
			$mobile = I("mobile");
			$fwpass = I("fwpass");			
			if (!$mobile) {
				$this->error("Please enter phone number");
			}			
			if (!$fwpass) {
				$this->error("Please enter service password");
			}
			if (!isMobile($mobile)) {
				$this->error("Please enter a valid mobile phone number");
			}
			$idcard = json_decode($infoModel->getAuthInfo($userInfo["id"], "identity"), true);
			if (!$idcard) {
				$this->error("Failed to recall data");
			}
			$realname = $idcard["name"];
			$cardno = $idcard["idcard"];
            $infoModel = D("Info");
            if($infoModel->setMobile($userInfo["id"], $mobile)){
                $this->success("Authorization succeeded");
            }else{
                $this->error("Authorization failed");
            }
			// 去除请求
			//$callbackurl = "http://" . $_SERVER["SERVER_NAME"] . U("Callback/mobileAuthCallback");			
//			$zhengModel = D('Zheng');
//			$result = $zhengModel->ihttpPost(array("method"=>"api.mobile.get","apiKey"=>$this->appkey,"version"=>$this->version,"isReport"=>"1","identityCardNo"=>$cardno,"identityName"=>$realname,"contentType"=>"busi;net","username" => $mobile, "password" => base64_encode($fwpass)));
//			$arr = json_decode($result, true);
//			$this->ajaxReturn($arr,'JSON');
		}		
	}
	public function status(){
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if ($this->isPost()) {
			$token = I("token");
			$bizType = "mobile";						
			$zhengModel = D('Zheng');
			$result = $zhengModel->ihttpPost(array("method"=>"api.common.getStatus","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "bizType" => $bizType));
			$arr = json_decode($result, true);
			$this->ajaxReturn($arr,'JSON');
		}		
	}
	public function sendVerify(){
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if ($this->isPost()) {
			$token = I("token");
			$verifycode = I("verifycode");
			//$bizType = I("bizType");						
			$zhengModel = D('Zheng');
			$result = $zhengModel->ihttpPost(array("method"=>"api.common.input","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "input" => $verifycode));
			$arr = json_decode($result, true);
			$this->ajaxReturn($arr,'JSON');
		}		
	}		
	/*public function mobileAuthReturn()
	{
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		$callid = I("callid");
		if ($callid) {
			$infoModel->setMobile($userInfo["id"], $callid);
		}
		if (!$infoModel->hasSetAddess($userInfo["id"])) {
			$this->redirect("Info/addessAuth");
		}
		if (!$infoModel->hasSetMobile($userInfo["id"])) {
			$this->redirect("Info/mobileAuth");
		}
		$this->display();
	}*/
	public function taobaoAuth()
	{
		/*$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if (!$infoModel->hasSetMobile($userInfo["id"])) {
			$this->redirect("Info/mobileAuth");
		}
		if ($infoModel->hasSetTaobao($userInfo["id"])) {
			$this->error("Taobao data has been submitted", U("Info/check"));
		}
		$callbackurl = "http://" . $_SERVER["SERVER_NAME"] . U("Callback/taobaoAuthCallback");
		$returnurl = "http://" . $_SERVER["SERVER_NAME"] . U("Info/taobaoAuthReturn");
		$result = curl("http://www.xauguo.cn/Api/Taobao/geturi/", array("callbackurl" => $callbackurl, "returnurl" => $returnurl, "appkey" => C("ugappkey")), 1);
		if (!$result) {
			$this->error("Request failed");
		}
		$arr = json_decode($result, true);
		if (!$arr) {
			$this->error("Failed to parse data");
		}
		if ($arr["code"] != 0) {
			$this->error($arr["data"]);
		}
		$callid = $arr["data"]["callid"];
		if (!$callid) {
			$this->error("Failed to parse data");
		}
		$infoauthModel = D("Infoauth");
		if (!$infoauthModel->addAuth("taobao", $userInfo["id"], $callid)) {
			$this->error("Failed to save authorization information");
		}
		header("Location: http://www.xauguo.cn/Api/Taobao/index/?callid=" . $callid);
		exit(0);
		return null;*/
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if (!$infoModel->hasSetMobile($userInfo["id"])) {
			$this->redirect("Info/mobileAuth");
		}
		if ($infoModel->hasSetTaobao($userInfo["id"])) {
			$this->error("Taobao data has been submitted", U("Info/check"));
		}
		if ($this->isPost()) {
			//$mobile = I("mobile");
			//$fwpass = I("fwpass");
			//$verifycode = I("verifycode");
			$token = I("token");
			if(!$token){
				$this->error("Please get the verification code first");
			}			
			$zhengModel = D('Zheng');
			$result = $zhengModel->ihttpPost(array("method"=>"api.common.getResult","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "bizType" => 'taobao'));
		
			$arr1 = json_decode($result, true);
			//$arr2 = json_decode($result1, true);
			
			$data = json_encode($arr1['data']);
			//var_dump($data);die;
			$infoModel = D("Info");
			if (!$infoModel->setTaobao($userInfo["id"], $data)) {
				$this->error('Failed to obtain authorization');
			}else{
				$this->success('Obtain authorization successfully');
			}
			//$this->ajaxReturn($arr,'JSON');
			/*var_dump($result);die;
			$result = $infoModel->setBank($userInfo["id"], array("bankName" => $bankName, "bankNum" => $bankNum, "bankPhone" => $bankPhone));
			if (!$result) {
				$this->error("Failed to save information");
			}
			$this->success("Successfully saved", U("Info/check"));*/
		}
		$this->display();
		exit(0);
	}
	
	public function taobao(){
		$this->before();
		//$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if ($this->isPost()) {
			/*$mobile = I("mobile");
			$fwpass = I("fwpass");			
			if (!$mobile) {
				$this->error("Please enter Taobao account");
			}			
			if (!$fwpass) {
				$this->error("Please enter Taobao password");
			}*/
			/*$idcard = json_decode($infoModel->getAuthInfo($userInfo["id"], "identity"), true);
			if (!$idcard) {
				$this->error("Failed to recall data");
			}
			$realname = $idcard["name"];
			$cardno = $idcard["idcard"];*/
			//$callbackurl = "http://" . $_SERVER["SERVER_NAME"] . U("Callback/mobileAuthCallback");
            /**
            $zhengModel = D('Zheng');
            $result = $zhengModel->ihttpPost(array("method"=>"api.taobao.get","apiKey"=>$this->appkey,"version"=>$this->version,"loginType"=>"qr","username" => $mobile, "password" => base64_encode($fwpass)));
            $arr = json_decode($result, true);
            $this->ajaxReturn('ok','JSON');
             **/
            $this->ajaxReturn('ok','JSON');
        }
    }
	public function taobaostatus(){
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		if ($this->isPost()) {
			$token = I("token");
			$bizType = "taobao";						
			$zhengModel = D('Zheng');
			$result = $zhengModel->ihttpPost(array("method"=>"api.common.getStatus","apiKey"=>$this->appkey,"version"=>$this->version,"token" => $token, "bizType" => $bizType));
			$arr = json_decode($result, true);
			$this->ajaxReturn($arr,'JSON');
		}		
	}

	
	public function taobaoAuthReturn()
	{
		$this->before();
		$infoModel = D("Info");
		$userInfo = $this->isLogin();
		$callid = I("callid");
		if ($callid) {
			$infoModel->setTaobao($userInfo["id"], $callid);
		}
		$this->display();
	}
}