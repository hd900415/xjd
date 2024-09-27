<?php

class GfpayAction extends Action
{

    /**
     * 代付回调
     * @return void
     */
    private function _beforeNotifyAction($type = 1)
    {
        $params = I('post.');
        if (empty($params)) {
            $params = I('put.');
        }
        if (empty($params)) {
            $params = I('get.');
        }
        if (empty($params)) {
            $params = I('request.');
        }
        if (empty($params)) {
            exit('error');
        }
        $this->sendMessage($type, $params);
        if (!$params['out_trade_no']) { //不合法的数据
            exit('out_trade_no not exist');  //返回失败 继续补单
        }
        $paymentModel = D('Payment');
        $merchant = $paymentModel->getMerchant();
        unset($params['_URL_']);
        $sign = $paymentModel->md5Sign($params, $merchant['app_secret']);
        if ($sign != $params['sign']) {
            exit('sign error');
        }
        return $params;
    }

    public function pay_notify()
    {
        $params = $this->_beforeNotifyAction(2);
        $loanorderModel = D("Loanorder");
        $loanorder = $loanorderModel->where(array("oid" => $params["out_trade_no"]))->find();
        if (empty($loanorder)) {
            exit('Order does not exist');
        }
        $orderInfo = array(
            'notify' => json_encode($params, JSON_UNESCAPED_UNICODE),
        );
        if ($params['trade_status'] == 'TRADE_SUCCESS') {
            //统计支付成功的
            $userModel = D("User");
            $userInfo = $userModel->getInfo(array("id" => $loanorder['uid']));
            if($userInfo){
                $update=[];
                $update["pay_count"]=intval($userInfo['pay_count'])+1;
                $userModel->updateInfo($userInfo['id'],$update);
            }
            //成功执行的操作
            $loanStatus = array("pending" => 1, "status" => 0);
            $loanorderModel->where(array("oid" => $params["out_trade_no"]))->save($loanStatus);
            $this->createOrderBill($loanorder, $loanorderModel); //创建账单
        } else {
            $orderInfo['pending'] = -3; //上游拒绝
            $loanbillModel = D("Loanbill");
//            $loanbillModel->where(array("oid" => $params["out_trade_no"]))->delete();
            $loanbillModel->where(array("oid" => $params["out_trade_no"]))->save(array('status' => -4));
        }
        $loanorderModel->where(array("oid" => $params["out_trade_no"]))->save($orderInfo);
        exit('success');
    }

    public function sendMessage($type, $requestData)
    {
        $paymentModel = D('Payment');
        $merchant = $paymentModel->getMerchant();
        if ($type == 1) {
            $url = $_SERVER['HTTP_HOST'];
            $info = "Cash 收到收款回调日志";
        } else {
            $url = $_SERVER['HTTP_HOST'];
            $info = "Cash 收到出款回调日志";
        }
        $messages = "***" . $info . "***:\n被请求URL:" . $url . "\n商户ID:" . $merchant['id'] . "\n密钥:" . $merchant['app_secret'] . "\n";
        $messages .= "请求体:" . json_encode($requestData) . "\n";
//        $messages .= "响应体:" . $responseText . "\n";

        $paymentModel->sendBotMessage($messages);
    }

    public function createOrderBill($order, $loanorderModel = null)
    {
        $data = json_decode($order["data"], true);
        $loanbillModel = D("Loanbill");
        $starttime = strtotime(date("Y-m-d"));

        if ($order["timetype"] == 0) {
            if (!$loanorderModel) $loanorderModel = D("Loanorder");
            $inserest = $loanorderModel->calcOrderInterest($order); //借款利息
            $replay_inserest = $loanorderModel->calcRepayFee($order); //还款利息
            $endtime = $loanorderModel->getOrderRepayTime($order);
            $fastrepayment = $repaymenttime = $endtime;
            $billData = array("uid" => $order["uid"], "toid" => $order["id"], "oid" => $order["oid"], "billnum" => 1, "money" => $order["money"], "interest" => toMoney($inserest), 'repay_interest' => toMoney($replay_inserest), "overdue" => 0, "repayment_time" => $repaymenttime, "status" => 0, "add_time" => time());
            $loanbillModel->add($billData);
        } else {
            $i = 0;
            while ($i < intval($order["time"])) {
                $repayment_time = $data["fastrepayment"];
                if ($i > 0) {
                    $repayment_time = strtotime("+" . $i . " Month", $repayment_time);
                }
                $inserest = intval(floatval($order["money"]) * floatval($order["interest"]));
                $replay_inserest = $loanorderModel->calcRepayFee($order); //还款利息
                $billData = array("uid" => $order["uid"], "toid" => $order["id"], "oid" => $order["oid"], "billnum" => $i + 1, "money" => toMoney($order["money"] / intval($order["time"])), "interest" => toMoney($inserest), 'repay_interest' => toMoney($replay_inserest), "overdue" => 0, "repayment_time" => $repayment_time, "status" => 0, "add_time" => time());
                $loanbillModel->add($billData);
                $i = $i + 1;
            }
            $billMoney = intval($order["time"] * toMoney(floatval($order["money"]) * floatval($order["interest"])) + intval($order["time"]) * toMoney($order["money"] / intval($order["time"])));
            $deviation = $billMoney - $order["money"];
            if ($deviation > 0) {
            }
            if ($deviation < 0) {
            }
        }
    }

    public function pay_return()
    {

    }

    //代收
    public function repay_notify()
    {
        $params = $this->_beforeNotifyAction(1);
        $payorderModel = D("Payorder");
        $id = $params['out_trade_no']; //需要充值的ID 或订单号 或用户名
        $info = $payorderModel->where(array("oid" => $id))->find();
        if (!$info) {
            exit("order not found!");
        }
        if ($params["trade_status"] == "TRADE_SUCCESS") {
            if ($info && !$info["status"]) {
                $payorderInfo = array("status" => 1, "pay_time" => time(), 'tomoney' => $params['money'], 'upid' => $params["trade_no"], 'notify' => json_encode($params, JSON_UNESCAPED_UNICODE));
                if($params['api_no']) $payorderInfo['api_trade_no']=$params['api_no'];
                if ($payorderModel->where(array("oid" => $id, "status" => array('NOT IN', '1')))->save($payorderInfo)) {
                    $this->updateLoanOrderRepay($info, $params);
//                    $billList = json_decode($info["billlist"], true);
//                    if ($billList) {
//                        $this->updateBillAndOrderRepay($billList, $info, $params);
//                    } else {
//
//                    }
                }
            }
            //统计支付成功的
            $userModel = D("User");
            $userInfo = $userModel->getInfo(array("id" => $info['uid']));
            if($userInfo){
                $update=[];
                $update["pay_count"]=intval($userInfo['pay_count'])+1;
                $userModel->updateInfo($userInfo['id'],$update);
            }
            exit('success');
        } else {
            $payorderInfo = array("status" => -1, 'upid' => $params["trade_no"], 'notify' => json_encode($params, JSON_UNESCAPED_UNICODE));
            if($params['api_no']) $payorderInfo['api_trade_no']=$params['api_no'];
            $payorderModel->where(array("oid" => $id))->save($payorderInfo);
        }
        exit('error');

    }

    public function updateLoanOrderRepay($info, $params)
    {
        $loanbillModel = D("Loanbill");
        $loanorderModel = D("Loanorder");
//        if ($info['pay_type'] == 1) { //部分还款
        $loanOrder = $loanorderModel->where(array("oid" => $info["loid"]))->find();
        $needRepayMoney = $loanorderModel->calcRepayMoney($loanOrder);
        if ($needRepayMoney <= $params['money'] + $loanOrder['repaid_money']) {  //判读总订单金额已经大于当前订单，如果是则表示已全部付清
            $loanorderModel->where(array("oid" => $info["loid"]))->save(array("status" => 2, 'repaid_money' => array("exp", "repaid_money+" . $params['money'])));
            $loanbillModel->where(array("oid" => $info["loid"]))->save(array("status" => 2, 'repaid_money' => array("exp", "repaid_money+" . $params['money'])));
        } else {
            $loanorderModel->where(array("oid" => $info["loid"]))->save(array('repaid_money' => array("exp", "repaid_money+" . $params['money'])));
        }
//        } else {
//            $loanorderModel->where(array("oid" => $info["loid"]))->save(array("status" => 2, 'repaid_money' => $params['money']));
//            $loanbillModel->where(array("oid" => $info["loid"]))->save(array("status" => 2, 'repaid_money' => $params['money']));
//        }

    }

    //更新订单支付状态和支付
    public function updateBillAndOrderRepay($billList, $info, $params)
    {
        if ($billList) {
            $loanbillModel = D("Loanbill");
            $loanorderModel = D("Loanorder");
            $i = 0;
            while ($i < count($billList)) {
                $bill = $loanbillModel->where(array("id" => $billList[$i]))->find();
                if ($bill && $bill["status"] != 2 && $bill["status"] != 3 && $bill["status"] != -4) {
                    $tmp = array("status" => 2, "repay_time" => time());
                    if ($info["pay_type"] == 1) { //部分还款
//                        $tmp["status"] = 5;
                        $tmp["repaid_money"] = $params['money'];
                    }
                    if ($bill["status"] == 1) {
//                        $tmp["status"] = 3;
                        if ($info["pay_type"] == 1) { //部分还款
//                            $tmp["status"] = 6;
                            $tmp["repaid_money"] = $params['money'];
                        }
                    }

                    $loanbillModel->where(array("id" => $billList[$i]))->save($tmp);
                    $loanOrder = $loanorderModel->where(array("oid" => $info["loid"]))->find();
                    $needRepayMoney = $loanorderModel->calcRepayMoney($loanOrder);
                    if ($needRepayMoney <= $params['money'] + $loanOrder['repaid_money']) {  //判读总订单金额已经大于当前订单，如果是则表示已全部付清
                        $loanorderModel->where(array("oid" => $info["loid"]))->save(array("status" => 2, 'repaid_money' => array("exp", "repaid_money+" . $params['money'])));
                        $loanbillModel->where(array("oid" => $info["loid"]))->save(array("status" => 2, 'repaid_money' => array("exp", "repaid_money+" . $params['money'])));
                    } else {
                        $loanorderModel->where(array("oid" => $info["loid"]))->save(array('repaid_money' => array("exp", "repaid_money+" . $params['money'])));
                    }
//                    if ($info["pay_type"] != 1) {
//                        if (!$loanbillModel->where(array("toid" => $bill["toid"], "status" => array("IN", "0,1,5,6")))->count()) {
//                            $loanorderModel->where(array("id" => $bill["toid"]))->save(array("status" => 2, "repaid_money" => $params['money']));
//                        }
//                    } else {
//                        $loanOrder = $loanorderModel->where(array("oid" => $bill["oid"]))->find();
//                        $needRepayMoney = $loanorderModel->calcRepayMoney($loanOrder);
//                        $status = 2;
//                        if ($needRepayMoney <= 0 || $params['money'] >= $needRepayMoney) {
//                            $status = 5;
//                        }
//                        $loanorderModel->where(array("id" => $bill["toid"]))->save(array("status" => $status, 'repaid_money' => array("exp", "repaid_money+" . $params['money'])));
//                    }

//                            $loanorderModel->where(array("id" => $bill["toid"]))->save(array("status" =>2)); //已还清
                }
                $i = $i + 1;
            }
        }
    }

    public function repay_return()
    {

    }

    public function delay_notify()
    {
        $params = $this->_beforeNotifyAction(1);
        $loanbillDelayModel = D('LoanbillDelay');
        $delayOrder = $loanbillDelayModel->where(array("pay_id" => $params["out_trade_no"]))->find();
        if (empty($delayOrder)) {
            exit('Order does not exist');
        }
        if ($params['trade_status'] == 'TRADE_SUCCESS') {
            //成功执行的操作
            $delayInfo=array('delay_pay_status' => 2, 'updated_at' => time(), 'tomoney' => $params['money'], 'upid' => $params["trade_no"]);
            if($params['api_no']) $delayInfo['api_trade_no']=$params['api_no'];
            $changeStatus = $loanbillDelayModel->where(array("pay_id" => $params["out_trade_no"], "delay_pay_status" => array("NOT IN", '2')))
                ->save($delayInfo);
            if ($changeStatus) {
                if ($delayOrder['delay_fee'] <= $params['money']) {
                    $flag = $this->modifyDelayOrderBill($delayOrder);
                }
                exit('success');
//                if ($flag) {
//                    exit('success');
//                } else {
//                    exit('change error');
//                }
            } else {
                exit('error');
            }
        } else {
            $delayInfo=array('delay_pay_status' => -2, 'updated_at' => time(), 'upid' => $params["trade_no"]);
            if($params['api_no']) $delayInfo['api_trade_no']=$params['api_no'];
            $changeStatus = $loanbillDelayModel->where(array("pay_id" => $params["out_trade_no"]))->save($delayInfo);
            exit('success');
        }
        exit('success');
    }

    public function delay_return()
    {

    }

    private function modifyDelayOrderBill($delayOrder, $delayDay = 7)
    {
        $delayInfo = array('delay' => 1,
            'delay_day' => array("exp", "delay_day+" . $delayOrder['delay_day']),
            'delay_rate' => $delayOrder['delay_rate'],
            'delay_num' => array("exp", "delay_num+1")
        );
        $trans = M();
        $trans->startTrans();
        $loanbillModel = D('Loanbill');
        $loanorderModel = D('loanorder');
        $loanbillDelayModel = D('LoanbillDelay');
        try {
            $billDelayInfo = array_merge($delayInfo, array('repayment_time' => array('exp', 'repayment_time+' . (86400 * $delayOrder['delay_day']))));
            $billStatus = $loanbillModel->where(array('id' => $delayOrder['billid']))->save($billDelayInfo);
            if (!$billStatus) {
                throw new Exception('Couldn\'t save bill status');
            }
            $loanBill = $loanbillModel->where(array('id' => $delayOrder['billid']))->find();
            if ($loanBill['repayment_time'] > time() && $loanBill['status'] == 1) { //续期之后，最终还款时间大于当前时间，则更新状态为续期
                $loanbillModel->where(array('id' => $delayOrder['billid']))->save(['status' => 4]);
                $delayInfo['status'] = 4;
            }
            $loanStatus = $loanorderModel->where(array('oid' => array('in', $delayOrder['oid'])))->save($delayInfo);
            if (!$loanStatus) {
                throw new Exception('Couldn\'t save loan order status:');
            }
            $trans->commit();
        } catch (Exception $e) {
            $trans->rollback();
            return false;
        }
        return true;
    }
}