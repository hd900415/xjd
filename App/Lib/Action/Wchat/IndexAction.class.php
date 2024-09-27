<?php

class IndexAction extends CommonAction
{
    public function index()
    {
        // print('hello');
//        $this->redirect("Repay/loans");
        cookie("fenxiang", "1", 30);
//		$this->display();
    }
     /**
     *  查看 订单还款状态：
     */ 
     public function Repaymentstatus()
    {
        
        $orderId = I("oid");
        if (!$orderId) {
            $this->error("param order id is required");
        }
        // $user = $this->isLogin();
        // if (!$user || empty($user)) {
        //     $this->error("User is not logged in");
        // }
        $loanorderModel = D("Loanorder");
        $bill = $loanorderModel->where(array('oid' => $orderId))->find();
        if(!$bill){
             $this->ajaxReturn(array(
                    "status" => 403,
                    "message" => 'No information found for this order',
                   
                ));
        }else{
            if($bill['status']=="2"){
                $status = 200;
                $message =  "Payment successful";
            }else{
                 $status = 201;
                $message =  "Payment has not been successful";
            } 
        }   
        $this->ajaxReturn(array(
                    "status" => $status,
                    "message" => $message,
                   
        ));

          
       

    }
    public function login()
    {
        if ($this->isLogin()) {
            $this->redirect("Repay/index");
        }
        $this->display();
    }

    public function forgetpwd()
    {
        if ($this->isPost()) {
            $username = I("username");
            $userInfo = $this->isLogin();
            if ($userInfo) {
                $username = $userInfo["telnum"];
            }
            $password = I("password");
            if (!$username) {
                $this->error("phone number is empty");
            }
            if (!isMobile($username)) {
                $this->error("Incorrect phone number");
            }
            if (!$password) {
                $this->error("new password is empty");
            }
            if (strlen($password) < 8 || strlen($password) > 16) {
                $this->error("Please enter 8-16 digit password");
            }
            $code = I("code");
            if (strlen($code) != 4) {
                $this->error("SMS verification code entered incorrectly");
            }
            $smsModel = D("Sms");
            $sms = $smsModel->getInfo($username, "find");
            if (!$sms) {
                $this->error("SMS verification code incorrectly");
            }
            if ($sms["send_time"] + 30 * 60 < time()) {
                $this->error("SMS verification code is invalid, please try again");
            }
            $userModel = D("User");
            $password = $userModel->str2pass($password);
            $result = $userModel->getInfo(array("telnum" => $username));
            if (!$result) {
                $this->error("User does not exist");
            }
            $result = $userModel->updateInfo($result["id"], array("password" => $password));
            if (!$result) {
                $this->error("Failed to retrieve password, please try again later");
            }
            $this->success("Successfully modified", U("Index/login"));
        }
        $this->display();
    }

    public function logout()
    {
        $this->setLogin(NULL);
        $this->redirect("Repay/index");
        exit(0);
    }

    public function more()
    {
        $userInfo = $this->isLogin();
        if (!$userInfo || empty($userInfo)) {
            $this->redirect("Index/login");
        }
        $this->display();
    }

    public function fenxiang()
    {
        $value = cookie("fenxiang");
        if (!$value) {
            $this->redirect("Repay/index");
        }
        $this->display();
    }

    public function ajaxLogin()
    {
        $username = I("username");
        $password = I("password");
        if (!$username) {
            $this->error("phone number is empty");
        }

        $userModel = D("User");
//        $password = $userModel->str2pass($password);
        $result = $userModel->getInfo(array("telnum" => $username));
        if (!$result) {
            $this->error("Incorrect username or password");
        }
        if (!$result["status"]) {
            $this->error("Account has been disabled, please contact administrator");
        }
        $this->setLogin($result);
        $this->success("login successful", U("Repay/index"));
        return null;
    }

    public function ajaxSignIn()
    {
        $username = I("username");
        $password = I("password");
        if (!$username) {
            $this->ajaxReturn(array("status" => 300, "message" => "phone number is empty"));
        }

        $userModel = D("User");
//        $password = $userModel->str2pass($password);
        $result = $userModel->getInfo(array("telnum" => $username));
        if (!$result) {
            $this->ajaxReturn(array("status" => 404, "message" => "Incorrect username or password"));
        }
        if (!$result["status"]) {
            $this->ajaxReturn(array("status" => 403, "message" => "Account has been disabled, please contact administrator"));
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!$result["login_time"]) {//首次登陆时间没有则保存
            $result["login_time"] = date("Y-m-d H:i:s");
            $result["login_count"] = 1;
            $userModel->updateInfo($result["id"], $result);
        } else {
            $result["login_count"] = intval($result["login_count"]) + 1;
            $userModel->updateInfo($result["id"], $result);
        }
//        if (!$_COOKIE['PHPSESSID'] || !session_id()) {
//            $ssessId = generateRandomString(32);
//            session_id($ssessId);
//            setcookie("PHPSESSID", $ssessId, 86400 * 100000, '/');
//        }
        session_set_cookie_params(86400 * 100000, '/');
        $this->setLogin($result);
        $this->ajaxReturn(array("status" => 200, "message" => "login successful", "data" => U("Repay/index")));
//        $this->ajaxReturn(array("status" => 200, "message" => "login successful", "data" => [
//            'url' => U("Repay/index"),
//            'telnum' => $result['telnum'],
//            'email' => $result['email'],
//            'firstname' => $result['firstname'],
//            'lastname' => $result['lastname'],
//            'confirm' => $result['confirm'],
//            'quota' => $result['quota'],
//        ]));
        return null;
    }

    public function ajaxLogOut()
    {
        $this->setLogin(NULL);
        $this->ajaxReturn(array("status" => 200, "message" => "Logout successful"));
    }

    public function ajaxReg()
    {
        $username = I("username");
        $password = I("password");
        if (!$username) {
            $this->error("phone number is empty");
        }
        if (!isMobile($username)) {
            $this->error("Incorrect phone number input");
        }
        if (!$password) {
            $this->error("password is empty");
        }
        if (strlen($password) < 8 || strlen($password) > 16) {
            $this->error("Please enter 8-16 digit password");
        }
        $code = I("code");
        if (strlen($code) != 4) {
            $this->error("SMS verification code entered incorrectly");
        }
        $smsModel = D("Sms");
        $sms = $smsModel->getInfoMy($username, $code, "reg");
        if (!$sms) {
            $this->error("SMS verification code entered incorrectly");
        }
        if ($sms["send_time"] + 30 * 60 < time()) {
            $this->error("SMS verification code is invalid, please try again");
        }
        $userModel = D("User");
        $password = $userModel->str2pass($password);
        $result = $userModel->getInfo(array("telnum" => $username));
        if ($result) {
            $this->error("The current phone number has been registered, please log in");
        }
        if (!$userModel->addInfo($username, $password)) {
            $this->error("Registration failed, please try again");
        }
        $result = $userModel->getInfo(array("telnum" => $username));
        $this->setLogin($result);
        $this->success("Registration success", U("Index/index"));
    }

    public function verify()
    {
        C("app_debug", false);
        import("ORG.Util.Image");
        Image::buildImageVerify();
    }

    public function quotaConfirm()
    {
        $userModel = D("User");

        $token = I("token");
        if ($token == session('user_quote_token')) {
            $userInfo = $this->isLogin();
            if ($userInfo) {
                $userModel->where(['id' => $userInfo['id']])->save(['confirm' => 1]);
                $this->success("Successful", U("Repay/index"));
            } else {
                $this->error("The user is not logged in.Please login first", U("Index/login"));
            }
        } else {
            $this->error("The token is error");
        }
    }
    public function contact()
    {
        $phone_number=C('phone_number');
        $whatapp=C('whatapp');
        $this->ajaxReturn(array("status" => 200, "message" => "successful",'data'=>[
            'phone_number'=>$phone_number,
            'whatapp'=>$whatapp
        ]));
    }

    public function ajaxApply()
    {
        $userModel = D("User");

        $userInfo = $this->isLogin();
        if ($userInfo) {
            $userModel->where(['id' => $userInfo['id']])->save(['confirm' => 1]);
            $userInfo['confirm'] = 1;
            $this->setLogin($userInfo);
            $this->ajaxReturn(array("status" => 200, "message" => "apply successful"));
        } else {
            $this->ajaxReturn(array("status" => 500, "message" => "The user is not logged in.Please login first"));

        }
    }
    public function settarget()
    {
     
        $target=C("Default_Target");
        $data = array(
            'target'=>$target
        );
        $this->ajaxReturn(array(
            'status'=>200,
            'data'=>$data,
            "message" => "successful"
        ));
    }
    public function languages()
    {
        $languages=C("Languages");
        $langs=explode(',',$languages);
        $default=C("Default_Language");
        $target=C("Default_Target");
        $loanAmount=C("DEFAULT_QUOTA");
        $currencyType = C("CURRENCY");
        if(!in_array($default,$langs)) array_push($langs,$default);
        $data = array('languages'=>$langs,
            'default'=>$default,
            'target'=>$target,
            'loanAmount'=>$loanAmount,
            'currencyType'=>$currencyType
        );
        $this->ajaxReturn(array(
            'status'=>200,
            'data'=>$data,
            "message" => "successful"
            
        ));
    }
}