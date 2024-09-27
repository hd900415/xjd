<?php

class RepayAction extends CommonAction
{

    public function loans()
    {

        $userModel = D("User");
        $userInfo = $this->isLogin();
//        print_r($userInfo);
        if ($userInfo) {
//            $doquota = $userModel->getDoquota($userInfo["id"]);
            $user = $userModel->where(array("id" => $userInfo["id"]))->find();

            $loanorderModel = D("Loanorder");
            $loanbillModel = D("Loanbill");
            $bill = $loanbillModel->field(['id', 'toid', 'oid', 'delay', 'delay_day', 'repay_interest', 'repayment_time', 'add_time', 'money'])
                ->where(array("uid" => $userInfo["id"], "status" => array("IN", "0,1,4,5,6")))->order("repayment_time DESC")->find();
            $confirm = intval($userInfo['confirm']);
            $money = $billMoney = $bill && $bill["money"] ? $bill["money"] : $user['quota'] ? $user['quota'] : C('DEFAULT_QUOTA');
            $needRepayMoney = intval($billMoney) + intval(C('REPAY_COST'));
            $hasBill = true;

            if ($bill) {
                $loanOrder = $loanorderModel->where(array("id" => $bill['toid'], "uid" => $userInfo['id']))->find();
                $hasBill = true;
            } else {
                $loanOrder = $loanorderModel->where(array("uid" => $userInfo['id'], "pending" => 0, "status" => 0))->order('add_time DESC')->find();

                $hasBill = false;
                $bill = [
                    "oid" => $loanOrder['oid'],
                    "delay" => $loanOrder['delay'],
                    "delay_day" => $loanOrder['delay'],
                    "add_time" => $loanOrder['add_time'],
                    "money" => $loanOrder['money'],
                ];
            }
            if (!$loanOrder) {
                $repaymentTime = strtotime("+7 days");
                $loanTime = C('DEFAULT_LOAN_TIME') ? C('DEFAULT_LOAN_TIME') : 7;
                $serviceRate = getInterest();
                $addTime = time();
            } else {
                $repaymentTime = $loanorderModel->getOrderRepayTime($loanOrder);
                if ($repaymentTime < time() && $loanOrder["status"] != 2) {
                    $overFee = $loanorderModel->calcOverDueFee($loanOrder);
                }
                $repayFee = $loanorderModel->calcRepayFee($loanOrder);
                $billMoney = $loanOrder['money'];
                $needRepayMoney = $loanorderModel->calcRepayMoney($loanOrder);
                $loanTime = $loanOrder['time'];
                $serviceRate = $loanOrder['interest'];
                $addTime = $bill["add_time"];
            }
            $bill['repayment_time'] = $repaymentTime;
            $bill['repay_interest'] = $repayFee;
//            print_r([$money , $loanTime , $serviceRate]);
            $serviceFee = $money * $loanTime * $serviceRate;
            $returnData = array(
                "money" => $billMoney,
                "repay_money" => $needRepayMoney, #应还金额
                'addtime' => date("Y-m-d H:i:s", $addTime),
                "repayment_time" => date("Y-m-d H:i:s", $repaymentTime),
                "loan_time" => $loanTime,
                "billId" => $bill["id"],
                "confirm" => $confirm,
                "overFee" => $overFee ? $overFee : 0,
                "serviceFee" => $serviceFee,
                "bill" => $bill,
                "hasOverdue" => $repaymentTime < time(),
                'doquota' => $user['quota'],
                'commission' => 0,
                "quota" => $userInfo['quota'] ? $userInfo['quota'] : C('DEFAULT_QUOTA'),
                "default_loan_time" => C('DEFAULT_LOAN_TIME') ? C('DEFAULT_LOAN_TIME') : 7,
                "default_deadline" => C('Deadline_D') ? C('Deadline_D') : '7,10,15,20',
                "default_loan_interest" => C("Interest_D"),
                "default_repay_interest" => C("Interest_R"),
                "currency_symbol" => C("CURRENCY_SYMBOL"),
                "next_loan" => C("Next_Loan"),
                "approval_rate" => C("Approval_Rate"),
                "next_loan_period" => C("Next_Loan_Period"),
            );
            $returnData['overdue_day'] = 0;
            if ($repaymentTime < time()) {
                $returnData['overdue_day'] = $loanorderModel->calcOverDueDay($repaymentTime);
            }
            if ($bill['delay'] == 1 && in_array(intval($bill['status']), array(4))) { //续期
//                $delayFee = $bill['money'] * $bill['delay_rate'] * $bill['delay_day'];

                $delayFee = $loanorderModel->calcDelayFee($loanOrder);
                $returnData['delay'] = $bill["delay"];
                $returnData['nextRepayDay'] = $repaymentTime;
                $returnData['repayment_time'] = $repaymentTime;
                $returnData['delayFee'] = $delayFee;
                $returnData['status'] = 4; //续期中

                if ($repaymentTime < time() || intval($loanOrder['status']) == 1) {
                    $returnData['status'] = 1; //逾期未还
                }
                if (!$hasBill) $returnData['status'] = 5;
                $this->ajaxReturn(array("status" => 200,
                    "data" => $returnData,
                    "message" => "Successfully"));
            } elseif ($bill['money'] > 0) {
                $returnData['status'] = 0; //正常待还款
                if ($repaymentTime < time() || intval($loanOrder['status']) == 1) {
                    $returnData['status'] = 1; //逾期未还
                }
                if (!$hasBill) $returnData['status'] = 5;
                if (!$loanOrder) $returnData['status'] = -1;
                $this->ajaxReturn(array("status" => 200,
                    "data" => $returnData,
                    "message" => "Successfully"));
            } else {
                $token = rand(1000000000000000, 9999999999999999999);
                session('user_quote_token', $token);
                $this->assign("token", $token);
                $loanorderModel = D("Loanorder");
                //用户是否可以重新借款
                $canLoanAgain = $loanorderModel->canLoanAgain($userInfo["id"]);
                //最新的一笔交易是否被拒绝
                $lastLoanOrder = $loanorderModel->where(array("uid" => $userInfo["id"]))->order("add_time DESC")->limit(1)->find();
                $isRefused = 0;
                if ($lastLoanOrder && intval($lastLoanOrder['pending']) == 2) {
                    $isRefused = 1;
                    $canLoanAgain = false;
                }
                $returnData['canLoanAgain'] = $canLoanAgain;
                $returnData['isRefused'] = $isRefused;
                $returnData['token'] = $token;
                $returnData['status'] = 2; //正常还款
                if (!$hasBill) $returnData['status'] = -1;
                $this->ajaxReturn(array("status" => 200,
                    "data" => $returnData,
                    "message" => "Successfully"));
            }
        } else {
            $this->ajaxReturn(array("status" => 200,
                "data" => [
                    "status" => -100,
                    "quota" => C('DEFAULT_QUOTA'),
                    "default_loan_time" => C('DEFAULT_LOAN_TIME') ? C('DEFAULT_LOAN_TIME') : 7,
                    "default_deadline" => C('Deadline_D') ? C('Deadline_D') : '7,10,15,20',
                    "default_loan_interest" => C("Interest_D"),
                    "default_repay_interest" => C("Interest_R"),
                    "currency_symbol" => C("CURRENCY_SYMBOL"),
                    "next_loan" => C("Next_Loan"),
                    "approval_rate" => C("Approval_Rate"),
                    "next_loan_period" => C("Next_Loan_Period"),
                ],
                "message" => "If the user is not logged in, the default quota will be displayed"));
        }
        exit;
    }

    public function fullRepay()
    {
        $orderId = I("oid");
        if (!$orderId) {
            $this->error("param order id is required");
        }
        $delay = false;
        if (I("next") && I("next") == "delay") {
            $delay = true;
        }
        $loanorderModel = D("Loanorder");
        $loanbillModel = D("Loanbill");
        $loanOrder = $loanorderModel->where(array("oid" => $orderId))->find();
        $bill = $loanbillModel->where(array("oid" => $orderId))->find();
        if (!$loanOrder || !$bill) {
            $this->error("order or bill does not exist");
        }
        $delayRate = C('PostPone_RATE');
        $needRepayMoney = $loanorderModel->calcRepayMoney($loanOrder);
        $needRepaymentTime = $loanorderModel->getOrderRepayTime($loanOrder);
        $needRepaymentDay = date("d-m-Y", $needRepaymentTime);
        $this->assign("oid", $orderId);
        $this->assign("needRepayMoney", $needRepayMoney);
        $this->assign("needRepaymentTime", $needRepaymentTime);
        $this->assign("needRepaymentDay", $needRepaymentDay);
        $this->assign("needDelay", 0);
        //统计点击还款按钮次数
        $userModel = D("User");
        $userInfo = $userModel->getInfo(array("id" => $loanOrder['uid']));
        if ($userInfo) {
            $update = [];
            $update["reply_count"] = intval($userInfo['reply_count']) + 1;
            $userModel->updateInfo($userInfo['id'], $update);
        }
        if ($delay) {
            $delayDefaultDays = C('DELAY_DEFAULT_DAYS');
            $delayDefaultDays = $delayDefaultDays > 0 ? $delayDefaultDays : 7;
            $days = I("day", $delayDefaultDays);
            $delayDay = $days;
            $nextRepayDatetime = strtotime('+' . $delayDay . 'days', $bill["repayment_time"]);
            $hasOverdueFee = $loanOrder['money'] * floatval($loanOrder['overdue']) * 7;
            if ($nextRepayDatetime < time()) {
                $needRepayMoney = $needRepayMoney - $hasOverdueFee;
                $this->assign("needRepayMoney", $needRepayMoney);
            }
            $overdueFee = $loanorderModel->calcOverDueFee($loanOrder);
            if ($overdueFee > 0 && $nextRepayDatetime > time()) {
                $needRepayMoney = $needRepayMoney - $overdueFee;
                if ($needRepayMoney < 0) $needRepayMoney = 0;
                $this->assign("needRepayMoney", $needRepayMoney);
            }
            $delayFee = $days * $delayRate * $bill['money'];
            $this->assign("needDelay", 1);
            $this->assign("delayFee", $delayFee);

            $this->assign("nextRepayDatetime", $nextRepayDatetime);
            $this->assign("nextRepayDay", date('d-m-Y', $nextRepayDatetime));
        }
        $this->assign("bill", $bill);
        $this->renderTemplate($delay);
//        if (in_array($_SERVER['HTTP_HOST'], ['m.moneycasha.top', 'm.quikwalleta.top'])) {
//            $this->display("New_fullRepay");
//        } else {
//            $this->display();
//        }
    }
    public function loanDetail()
    {

        $orderId = I("oid");
        if (!$orderId) {
            $this->ajaxReturn([
                'status'=>400,
                'message'=>"param order id is required"
            ]);
        }
        $loanorderModel = D("Loanorder");
        $loanbillModel = D("Loanbill");
        $loanOrder = $loanorderModel->where(array("oid" => $orderId))->find();
        $bill = $loanbillModel->where(array("oid" => $orderId))->find();
        if (!$loanOrder || !$bill) {
            $this->ajaxReturn([
                'status'=>404,
                'message'=>"order or bill does not exist"
            ]);
        }
        $delayRate = C('PostPone_RATE');
        $needRepayMoney = $loanorderModel->calcRepayMoney($loanOrder);
        $needRepaymentTime = $loanorderModel->getOrderRepayTime($loanOrder);
        $needRepaymentDay = date("d-m-Y", $needRepaymentTime);
        //统计点击还款按钮次数
        $userModel = D("User");
        $userInfo = $userModel->getInfo(array("id" => $loanOrder['uid']));
        if ($userInfo) {
            $update = [];
            $update["reply_count"] = intval($userInfo['reply_count']) + 1;
            $userModel->updateInfo($userInfo['id'], $update);
        }
        if (!$loanOrder) {
            $loanTime = C('DEFAULT_LOAN_TIME') ? C('DEFAULT_LOAN_TIME') : 7;
            $serviceRate = getInterest();
        } else {
            $loanTime = $loanOrder['time'];
            $serviceRate = $loanOrder['interest'];
        }
        $serviceFee = $loanOrder['money'] * $loanTime * $serviceRate;
        $this->ajaxReturn(array("status" => 200,
            "data" => [
                'bill'=>$bill,
                'oid'=>$orderId,
                'money'=>$loanOrder['money'],
                'interest'=>C('REPAY_COST'),
                'needRepayMoney'=>$needRepayMoney,
                'needRepaymentTime'=>$needRepaymentTime,
                'needRepaymentDay'=>$needRepaymentDay,
                'serviceFee'=>$serviceFee,
            ],
            "message" => "Successfully"));
    }
    public function loanDelay()
    {
        $orderId = I("oid");
        if (!$orderId) {
            $this->ajaxReturn([
                'status'=>400,
                'message'=>"param order id is required"
            ]);
        }
        $loanorderModel = D("Loanorder");
        $loanbillModel = D("Loanbill");
        $loanOrder = $loanorderModel->where(array("oid" => $orderId))->find();
        $bill = $loanbillModel->where(array("oid" => $orderId))->find();
        if (!$loanOrder || !$bill) {
            $this->ajaxReturn([
                'status'=>404,
                'message'=>"order or bill does not exist"
            ]);
        }
        $delayRate = C('PostPone_RATE');
        $needRepayMoney = $loanorderModel->calcRepayMoney($loanOrder);
        $needRepaymentTime = $loanorderModel->getOrderRepayTime($loanOrder);
        $needRepaymentDay = date("d-m-Y", $needRepaymentTime);
        //统计点击还款按钮次数
        $userModel = D("User");
        $userInfo = $userModel->getInfo(array("id" => $loanOrder['uid']));
        if ($userInfo) {
            $update = [];
            $update["reply_count"] = intval($userInfo['reply_count']) + 1;
            $userModel->updateInfo($userInfo['id'], $update);
        }
        $delayDefaultDays = C('DELAY_DEFAULT_DAYS');
        $delayDefaultDays = $delayDefaultDays > 0 ? $delayDefaultDays : 7;
        $days = I("day", $delayDefaultDays);
        $delayDay = $days;
        $nextRepayDatetime = strtotime('+' . $delayDay . 'days', $bill["repayment_time"]);
        $hasOverdueFee = $loanOrder['money'] * floatval($loanOrder['overdue']) * 7;
        if ($nextRepayDatetime < time()) {
            $needRepayMoney = $needRepayMoney - $hasOverdueFee;
        }
        $overdueFee = $loanorderModel->calcOverDueFee($loanOrder);
        if ($overdueFee > 0 && $nextRepayDatetime > time()) {
            $needRepayMoney = $needRepayMoney - $overdueFee;
            if ($needRepayMoney < 0) $needRepayMoney = 0;
        }
        $delayFee = $days * $delayRate * $bill['money'];
        $this->ajaxReturn(array("status" => 200,
            "data" => [
                'bill'=>$bill,
                'oid'=>$orderId,
                'needRepayMoney'=>$needRepayMoney,
                'needRepaymentTime'=>$needRepaymentTime,
                'needRepaymentDay'=>$needRepaymentDay,
                'delayFee'=>$delayFee,
                'money'=>$loanOrder['money'],
                'interest'=>C('REPAY_COST'),
                'nextRepayDatetime'=>$nextRepayDatetime,
                'overdueFee'=>$overdueFee,
                'nextRepayDay'=>date('d-m-Y', $nextRepayDatetime),
            ],
            "message" => "Successfully"));
    }
    public function detail()
    {
        $orderId = I("oid");
        if (!$orderId) {
            $this->ajaxReturn(array("status" => 300,
                "data" => [],
                "message" => "params error"));
        }
        $delay = false;
        if (I("next") && I("next") == "delay") {
            $delay = true;
        }
        $loanorderModel = D("Loanorder");
        $loanbillModel = D("Loanbill");
        $loanOrder = $loanorderModel->where(array("oid" => $orderId))->find();
        $bill = $loanbillModel->where(array("oid" => $orderId))->find();
        if (!$loanOrder || !$bill) {
            $this->error("order or bill does not exist");
        }
        $delayRate = C('PostPone_RATE');
        $needRepayMoney = $loanorderModel->calcRepayMoney($loanOrder);
        $needRepaymentTime = $loanorderModel->getOrderRepayTime($loanOrder);
        $needRepaymentDay = date("d-m-Y", $needRepaymentTime);

        //统计点击还款按钮次数
        $userModel = D("User");
        $userInfo = $userModel->getInfo(array("id" => $loanOrder['uid']));
        if ($userInfo) {
            $update = [];
            $update["reply_count"] = intval($userInfo['reply_count']) + 1;
            $userModel->updateInfo($userInfo['id'], $update);
        }
        $returnData=[
            "oid"=> $orderId,
            "needRepayMoney"=>$needRepayMoney,
            "needRepaymentTime"=>$needRepaymentTime,
            "needRepaymentDay"=>$needRepaymentDay,
            "bill"=>$bill
        ];
        if ($delay) {
            $delayDefaultDays = C('DELAY_DEFAULT_DAYS');
            $delayDefaultDays = $delayDefaultDays > 0 ? $delayDefaultDays : 7;
            $days = I("day", $delayDefaultDays);
            $delayDay = $days;
            $nextRepayDatetime = strtotime('+' . $delayDay . 'days', $bill["repayment_time"]);
            $hasOverdueFee = $loanOrder['money'] * floatval($loanOrder['overdue']) * 7;
            if ($nextRepayDatetime < time()) {
                $needRepayMoney = $needRepayMoney - $hasOverdueFee;
//                $this->assign("needRepayMoney", $needRepayMoney);
            }
            $overdueFee = $loanorderModel->calcOverDueFee($loanOrder);
            if ($overdueFee > 0 && $nextRepayDatetime > time()) {
                $needRepayMoney = $needRepayMoney - $overdueFee;
                if ($needRepayMoney < 0) $needRepayMoney = 0;
//                $this->assign("needRepayMoney", $needRepayMoney);
            }
            $delayFee = $days * $delayRate * $bill['money'];

//            $this->assign("needDelay", 1);
//            $this->assign("delayFee", $delayFee);
//            $this->assign("nextRepayDatetime", $nextRepayDatetime);
//            $this->assign("nextRepayDay", date('d-m-Y', $nextRepayDatetime));
            $returnData["delayFee"]= $delayFee;
            $returnData["nextRepayDatetime"]= $nextRepayDatetime;
            $returnData["nextRepayDay"]= date('d-m-Y', $nextRepayDatetime);
        }
//        $this->assign("bill", $bill);

        $this->ajaxReturn(array("status" => 200,
            "data" => $returnData,
            "message" => "Successfully"));
    }

    //Cashwallet绿  //EasyLoan 天蓝【darkblue】//goomony 橙色【orange】 //smart capital 橙色
    private function renderTemplate($delay)
    {
        if (in_array($_SERVER['HTTP_HOST'], ['m.bracha.top', 'm.casheasya.top', 'm.degge.top', 'm.rupeelixhgn.top', 'db.mxgeb8.top', 'm.koobp.top', 'india-easyloan.agsog.top'])) {
            if ($delay) { //蓝色 easyloan
                $this->display("darkblue_english_india/delay");
            } else {
                $this->display("darkblue_english_india/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.pocketlya.top', 'm.goomoneyass.top', 'm.ofabb.top', 'india-goldrupee.agsog.top'])) {
            if ($delay) { //goldrupee 橙色
                $this->display("orange_english_india/delay");
            } else {
                $this->display("orange_english_india/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.kreditbeea.top', 'm.goomoneyass.top', 'm.ofabb.top', 'india-goomoney.agsog.top'])) {
            if ($delay) { //GooMoney 黄色
                $this->display("yellow_english_india/delay");
            } else {
                $this->display("yellow_english_india/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.xagekt.top', 'india-samllcapital.agsog.top'])) {
            if ($delay) { //smart capital 紫色
                $this->display("purple_english_india/delay");
            } else {
                $this->display("purple_english_india/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.buddya.top', 'm.yindb12.top', 'india-hugocash.agsog.top','m.sjfjfn.top','m.ysdgz.top','m6.ysdgz.top','m7.ysdgz.top'])) {
            if ($delay) {//绿色HugoCash
                $this->display("green_english_india/delay");
            } else {
                $this->display("green_english_india/repay");
            }

        }else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['india-moneyview.agsog.top','m.vderg.top'])) {
            //粉色 moneyview
            $this->display("pink_english_india/repay");

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.aneaki.top', 'mexico-hugocash.agsog.top',"m.credmex.top","m.solprestamo.top",'m.skdnv.top','m.vknsdf.top'])) {
            if ($delay) {//绿色HugoCash西班牙
                $this->display("green_spanish_mexico/delay");
            } else {
                $this->display("green_spanish_mexico/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.goldrupeea.top', 'm.loanpalkwfg.top'])) {
            if ($delay) { //goldrupee 橙色
                $this->display("orange_Ind_Indonesian/delay");
            } else {
                $this->display("orange_Ind_Indonesian/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.quikaoas.top', 'm.smartcreditkdja.top'])) {
            if ($delay) {//purple 西班牙
                $this->display("quikaoas/delay");
            } else {
                $this->display("quikaoas/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.meconkicy.top', 'm.glbierb9.top', 'mexico-samllcapital.agsog.top','m.baubap.top','m.prestamorapidoen.top','m.weeuw.top','m.gldsa.top','m.sgbkss.xyz'])) {
            if ($delay) { //紫色 墨西哥SamllCapital
                $this->display("purple_spanish_mexico/delay");
            } else {
                $this->display("purple_spanish_mexico/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.lendluxepk.top', 'yellow.mxgeb8.top'])) {
            if ($delay) { //黄色goomoney
                $this->display("yellow_english_india/delay");
            } else {
                $this->display("yellow_english_india/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.fundwave.top', 'm.mxgeb10.top', 'm.glbierb11.top', 'mexico-goomoney.agsog.top','m.noro.top','m.flacash.top'])) {
            if ($delay) { //黄色GooMoney 西班牙文
                $this->display("yellow_spanish_mexico/delay");
            } else {
                $this->display("yellow_spanish_mexico/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.handkjsbcoan.top', 'm2.handkjsbcoan.top', 'mexico-easyloan.agsog.top','m.gegjl.top','m.prestamorapidoeasy.top','m.aegwlo.top'])) {
            if ($delay) { //EasyLoan 深蓝 西班牙墨西哥
                $this->display("darkblue_spanish_mexico/delay");
            } else {
                $this->display("darkblue_spanish_mexico/repay");
            }

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.koopaassn.top', 'm.mxgeb8.top', 'm.agoopa.top', 'mexico-goldrupee.agsog.top','m.superss.top','m.dinerbacano.top'])) {
            if ($delay) { //GoldRupee 西班牙 橙色
                $this->display("orange_spanish_columbia/delay");
            } else {
                $this->display("orange_spanish_columbia/repay");
            }
        }else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['mexico-moneyview.agsog.top','m.fehwe.top','m.gsadf.top'])) {
            //粉色moneyview
            $this->display("pink_spanish_mexico/repay");

        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.memxilosk.top', 'mexico-cashwallet.agsog.top','m.rapidos.top','m.profins.top'])) {
            //Cashwallet 浅蓝 西班牙 墨西哥
            $this->display("lightblue_spanish_mexico/repay");
        } else if (in_array(strtolower($_SERVER['HTTP_HOST']), ['m.moxikkga.top'])) {
            $this->display("lightblue_spanish/repay");
        } else {//cashwallet 绿色英语
            $this->display();
        }
    }

    public function nextpay()
    {
        $orderId = I("oid");
        if ($this->isPost()) {
            if (!$orderId) {
                $this->error("param order id is required");
            }
            $paytype = I("paytype");
            $loanorderModel = D("Loanorder");
            $loanbillModel = D("Loanbill");
            $loanbillDelayModel = D('LoanbillDelay');
            $loanOrder = $loanorderModel->where(array("oid" => $orderId))->find();
            $bill = $loanbillModel->where(array("oid" => $orderId))->find();
            if (!$loanOrder || !$bill) {
                $this->error("order or bill does not exist");
            }
            if ($loanOrder['status'] == 2 || $loanOrder['pending'] != 1) {
                $this->error("the order status does not allow operations");
            }
            $delayRate = C('PostPone_RATE');
            $processDelayData = $this->processDelay($bill, $delayRate, $loanOrder, $paytype);
            $response = $processDelayData["response"];
            if ($response) {
                if ($response['code'] == 200) {
                    $payUrl = $response['pay_url'];
                    if (preg_match("#\/qrcode\/([a-zA-ZA-Z0-9]+)\/#", $payUrl, $matches)) {
                        if ($matches && count($matches) > 1) {
                            $upid = $matches[1];
                            $loanbillDelayModel->where(["pay_id" => $processDelayData['orderId']])->save(array("upid" => $upid));
                        }
                    }
                    $this->success($response['pay_url']);
                } else {
                    $this->error("Payment Order Submit Failed:" . $response['msg']);
                }
            } else {
                $this->success(U('/Repay/fullRepay', array("oid" => $orderId)));
            }
        }
    }

    public function payUrl()
    {
        $orderId = I("oid");
        if ($this->isPost()) {
            if (!$orderId) {
                $this->setAjaxResponse(300, '', "param order id is required");
            }
            $paytype = I("paytype", 0);
            $loanorderModel = D("Loanorder");
            $loanbillModel = D("Loanbill");
            $payorderModel = D("Payorder");
            $paymentModel = D("Payment");
            $loanOrder = $loanorderModel->where(array("oid" => $orderId))->find();
            $bill = $loanbillModel->where(array("oid" => $orderId))->find();
            if (!$loanOrder || !$bill) {
                $this->error(403, '', "order or bill does not exist");
            }
            //统计拉单次数
            $userModel = D("User");
            $userInfo = $userModel->getInfo(array("id" => $loanOrder['uid']));
            if ($userInfo) {
                $update = [];
                $update["get_order"] = intval($userInfo['get_order']) + 1;
                $userModel->updateInfo($userInfo['id'], $update);
            }
            $delayRate = C('PostPone_RATE');
            $daishouSwitch = C("DAISHOU_SUBMIT_TO_PAYER");
            if (intval($daishouSwitch) == 1) { //提交到代付

                $response = $paymentModel->repayment($loanOrder, $bill, $paytype);
                if ($response['code'] == 200) {
                    $this->setAjaxResponse(200, $response['pay_url'], 'success');
                } else {
                    $this->setAjaxResponse(500, "Payment Order Submit Failed:" . $response['msg']);
                }
            } else {
                $this->setAjaxResponse(400, '', 'The system is busy, please try again later');
            }
        }else{
            $this->error("not support method");
        }

    }

    public function payData()
    {
        $orderId = I("oid");
        if ($this->isPost()) {
            if (!$orderId) {
                $this->setAjaxResponse(300, '', "param order id is required");
            }
            $paytype = I("paytype", 0);
            $loanorderModel = D("Loanorder");
            $loanbillModel = D("Loanbill");
            $payorderModel = D("Payorder");
            $paymentModel = D("Payment");
            $loanOrder = $loanorderModel->where(array("oid" => $orderId))->find();
            $bill = $loanbillModel->where(array("oid" => $orderId))->find();
            if (!$loanOrder || !$bill) {
                $this->error(403, '', "order or bill does not exist");
            }
            //统计拉单次数
            $userModel = D("User");
            $userInfo = $userModel->getInfo(array("id" => $loanOrder['uid']));
            if ($userInfo) {
                $update = [];
                $update["get_order"] = intval($userInfo['get_order']) + 1;
                $userModel->updateInfo($userInfo['id'], $update);
            }
            $delayRate = C('PostPone_RATE');
            $daishouSwitch = C("DAISHOU_SUBMIT_TO_PAYER");
            if (intval($daishouSwitch) == 1) { //提交到代付

                $response = $paymentModel->repayment($loanOrder, $bill, $paytype);
                if ($response['code'] == 200) {
                    $this->setAjaxResponse(200, $response['pay_url'].'&next=pay', 'success');
                } else {
                    $this->setAjaxResponse(500, "Payment Order Submit Failed:" . $response['msg']);
                }
            } else {
                $this->setAjaxResponse(400, '', 'The system is busy, please try again later');
            }
        }else{
            $this->error("not support method");
        }

    }

    public function orders()
    {
        $loanorderModel = D("Loanorder");
        $page = I("page", 1);
        $pageSize = I("pageSize", 100);

        $userInfo = $this->isLogin();
        if (!$userInfo) {
            $this->setAjaxResponse(403, '', 'User not logged in');
        }
//        $list = $loanorderModel->getNoneList($userInfo["id"], [0, 1, 2, 3, 4, 5, 6], [1]); //0待还款,1逾期未还，2已还款，3逾期还款，4续期中,5部分还款，6逾期部分还款
        $loanbillModel = D("Loanbill");
        $offset = ($page - 1) * $pageSize;
        $list = $loanorderModel->where(['uid' => $userInfo["id"], 'pending' => array('IN', '1')])->order('start_time desc,add_time desc')->limit("$offset,$pageSize")->select();
//        $list = $loanbillModel->where(['uid' => $userInfo["id"]])->select();

        $orders = [];
        foreach ($list as $key => $value) {
            $orders[$key]['id'] = $list[$key]['id'];
            $orders[$key]['oid'] = $list[$key]['oid'];
            $orders[$key]['time'] = $list[$key]['time'];
            $orders[$key]['interest'] = $list[$key]['interest'];
            $orders[$key]['reply_rate'] = $list[$key]['reply_rate'];
            $orders[$key]['start_time'] = $list[$key]['start_time'];
            $orders[$key]['addtime'] = $list[$key]["add_time"];
            $orders[$key]['money'] = $list[$key]["money"];
            $orders[$key]['delay'] = $list[$key]['delay'];

            $orders[$key]['status'] = $list[$key]['status'];
            $orders[$key]['repay_interest'] = $loanorderModel->calcRepayFee($list[$key]);
            $orders[$key]['repay_money'] = floor($list[$key]["money"] + $orders[$key]['repay_interest']);
            $orders[$key]['repayment_time'] = $loanorderModel->getOrderRepayTime($list[$key]);
            $orders[$key]['overdue_day'] = 0;
            if ($orders[$key]['status'] != 2) {
                if ($orders[$key]['delay']) $orders[$key]['status'] = 4;
                $lastRepayTime = $orders[$key]['repayment_time'];
                if ($lastRepayTime < time()) {
                    $orders[$key]['status'] = 1;
                    $overFee = $loanorderModel->calcOverDueFee($list[$key]);
                    $orders[$key]['repay_money'] += floor($overFee);
                    $orders[$key]['overFee'] = floor($overFee);
                    $orders[$key]['overdue_day'] = $loanorderModel->calcOverDueDay($orders[$key]['repayment_time']);
                }
                $orders[$key]['repay_money'] += floor($list[$key]['delay_unpaid']); //延期未支付的费用
                $orders[$key]['repay_money'] -= floor($list[$key]['repaid_money']); //减去已还款金额
            }
            $orders[$key]['interest'] = $loanorderModel->calcOrderInterest($orders[$key]);
            $orders[$key]['get_money'] = floor($orders[$key]['money'] - $orders[$key]['interest']);
            $orders[$key]['currency_symbol'] = C('CURRENCY_SYMBOL');
            $orders[$key]['next_loan'] = C('Next_Loan');
            $orders[$key]['approval_rate'] = C('Approval_Rate');
            $orders[$key]['next_loan_period'] = C('Next_Loan_Period');
        }
      /*  $returnData = array(
            "orders" => [$orders[0]]
        );*/
        $returnData = array(
            "orders" => $orders
        );

        $this->ajaxReturn(array("status" => 200,
            "data" => $returnData,
            "message" => "Successfully"));
        exit;
    }



    public function repayProof()
    {
//        $userInfo = $this->isLogin();
//        if (!$userInfo) {
//            $this->error("User not logged in");
//        }
        if ($this->isPost()) {
            $oid = I('oid');
            $utr = I('utr');
            $utrImage = I('utr_image');
            $repayImage = I('repay_image');
            if (!$oid) {
                $this->error("orderID is must");
            }
            $trans = M();
            $trans->startTrans();
            $loanbillModel = D('Loanbill');
            $loanorderModel = D('loanorder');
            $loanOrder = $loanorderModel->where(array('oid' => $oid))->find();
            if (!$loanOrder) {
                $this->error("order not found");
            }
            try {
                $repayProof = array(
                    'utr' => $utr,
                    'utr_image' => $utrImage,
                    'repay_image' => $repayImage
                );

                $billStatus = $loanbillModel->where(array('oid' => $oid))->save($repayProof);
                if (!$billStatus) {
                    throw new Exception('Couldn\'t save bill status');
                }
                $loanStatus = $loanorderModel->where(array('oid' => $oid))->save($repayProof);
                if (!$loanStatus) {
                    throw new Exception('Couldn\'t save loan order status:');
                }
                $payProofModel = D("PayProof");
                $infoModel = D("Info");
                $userinfo = $infoModel->where(array('uid' => $loanOrder['uid']))->find();
                $identity = json_decode($userinfo['identity'], true);
                $payOrderInfo = array(
                    "uid" => $userinfo['uid'],
                    "uname" => $userinfo['mobile'],
                    "realname" => $identity['name'],
                    "idcard" => $identity['idcard'],
                    "order_id" => $loanOrder['oid'],
                    "pay_id" => time(),
                    "type" => 2,
                    "money" => $loanOrder['money'],
                    "utr" => $utr,
                    "utr_image" => $utrImage,
                    "repay_image" => $repayImage,
                    "add_time" => time(),
                );
                $payoreerId = $payProofModel->add($payOrderInfo);
                if (!$payoreerId) {
                    throw new Exception("Couldn\'t save payorder info");
                }
                $trans->commit();
                $this->success("Successful");
            } catch (Exception $e) {
                $trans->rollback();
                $this->error("Operation failed:" . $e->getMessage());
            }

        } else {
            $this->error("wrong parameter");
        }

    }

    public function repayment()
    {
        $billId = I("id");
        if (!$billId) {
            $this->error("Incorrect billing parameters");
        }
        $payorderModel = D("Payorder");
        $loanbillModel = D("Loanbill");
        $bill = $loanbillModel->where(array("id" => $billId))->find();
        if (!$bill) {
            $this->error("Bill does not exist");
        }
        if ($bill["status"] == 2 || $bill["status"] == 3) {
            $this->error("Current bill paid");
        }
        if ($bill["status"] == 4) {
            $this->error("Current bill has expired");
        }
        $userInfo = $this->isLogin();
        //$billMoney = toMoney($bill["money"] + $bill["interest"] + $bill["overdue"]);
        $billMoney = toMoney($bill["money"] + $bill["overdue"]);
        $order = $payorderModel->newOrder($userInfo["id"], $billMoney, array($billId));
        if (!$order) {
            $this->error("Failed to create payment order");
        }
        //var_dump($bill);
        $this->assign("data", $order);
        $this->display();
        //$this->redirect("Pay/alipay", array("order" => $order));
        exit(0);
    }


    /**
     * 订单延期
     * @return void
     */
    public function postpone()
    {
        $billId = I("oid");
        if (!$billId) {
            $this->error("Incorrect billing parameters");
        }
        $user = $this->isLogin();
        if (!$user || empty($user)) {
            $this->error("User is not logged in");
        }
        $days = I("day", 7);
        $loanorderModel = D("loanorder");
        $loanbillModel = D("loanbill");
        $bill = $loanbillModel->where(array('id' => $billId))->find();
        if ($bill) {

            $delayRate = C('PostPone_RATE');
            $delayInfo = array('delay' => 1, 'delay_day' => array("exp", "delay_day+$days"), 'delay_rate' => $delayRate, 'status' => 4);
            $trans_result = true;
            $trans = M();
            $oid = $bill['oid'];
            $trans->startTrans();
            try {
                $billStatus = $loanbillModel->where(array('id' => $billId))->save($delayInfo);
                if (!$billStatus) {
                    throw new Exception('Couldn\'t save bill status');
                }
                $loanStatus = $loanorderModel->where(array('oid' => array('in', $oid)))->save($delayInfo);
//                echo $loanorderModel->getLastSql();
                if (!$loanStatus) {
                    throw new Exception('Couldn\'t save loan order status:');
                }
                $trans->commit();
                $this->success("Deferred success");
            } catch
            (Exception $e) {
                $trans->rollback();
                $this->error("Deferred Failed" . $e->getMessage());
            }

        } else {
            $this->error("Order not found");
        }
    }

    public function ajaxDelay()
    {
        $billId = I("oid");
        if (!$billId) {
            $this->setAjaxResponse(300, "", "Incorrect billing parameters");
        }
        $user = $this->isLogin();
        if (!$user || empty($user)) {
            $this->setAjaxResponse(300, "", "User is not logged in");
        }
        $days = I("day", 7);
        $loanorderModel = D("loanorder");
        $loanbillModel = D("loanbill");
        $bill = $loanbillModel->where(array('id' => $billId))->find();
        $daishouSwitch = C("DAISHOU_SUBMIT_TO_PAYER");

        $delayRate = C('PostPone_RATE');
        $delayDefaultDays = C('DELAY_DEFAULT_DAYS');
        $delayDefaultDays = $delayDefaultDays > 0 ? $delayDefaultDays : 7;
        $days = I("day", $delayDefaultDays);
        $nextRepayDay = strtotime('+' . $bill["delay_day"] . 'days', $bill["repayment_time"]);
        $loanbillDelayModel = D('LoanbillDelay');
        $delayFee = $days * $delayRate * $bill['money'];
        $orderId = $loanbillDelayModel->newOrderId($billId);

        if ($bill) {
            if ($daishouSwitch == 1) { //代收
                $delayInfo = array(
                    "billid" => $bill['id'],
                    "oid" => $bill['oid'],
                    "uid" => $bill['uid'],
                    "pay_id" => $orderId,
                    "delay_day" => $days,
                    "loan_add_time" => $bill['add_time'],
                    "loan_repayment_time" => $nextRepayDay,
                    "delay_rate" => $delayRate,
                    "delay_fee" => $delayFee,
                    "delay_pay_status" => 0,
                    "created_at" => time(),
                    "updated_at" => time(),
                );
                $delayId = $loanbillDelayModel->add($delayInfo);
                if ($delayId) {
                    $pname = isset($user['firstname']) ? $user['firstname'] : "";
                    $pname .= isset($user['lastname']) ? $user['lastname'] : "";
                    $pname = strlen($pname) > 0 ? $pname : "cashgo";
                    $accountInfo = array(
                        'name' => "DELAY FEE",
                        'pname' => $pname,
                        'pemail' => $user['email'],
                        'phone' => $user['telnum'],
                        'ccy_no' => C('CURRENCY'),
                        'bank_code' => 'UPI'
                    );
                    $delayInfo = array_merge($delayInfo, $accountInfo);
                    $paymentModel = D('Payment');
                    $response = $paymentModel->payDelayFee($delayInfo);
                    if ($response['code'] == 200) {
                        $this->setAjaxResponse(200, $response['pay_url'], "success");
                    } else {
                        $this->setAjaxResponse(500, "", "Payment Order Submit Failed:" . $response['msg']);
                    }
                    $this->setAjaxResponse(500, "", "Payment Order Submit Failed: create failed");
                }
            } else {
                $delayRate = C('PostPone_RATE');
                $delayInfo = array('delay' => 1,
                    'delay_day' => array("exp", "delay_day+$days"),
//                    'delay_unpaid' => array("exp", "delay_unpaid+$delayFee"),
                    'delay_rate' => $delayRate,
                    'status' => 4);
                $trans_result = true;
                $trans = M();
                $oid = $bill['oid'];
                $trans->startTrans();
                try {
                    $billStatus = $loanbillModel->where(array('id' => $billId))->save($delayInfo);
                    if (!$billStatus) {
                        throw new Exception('Couldn\'t save bill status');
                    }
                    $loanStatus = $loanorderModel->where(array('oid' => array('in', $oid)))->save($delayInfo);
                    if (!$loanStatus) {
                        throw new Exception('Couldn\'t save loan order status:');
                    }
                    $trans->commit();
                    $this->setAjaxResponse(200, "", "Deferred success");
                } catch (Exception $e) {
                    $trans->rollback();
                    $this->setAjaxResponse(500, "", "Deferred Failed" . $e->getMessage());
                }
            }
        } else {
            $this->setAjaxResponse(404, "", "Order not found");
        }
    }

    protected function processDelay($bill, $delayRate, $loanOrder = null, $paytype = -1)
    {

        $delayDefaultDays = C('DELAY_DEFAULT_DAYS');
        $delayDefaultDays = $delayDefaultDays > 0 ? $delayDefaultDays : 7;
        $days = I("day", $delayDefaultDays);
        $delayDay = intval($days);
        $nextRepayDay = strtotime('+' . $delayDay . 'days', $bill["repayment_time"]);

        $delayFee = $days * $delayRate * $bill['money'];
        $paymentModel = D('Payment');
        $loanbillDelayModel = D('LoanbillDelay');
        $orderId = $loanbillDelayModel->newOrderId($bill["id"]);
        $delayInfo = array(
            "billid" => $bill['id'],
            "uid" => $bill['uid'],
            "oid" => $bill['oid'],
            "pay_id" => $orderId,
            "delay_day" => $days,
            "loan_add_time" => $bill['add_time'],
            "loan_repayment_time" => $nextRepayDay,
            "delay_rate" => $delayRate,
            "delay_fee" => $delayFee,
            "delay_pay_status" => 0,
            "created_at" => time(),
            "updated_at" => time(),
        );
        $delayId = $loanbillDelayModel->add($delayInfo);

        $response = null;
        if ($delayId) {
            $payRecordModel = D('PayRecord');
            $payRecordInfo = array(
                "oid" => $bill['oid'],
                "action" => 1,
                "pay_id" => $orderId,
                "mark" => '延期付款新建付款单',
                "type" => 1,
                "sid" => 0,
                "ip" => get_client_ip(),
                "create_at" => time(),
                "updated_at" => time()
            );
            $payRecordModel->add($payRecordInfo);

            $daishouSwitch = C("DAISHOU_SUBMIT_TO_PAYER");
            if (intval($daishouSwitch) == 1) { //提交到代付
                $user = $this->isLogin();
                if (!$user) {
                    $userModel = D("User");
                    $user = $userModel->where(['id' => $bill['uid']])->field('firstname,lastname,email,telnum,quota')->find();
                }
                $pname = isset($user['firstname']) ? $user['firstname'] : "";
                $pname .= isset($user['lastname']) ? $user['lastname'] : "";
                $pname = strlen($pname) > 0 ? $pname : "cashwallet";
                $nameMatch = [];
                preg_match_all("/([^\x{4e00}-\x{9fa5}])/u", $pname, $nameMatch);
                $pname = implode('', $nameMatch[0]);
                if (strlen($pname) <= 1) {
                    $pname = 'cash wallet' . rand(1, 99);
                }
                $loanData = [];
                if ($loanOrder) $loanData = json_decode($loanOrder['data'], true);
                $accountInfo = array(
                    'name' => "DELAY FEE",
                    'pname' => 'VivaCred',
                    'pemail' => $user['email'] ? $user['email'] : 'pay@vivacred.com',
                    'phone' => $user['telnum'] ? $user['telnum'] : $loanData['phoneNumber'],
                    'ccy_no' => C('CURRENCY'),
                    'bank_code' => 'UPI'
                );
                $delayInfo = array_merge($delayInfo, $accountInfo);
                $response = $paymentModel->payDelayFee($delayInfo, $paytype);
            } else { //不提交到代付
                $delayInfo = array('delay' => 1,
                    'delay_day' => array("exp", "delay_day+" . $delayDay),
                    'delay_rate' => $delayRate,
                    'delay_num' => array("exp", "delay_num+1"),
//                    'delay_unpaid' => array("exp", "delay_unpaid+" . $delayFee)
                );
                $trans = M();
                $trans->startTrans();
                $loanbillModel = D('Loanbill');
                $loanorderModel = D('loanorder');
                try {
                    $billDelayInfo = array_merge($delayInfo, array('repayment_time' => $nextRepayDay));
                    $billStatus = $loanbillModel->where(array('id' => $bill['id']))->save($billDelayInfo);
                    if (!$billStatus) {
                        throw new Exception('Couldn\'t save bill status');
                    }
                    $loanStatus = $loanorderModel->where(array('oid' => array('in', $bill['oid'])))->save($delayInfo);
                    if (!$loanStatus) {
                        throw new Exception('Couldn\'t save loan order status:');
                    }
                    $trans->commit();
                } catch (Exception $e) {
                    $trans->rollback();
                }
            }
        }
        return array(
            "response" => $response,
            "nextRepayDay" => $nextRepayDay,
            "days" => $days,
            'orderId' => $orderId
        );
    }


    /**
     * 延期
     * @return void
     */
    public function delay()
    {
        $billId = I("bid");
        if (!$billId) {
            $this->error("Incorrect billing parameters");
        }
        $user = $this->isLogin();
        if (!$user || empty($user)) {
            $this->error("User is not logged in");
        }
        $loanorderModel = D("loanorder");
        $loanbillModel = D("loanbill");
        $delayRate = C('PostPone_RATE');
        $bill = $loanbillModel->where(array('id' => $billId))->find();

        if ($this->isPost()) {
            $processDelayInfo = $this->processDelay();
            $response = $processDelayInfo["response"];
            $nextRepayDay = $processDelayInfo["nextRepayDay"];
            $days = $processDelayInfo["days"];
            if ($response && $response['code'] == 200) {
                redirect($response['pay_url']);
            } else {
                $this->error("Payment Order Submit Failed:" . $response['msg']);
            }
        }
        $this->assign("nextRepayDay", $nextRepayDay);
        $this->assign("delayDay", $days);
        $this->assign("delayRate", $delayRate);
        $this->assign('bill', $bill);
        $this->display();
    }


    public function test()
    {
        $paymentModel = D('Payment');
        $paymentModel->sendBotMessage("测试");
        echo "ceshi";
        exit;
    }
}