<?php

class LoanorderModel extends RelationModel
{
    protected $_link = array('User' => array('mapping_type' => BELONGS_TO, 'foreign_key' => 'uid', 'mapping_name' => 'user'));

    public function newOrder($data = array())
    {
        if (!$data) {
            return false;
        }
        $oid = date("YmdHis") . rand(10000, 99999) . substr($data["uid"], 0, 1) . rand(0, 9) . $data["loantype"];
        return $oid;
    }

    public function getNoneList($uid = 0, $status = array(), $pendStatus = array())
    {
        if (!$uid) {
            return false;
        }
        if (!empty($pendStatus)) {
            $pending = array("IN", implode(",", $pendStatus));
            $data = $this->where(array("uid" => $uid, "status" => 0, "pending" => $pending))->order("add_time Desc")->select();
        } else {
            $data = $this->where(array("uid" => $uid, "status" => 0))->order("add_time Desc")->select();
        }
        $loanbillModel = D("Loanbill");
        $i = 0;
        while ($i < count($data)) {
            $mstatus = array("IN", "0,1");
            if (!empty($status)) {
                $mstatus = array("IN", implode(",", $status));
            }

            $bill = $loanbillModel->where(array("status" => $mstatus, "toid" => $data[$i]["id"]))->order("repayment_time DESC")->find();
            //$bill["allmoney"] = toMoney($bill["money"] + $bill["interest"] + $bill["overdue"]);
            $bill["allmoney"] = toMoney($bill["money"] + $bill["overdue"]);
            if ($data[$i]["timetype"] == 1) {
                $bill["allbill"] = $data[$i]["time"];
            } else {
                $bill["allbill"] = 1;
            }
            $bill["timelenth"] = ($bill["repayment_time"] - time()) / (60 * 60 * 24);
            if ($bill["timelenth"] <= 0) {
                $bill["timelenth"] = abs(intval($bill["timelenth"])) + 1;
            }
            $bill["timelenth"] = abs(intval($bill["timelenth"]));
            $data[$i]["bill"] = $bill;
            $i = $i + 1;
        }
        return $data;
    }

    public function getOrderInfo($oid = 0)
    {
        if (!$oid) {
            return false;
        }
        $data = $this->where(array("id" => $oid))->find();
        $loanbillModel = D("Loanbill");
        $billList = $loanbillModel->where(array("toid" => $oid))->order("billnum ASC")->select();
        $i = 0;
        while ($i < count($billList)) {
            //$billList[$i]["allmoney"] = toMoney($billList[$i]["money"] + $billList[$i]["interest"] + $billList[$i]["overdue"]);
            $billList[$i]["allmoney"] = toMoney($billList[$i]["money"] + $billList[$i]["overdue"]);
            $i = $i + 1;
        }
        $nowBill = array("id" => 0, "money" => 0, "status" => 2);
        $bill = $loanbillModel->where(array("status" => array("IN", "0,1"), "toid" => $data["id"]))->order("repayment_time ASC")->find();
        if ($bill) {
            //$nowBill = array("id" => $bill["id"], "money" => toMoney($bill["money"] + $bill["interest"] + $bill["overdue"]), "status" => $bill["status"]);
            $nowBill = array("id" => $bill["id"], "money" => toMoney($bill["money"] + $bill["overdue"]), "status" => $bill["status"]);
        }
        $data["nowBill"] = $nowBill;
        $hasMoney = $loanbillModel->where(array("toid" => $data["id"], "status" => array("in", "0,1")))->sum("money");
        //$hasInterest = $loanbillModel->where(array("toid" => $data["id"], "status" => array("in", "0,1")))->sum("interest");
        $hasOverdue = $loanbillModel->where(array("toid" => $data["id"], "status" => array("in", "0,1")))->sum("overdue");
        //$data["allBillMoney"] = toMoney($hasMoney + $hasInterest + $hasOverdue);
        $data["allBillMoney"] = toMoney($hasMoney + $hasOverdue);
        $data["billList"] = $billList;
        return $data;
    }

    public function getOrderCount($adminid)
    {
        $count = $this->where(array('sid' => $adminid, 'pending' => 0))->count();
        //var_dump($this->getLastSql());
        return $count;
    }

    public function haseOrder($uid)
    {
        $order = $this->where(array('uid' => $uid, 'pending' => array('NEQ', 2), 'status' => 0))->find();
        if ($order) {
            return $order;
        } else {
            return false;
        }
    }

    public function getUserSuccNum($uid)
    {
        $num = $this->where(array("uid" => $uid, "pending" => 1))->count();
        return $num;
    }

    public function getUserErrNum($uid)
    {
        $num = $this->where(array("uid" => $uid, "pending" => 2))->count();
        return $num;
    }

    public function getUserRepayNum($uid)
    {
        $num = $this->where(array("uid" => $uid, "status" => 1))->count();
        return $num;
    }

    public function getUserLoanMoney($uid)
    {
        $money = $this->where(array("uid" => $uid, "pending" => 1))->sum("money");
        return toMoney($money);
    }

    public function addUserOrder($uid, $loanTime = 7, $sid = 1, $loanTimeInfo = array(), $user = null, $infoData = null, $loanTotal = 0)
    {

        if (!$user) {
            $userModel = D('User');
            $user = $userModel->where(array("id" => $uid))->find();
        }
        if ($user) {
            if (!$infoData) {
                $infoModel = D('info');
                $infoData = $infoModel->where(array("uid" => $uid))->find();
            }
            $identity = json_decode($infoData['identity'], true);
            $bankInfo = json_decode($infoData['bank'], true);
            if (empty($loanTimeInfo)) {
                $starttime = strtotime(date("Y-m-d"));
                if (C("Loan_TYPE")) {
                    $endtime = strtotime("+" . $loanTime . " Month", $starttime);
                    $repaymenttime = date("d", strtotime("+29 day", $starttime));
                    $fastrepayment = strtotime("+29 day", $starttime);
                } else {
                    $endtime = strtotime("+" . $loanTime . " day", $starttime);
                    $fastrepayment = $repaymenttime = $endtime;
                }
            } else {
                $starttime = $loanTimeInfo['starttime'];
                $endtime = $loanTimeInfo['endtime'];
                $repaymenttime = $loanTimeInfo['repaymenttime'];
                $fastrepayment = $loanTimeInfo['fastrepayment'];
            }
            $userRealName = $identity["name"];
            if (!$userRealName) {
                $userRealName = $user['firstname'] . ' ' . $user['lastname'];
            }
            if (strlen($userRealName) <= 3) {
                $userRealName = $user['telnum'];
            }
            $loanorderModel = D("Loanorder");
            $loanOrder = $loanorderModel->where(['uid' => $user["id"]])->find();
            $data = array(
                "uid" => $user["id"],
                "name" => $userRealName,
                "idcard" => $identity["idcard"],
                "money" => $user['quota'],
                "time" => $loanTime,
                "bankname" => $bankInfo["bankName"],
                "banknum" => $bankInfo["bankNum"],
                "phoneNumber" => $bankInfo["bankPhone"],
                "loantype" => C("Loan_TYPE"),
                "interest" => getInterest(),
                "reply_rate" => getRepayRate(),
                "starttime" => $starttime,
                "endtime_str" => date("Y/m/d", $endtime),
                "endtime" => $endtime,
                "repaymenttime" => $repaymenttime,
                "fastrepayment_str" => date("m/d", $fastrepayment),
                "fastrepayment" => $fastrepayment,
                "overdue" => C("Overdue"),
                "remark" => $loanOrder['remark']
            );
            $data['oid'] = $this->newOrder($data);
            if ($this->addNewOrder($data, $sid, $loanTotal)) {
                return true;
            }
        }
        return false;
    }


    //是否逾期
    public function hasOrderOverDue($order)
    {
        if ($order["delay"] === 1) {
            $day = $order["delay_day"] + $order['time'];
            return $this->orderOverStatus($order['timetype'], $day, $order['add_time']);
        } else {
            return $this->orderOverStatus($order['timetype'], $order['time'], $order['add_time']);
        }
    }

    //订单状态
    public function orderOverStatus($timeType, $loanTime, $startTime)
    {
        $loanType = C('LOAN_TYPE');
        if ($timeType == 0) {
            if (time() > $startTime + $loanTime * 86400) return true;
        } else {
            if (time() > $startTime + $loanTime * 86400 * 30) return true;
        }
    }

    //最后还款时间
    public function getOrderRepayTime($order)
    {
        if (intval($order["delay"]) == 1) {
            $day = $order["delay_day"] + $order['time'];
            return $this->repaymentTime($order['timetype'], $day, $order['start_time']);
        }

        return $this->repaymentTime($order['timetype'], $order['time'], $order['start_time']);
    }

    public function repaymentTime($timeType, $loanTime, $startTime)
    {
        if ($timeType == 0) {

            return $startTime + $loanTime * 86400;
        }

        return $startTime + $loanTime * 86400 * 30;
    }

    /**
     * 查看用户是否可以重新借款
     * @return void
     */
    public function canLoanAgain($uid, $billMoney = 0)
    {
        $loanorderModel = D("Loanorder");
        $loanbillModel = D("Loanbill");
        if (!$billMoney) {
            $tmpTime = strtotime("+29 day", time());
            $bill = $loanbillModel->where(array("uid" => $uid, "repayment_time" => array("ELT", $tmpTime), "status" => array("IN", "0,1,4,5,6")))->order("repayment_time DESC")->find();
//            echo $loanbillModel->getLastSql();
            $billMoney = $bill["money"] + $bill["overdue"];
        }
        if ($billMoney > 0) {
            return false;
        } else {
            $loanorder = $loanorderModel->where(array("uid" => $uid, "status" => array('in', '0,1,4,5'), 'pending' => array('in', '0,1,2')))->find();
            if ($loanorder) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function addNewOrder($data, $sid, $loan_total = 0)
    {
        if (is_numeric($data["starttime"])) {
            $starttime = $data["starttime"];
        } else {
            $starttime = strtotime($data["starttime"]);
        }
        //获取之前成功借款次数
        if (!$loan_total) {
            $loanbillModel = D("Loanbill");
            $loan_total = $loanbillModel->where(array("uid" => $data["uid"]))->count();
        }

        $orderInfo = array(
            "uid" => $data["uid"],
            "oid" => $data['oid'],
            "money" => $data["money"],
            "time" => $data["time"],
            "timetype" => $data["loantype"],
            "name" => $data["name"],
            "bankname" => $data["bankname"],
            "banknum" => $data["banknum"],
            "interest" => $data["interest"],
            "reply_rate" => $data["reply_rate"],
            "start_time" => $starttime,
            "overdue" => $data["overdue"],
            "add_time" => time(),
            "sign" => "",
            "loan_total" => $loan_total + 1,
            "data" => json_encode($data),
            "status" => 0,
            "pending" => 0,
            'remark' => $data['remark'],
            "sid" => $sid
        );
        return $this->add($orderInfo);
    }

    /**
     * 逾期天数
     * @param $lastime
     * @return int
     */
    public function calcOverDueDay($lastime)
    {
        return intval((time() - $lastime) / 86400);
    }

    /**
     * 计算费用
     * @param $orderInfo
     * @return float|int
     */
    public function calcOrderInterest($orderInfo)
    {
        return floor($orderInfo['money'] * floatval(getInterest()) * $orderInfo['time']);
    }

    /**
     * 获取延期费用
     * @param $orderInfo
     * @return float|int
     */
    public function calcDelayFee($orderInfo) 
    {
        if ($orderInfo['delay']) {
            return $orderInfo['money'] * $orderInfo['delay_day'] * $orderInfo['delay_rate'];
        }
        return 0;
    }

    /**
     * 获取支付手续费
     * @param $orderInfo
     * @return float|int
     */
    public function calcRepayFee($orderInfo)
    {
       //return $orderInfo['money'] * $orderInfo['reply_rate'] * $orderInfo['time'];
//        return $orderInfo['money'] * $orderInfo['reply_rate'];

    //旧：
          return C('REPAY_COST') != null ? C('REPAY_COST') : $orderInfo['reply_rate'];
     
     /*2024-9-7修改：*/
      //return $orderInfo['money'] * C('Interest_D') * $orderInfo['time'];
    }
    
    /**
     * 手续费Commissions
     */ 
     public function calcCommissionsFee($orderInfo)
    {
//        return $orderInfo['money'] * $orderInfo['reply_rate'] * $orderInfo['time'];
//        return $orderInfo['money'] * $orderInfo['reply_rate'];
        return C('Commissions') != null ? C('Commissions') : 0;
    }
    
    /**
     * 利息：
     * repay_interest
     * 
     */ 
     public function callRepayinterestFee($oid){
         $loanbillModel = D("Loanbill");
          $bill = $loanbillModel->where(array("oid" => $oid))->find();
          return $bill['repay_interest'];
     }

    public function calcOverDueFee($orderInfo)
    {
        $money = floatval($orderInfo["money"]);
        $repaymentTime = $this->getOrderRepayTime($orderInfo);
        if ($repaymentTime < time()) { //逾期
            $overDueDay = $this->calcOverDueDay($repaymentTime);
//            echo "overDueDay:" . $overDueDay;
//            print_r([$money ,$overDueDay ,floatval($orderInfo['overdue'])]);
            if ($overDueDay >= 1) {
                return floor($money * $overDueDay * floatval($orderInfo['overdue']));
            }
        }
        return 0;
    }

    /**
     * 获取订单应还款金额
     * @param $orderInfo
     * @return void
     */
    public function calcRepayMoney($orderInfo)
    {
        $money = floatval($orderInfo["money"]);
        if ($orderInfo['delay']) {
            $delayMoney = $orderInfo['delay_day'] * floatval($orderInfo['interest']); //支付手续费
        }
        $money += $this->calcOverDueFee($orderInfo);
      //  echo "calcOverDueFee==>".$this->calcOverDueFee($orderInfo);
//        $money += $this->calcDelayFee($orderInfo); //延期
         $money += $this->calcRepayFee($orderInfo); //支付手续费
        //echo "calcRepayFee==>".$this->calcRepayFee($orderInfo);
//        $money += $orderInfo['delay_unpaid'];//延期付款未支付费用

        //Commissions手续费：
        $money += $this->calcCommissionsFee($orderInfo);
        
      //  var_dump($orderInfo);
        //利息：
       // var_dump($orderInfo);
        //$money += $this->callRepayinterestFee($orderInfo['oid']); 
        
        $money -= floatval($orderInfo['repaid_money']); //减去已还款金额
      
       return $money;
    }
}