<?php

class LoanbillModel extends RelationModel
{
    protected $_link = array('User' => array('mapping_type' => BELONGS_TO, 'foreign_key' => 'uid', 'mapping_name' => 'user'), 'Loanorder' => array('mapping_type' => BELONGS_TO, 'foreign_key' => 'toid', 'mapping_name' => 'Loanorder'));

    public function getUserOverdueNum($uid)
    {
        $num = $this->where(array("uid" => $uid, "status" => array("IN", "1,3")))->count();
        return $num;
    }

    public function getUserOverdueMoney($uid)
    {
        $money = $this->where(array("uid" => $uid, "status" => array("IN", "1")))->sum("money");
        $interest = $this->where(array("uid" => $uid, "status" => array("IN", "1")))->sum("interest");
        $overdue = $this->where(array("uid" => $uid, "status" => array("IN", "1")))->sum("overdue");
        return $money + $interest;
    }

    public function hasOverdue($uid)
    {
        $bill = $this->where(array("uid" => $uid, "status" => array('IN', '1')))->order('add_time DESC')->find();
        if ($bill['status'] == 1) {
            return true;
        } else {
            $loanType = C('LOAN_TYPE');
            $overdueTime = time() - $bill['add_time'];
            $loanOrderModel = D('Loanorder');
            $loanOrder = $loanOrderModel->where(array("id" => $bill['toid']))->find();

            if ($loanOrder['timetype'] == 0) {
                if ($overdueTime > $loanOrder['time'] * 86400) return true;
            } else {
                if ($overdueTime > $loanOrder['time'] * 86400 * 30) return true;
            }
        }
        return false;
    }

    public function hasOrderOverDue($order)
    {
        if ($order["delay"] === 1) {
            $day = $order["delay_day"] + $order['time'];
            return $this->orderOverStatus($order['timetype'], $day, $order['add_time']);
        } else {
            return $this->orderOverStatus($order['timetype'], $order['time'], $order['add_time']);
        }
    }

    public function orderOverStatus($timeType, $loanTime, $startTime)
    {
        $loanType = C('LOAN_TYPE');
        if ($timeType == 0) {
            if (time() > $startTime + $loanTime * 86400) return true;
        } else {
            if (time() > $startTime + $loanTime * 86400 * 30) return true;
        }
    }

    public function getUserNotRepayMoney($uid)
    {
        $money = $this->where(array("uid" => $uid, "status" => array("IN", "0,1")))->sum("money");
        $interest = $this->where(array("uid" => $uid, "status" => array("IN", "0,1")))->sum("interest");
        $overdue = $this->where(array("uid" => $uid, "status" => array("IN", "0,1")))->sum("overdue");
        //return $money + $interest + $overdue;
        return $money + $overdue;
    }

    public function createOrderBill($order,$force=false)
    {
        $data = json_decode($order["data"], true);
        $starttime = strtotime(date("Y-m-d"));
        $hasBill = $this->where(array("toid" => $order["id"], "oid" => $order["oid"], 'status' => array('in', '0,1')))->find();
        if($force==false){
            if ($hasBill && !empty($hasBill)) {
                return false;
            }
        }

        $loanorderModel = D("Loanorder");

        if ($order["timetype"] == 0) {

            $inserest = intval(floatval($order["money"]) * floatval($order["interest"]) * intval($order["time"])); //借款利息
//            $replay_inserest = intval(floatval($order["money"]) * floatval($order["reply_rate"]) * $order['time']); //还款利息


            /*2024-9-9 修改：*/
           $replay_inserest=$loanorderModel->calcCommissionsFee($order); //还款利息
        
          /*2024-9-9 修改旧：*/
          //$replay_inserest=$loanorderModel->calcOrderInterest($order); //还款利息
          
          
            $endtime = strtotime("+" . $order['time'] . " day", $starttime);
            $fastrepayment = $repaymenttime = $endtime;
            $billData = array("uid" => $order["uid"], "toid" => $order["id"], "oid" => $order["oid"], "billnum" => 1, "money" => $order["money"], "interest" => toMoney($inserest), 'repay_interest' => toMoney($replay_inserest), "overdue" => 0, "repayment_time" => $repaymenttime, "status" => 0, "add_time" => time());
            $this->add($billData);
        } else {
            $i = 0;
            while ($i < intval($order["time"])) {
                $repayment_time = $data["fastrepayment"];
                if ($i > 0) {
                    $repayment_time = strtotime("+" . $i . " Month", $repayment_time);
                }
                $inserest = intval(floatval($order["money"]) * floatval($order["interest"]));
                  /*2024-9-9 修改旧：：*/
                //$replay_inserest=$loanorderModel->calcRepayFee($order); //还款利息
                  /*2024-9-9 修改：*/
                $replay_inserest=$loanorderModel->calcCommissionsFee($order); //还款利息
                
                
                $billData = array("uid" => $order["uid"], "toid" => $order["id"], "oid" => $order["oid"], "billnum" => $i + 1, "money" => toMoney($order["money"] / intval($order["time"])), "interest" => toMoney($inserest), 'repay_interest' => toMoney($replay_inserest), "overdue" => 0, "repayment_time" => $repayment_time, "status" => 0, "add_time" => time());
                $this->add($billData);
                $i = $i + 1;
            }
            $billMoney = intval($order["time"]) * toMoney(floatval($order["money"]) * floatval($order["interest"])) + intval($order["time"]) * toMoney($order["money"] / intval($order["time"]));
            $deviation = $billMoney - $order["money"];
        }
    }

    public function calcNeedRepayTime($billOrder)
    {
        $repyamentTime = $billOrder['repayment_time'];
        if ($billOrder["delay"] == 1) {
            $repyamentTime += $billOrder['delay_day'] * 86400;
        }
        return $repyamentTime;
    }

    public function calcBillOverDueFee($billOrder)
    {
        $overdueRate = C("Overdue");
        $repaymentTime = $this->calcNeedRepayTime($billOrder);
        $overdueDay = floor((time() - $repaymentTime) / 86400);
        if ($overdueDay >= 1) {
            return $overdueDay * $overdueRate * $billOrder["money"];
        }
    }

    public function calcBillRepayFee($billOrder)
    {
//        $loanDay = intval(($billOrder["repayment_time"] - $billOrder["add_time"]) / 86400) - $billOrder["delay_day"];
//        return $loanDay * $billOrder['repay_interest'] * $billOrder["money"];
        return C('REPAY_COST') ? C('REPAY_COST') : $billOrder['repay_interest'];
    }

    public function calcNeedRepayMoney($billOrder)
    {
        $needRepayMoney = $billOrder["money"];
        $needRepayMoney += $this->calcBillOverDueFee($billOrder);//逾期费用
        $needRepayMoney += $this->calcBillRepayFee($billOrder); //还款费用
//        $needRepayMoney += $billOrder["delay_unpaid"];
        $needRepayMoney -= $billOrder["repaid_money"];
        return $needRepayMoney;
    }
}