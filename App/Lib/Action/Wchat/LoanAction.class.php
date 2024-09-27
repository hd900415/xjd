<?php

class LoanAction extends CommonAction
{
    public function review()
    {
        $this->display();
    }

    public function refuse()
    {
        $this->display();
    }

    public function getConfirmInfo()
    {
        session("LoanInfo", NULL);
        $userInfo = $this->isLogin();
        if (!$userInfo) {
            $this->error("please login first", U("Index/login"));
        }
        $money = I("money");
        $time = I("time");
        if (!$money || !$time) {
            $this->error("Parameter error");
        }
        $MoneyScale = getMoneyScale();
        if ($money < $MoneyScale["min"] || $money > $MoneyScale["max"]) {
            $this->error("The loan amount does not meet the requirements");
        }
        $infoModel = D("Info");
// 		$infoStatus = $infoModel->getStatus($userInfo["id"]);
// 		if ($infoStatus == 0) {
// 			$this->error("Please complete the data evaluation", U("Info/check"));
// 		}
// 		if ($infoStatus == 0 - 1) {
// 			$this->error("Your profile has not been reviewed", U("Loan/refuse"));
// 		}
// 		if ($infoStatus == 1) {
// 			$this->error("Your information is being reviewed, please be patient", U("Loan/review"));
// 		}
// 		if (!$userInfo["quota"] || $userInfo["quota"] == 0) {
// 			$this->error("You currently have no credit limit and cannot apply for a loan");
// 		}
        /*if ($userInfo['vipid']==0){
            $this->error("You are not a premium member, please purchase first", U("Page/buy"));
        }*/
// 		$userModel = D("User");
// 		$doquota = $userModel->getDoquota($userInfo["id"]);
// 		if ($doquota < $money) {
// 			$this->error("Your credit limit is insufficient to apply for a loan");
// 		}
// 		$DeadlineList = getDeadlineList();
// 		if (!$DeadlineList || !$DeadlineList["list"]) {
// 			$this->error("System error, please contact customer service");
// 		}
// 		if (!is_array($DeadlineList["list"])) {
// 			$this->error("system setting error");
// 		}
// 		if (!in_array($time, $DeadlineList["list"])) {
// 			$this->error("The loan term does not meet the requirements");
// 		}
// 		$infoModel = D("Info");
// 		if (!$infoModel->checkAllInfo($userInfo["id"])) {
// 			$this->error("Your information is incomplete, please add", U("Info/check"));
// 		}
        $idcardInfo = $infoModel->getAuthInfo($userInfo["id"], "identity");
        $idcardInfo = json_decode($idcardInfo, true);
// 		if (!$idcardInfo) {
// 			$this->error("Failed to obtain identity information");
// 		}
        $bankInfo = $infoModel->getAuthInfo($userInfo["id"], "bank");
        $bankInfo = json_decode($bankInfo, true);
        if (!$bankInfo) {
            $this->error("Failed to obtain the receiving bank card");
        }
        $starttime = strtotime(date("Y-m-d"));
        if (C("Loan_TYPE")) {
            $endtime = strtotime("+" . $time . " Month", $starttime);
            $repaymenttime = date("d", strtotime("+29 day", $starttime));
            $fastrepayment = strtotime("+29 day", $starttime);
        } else {
            $endtime = strtotime("+" . $time . " day", $starttime);
            $repaymenttime = $endtime;
            $fastrepayment = $endtime;
        }
        $data = array("uid" => $userInfo["id"], "name" => $idcardInfo["name"], "idcard" => $idcardInfo["idcard"], "money" => $money, "time" => $time, "bankname" => $bankInfo["bankName"], "banknum" => substr($bankInfo["bankNum"], 0, 4) . '***' . substr($bankInfo["bankNum"], 0 - 4), "loantype" => C("Loan_TYPE"), "interest" => getInterest(), "reply_rate" => getRepayRate(), "starttime_str" => date("Y/m/d", $starttime), "starttime" => $starttime, "endtime_str" => date("Y/m/d", $endtime), "endtime" => $endtime, "repaymenttime" => $repaymenttime, "fastrepayment_str" => date("m/d", $fastrepayment), "fastrepayment" => $fastrepayment, "overdue" => C("Overdue"));
        session("LoanInfo", $data);
        $this->success($data);
    }

    public function signature()
    {
        if ($this->isPost()) {
            /*$verifycode = I("verifycode");
            if(!$verifycode){
                $this->error("verification code must be filled");
            }
            if($verifycode != C('verifycode')){
                $this->error("The verification code is wrong");
            }*/
            $signature = I("signature");
            if (!$signature) {
                $this->error("Contract signing failed");
            }
            $data = session("LoanInfo");
            if (!$data) {
                $this->error("Failed to extract loan information");
            }
            $loanorderModel = D("Loanorder");
            $data["oid"] = $loanorderModel->newOrder($data);

            if (!$data["oid"]) {
                $this->error("Failed to generate order");
            }
            $infoModel = D("Info");
            $userInfo = $this->isLogin();
            $bankInfo = $infoModel->getAuthInfo($userInfo["id"], "bank");
            $bankInfo = json_decode($bankInfo, true);
            $data['ifsc'] = $bankInfo['ifsc'];
            $data['realname'] = $bankInfo['name'];
// 			if (!$bankInfo) {
// 				$this->error("收款银行卡获取失败");
// 			}
            //分配订单
            $admins = M('admin')->where(array('type' => 2))->select();
            $carrays = array();
            if ($admins) {
                foreach ($admins as $v) {
                    $count = $loanorderModel->getOrderCount($v['id']);
                    $carrays[] = array('uid' => $v['id'], 'count' => $count);
                }
            }
            $carrays = sigcol_arrsort($carrays, 'count', SORT_ASC);
            $sid = $carrays[0]['uid'];
            if (!$sid) {
                $sid = 0;
            }
            $arr = array("uid" => $data["uid"], "oid" => $data["oid"], "money" => $data["money"], "time" => $data["time"], "timetype" => $data["loantype"], "name" => $data["name"], "bankname" => $data["bankname"], "banknum" => $bankInfo["bankNum"], "interest" => $data["interest"], "reply_rate" => $data["reply_rate"], "start_time" => $data["starttime"], "overdue" => $data["overdue"], "add_time" => time(), "sign" => $signature, "data" => json_encode($data), "status" => 0, "pending" => 0, "sid" => $sid);
            //分配订单结束
            $toid = $loanorderModel->add($arr);
            if (!$toid) {
                $this->error("Order save failed");
            }
            session("LoanInfo", NULL);
            $smsModel = D("Sms");
            $content = htmlspecialchars_decode(htmlspecialchars_decode(C("loan_submit")));
            $content = str_replace("<@>", $data["oid"], $content);
            $content = str_replace("《@》", $data["oid"], $content);
            $smsModel->sendSms($userInfo["telnum"], $content);
            $this->success("Signed successfully", U("Loan/signdone", array("oid" => $data["oid"])));
        }
        $data = session("LoanInfo");
        if (empty($data)) {
            $this->redirect("Repay/index");
        }
        $this->display();
    }

    public function signdone()
    {
        $oid = I("oid");
        if (!$oid) {
            $this->redirect("Repay/index");
        }
        $this->assign("oid", $oid);
        $this->display();
    }

    public function viewContract()
    {
        $contractTpl = C("contractTpl");
        $contractTpl = empty($contractTpl) ? "" : htmlspecialchars_decode(htmlspecialchars_decode($contractTpl));
        $this->assign("tpl", $contractTpl);
        $this->display();
    }

    public function applyLoan()
    {
        if ($this->isPost()) {
            $userModel = D("User");
            $token = I("token");
            if ($token == session('user_quote_token')) {
                $userInfo = $this->isLogin();
                if ($userInfo) {
                    $loanorderModel = D("Loanorder");
                    //用户是否可以重新借款
                    $canLoanAgain = $loanorderModel->canLoanAgain($userInfo["id"], 0);
                    if (!$canLoanAgain) {
                        $this->error('You already have a new loan order or your previous order has not been repaid successfully, if you have any questions, please contact us');
                    }
                    $loanTime = C("DEFAULT_LOAN_TIME");
                    $loanTime = $loanTime > 0 ? $loanTime : 7;
                    $flag = $loanorderModel->addUserOrder($userInfo["id"], $loanTime, 1);
                    $this->success("Successful", U("Repay/index"));
                } else {
                    $this->error("The user is not logged in.Please login first", U("Index/login"));
                }
            } else {
                $this->error("The token is error");
            }
        } else {
            $this->error("Unsupported request type");
        }

    }

    public function reLoan()
    {
        if ($this->isPost()) {
            $userModel = D("User");

            $userInfo = $this->isLogin();
            if ($userInfo) {
                $loanorderModel = D("Loanorder");
                //用户是否可以重新借款
                $canLoanAgain = $loanorderModel->canLoanAgain($userInfo["id"], 0);
                if (!$canLoanAgain) {
                    $this->ajaxReturn(array(
                        "status" => 1001,
                        "message" => 'You already have a new loan order or your previous order has not been repaid successfully, if you have any questions, please contact us',
                        "data" => ""
                    ));
                }
                $loanTime = C("DEFAULT_LOAN_TIME");
                $loanTime = $loanTime > 0 ? $loanTime : 7;
                $user = $userModel->where(array("id" => $userInfo["id"]))->find();
                $flag = $loanorderModel->addUserOrder($userInfo["id"], $loanTime, 1, [], $user);
                $this->ajaxReturn(array(
                    "status" => 200,
                    "message" => 'Successful',
                    "data" => "",
                ));

            } else {
                $this->ajaxReturn(array(
                    "status" => 403,
                    "message" => 'The user is not logged in.Please login first',
                    "data" => ""
                ));
            }
        } else {
            $this->ajaxReturn(array(
                "status" => 405,
                "message" => 'Unsupported request Method',
                "data" => ""
            ));
        }

    }
}