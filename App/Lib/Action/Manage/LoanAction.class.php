<?php

class LoanAction extends CommonAction
{
    public function pending()
    {
        $admin = $this->isLogin();
        if (!ISADMIN) {
            $where = array("pending" => 0, "sid" => $admin['id']);
        } else {
            $where = array("pending" => 0);
        }
        if (I("oid")) {
            $where["oid"] = trim(I("oid"));
        }
        $pendingType = I("__type__", 1);

        if ($pendingType && $pendingType == 2) {
            $where["loan_total"] = array('gt', 1);
        } else {
            $where["loan_total"] = array('ELT', 1);
        }
        if (I("stimeStart")) {
            $where["add_time"] = array("EGT", strtotime(I("stimeStart")));
        }
        if (I("stimeEnd")) {
            $where["add_time"] = array("ELT", strtotime(I("stimeEnd")));
        }
        if (I("stimeStart") && I("stimeEnd")) {
            $where["add_time"] = array(array("EGT", strtotime(I("stimeStart"))), array("ELT", strtotime(I("stimeEnd"))));
        }
        $loanorderModel = D("Loanorder");
        import("ORG.Util.Page");
        $count = $loanorderModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $loanorderModel->where($where)->order("add_time DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
//        echo $loanorderModel->getLastSql();
        $i = 0;
        $loanbillModel = D("Loanbill");
        while ($i < count($list)) {
            $interestMoney = $list[$i]["interest"] * $list[$i]["time"] * $list[$i]["money"];
            $list[$i]["interest_money"] = toMoney(intval($interestMoney));
            $list[$i]["get_money"] = toMoney(intval($list[$i]["money"] - $interestMoney));
            $list[$i]["repay_money"] = $loanorderModel->calcRepayMoney($list[$i]);
            //待还款金额
            $loanbillModel->where(array("oid" => $list[$i]['oid'], "status" => array("IN", "2,3")))->sum("money");
            $i = $i + 1;
        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->assign("pendingType", $pendingType);
        $this->display();
    }

    public function ajaxView()
    {
        $admin = $this->isLogin();
        $id = I("id");
        if (!ISADMIN) {
            $where = array("id" => $id, "sid" => $admin['id']);
        } else {
            $where = array("id" => $id);
        }
        $loanorderModel = D("Loanorder");
        $loanOrder = $loanorderModel->where($where)->find();
        if ($loanOrder) {
            $loanOrder['data'] = json_decode($loanOrder['data'], "JSON");
            $loanOrder['notify'] = json_decode($loanOrder['notify'], "JSON");
            $this->ajaxReturn($loanOrder);
        } else {
            $this->error("没有信息");
        }
    }

    public function ajaxRecord()
    {
        $admin = $this->isLogin();

        if (!ISADMIN) {
            $where = array("sid" => $admin['id']);
        } else {
            $where = array();
        }
        $oid = I('order_id');
        if ($oid) {
            $where["oid"] = trim($oid);
        }

        $loanRecordModel = D("LoanRecord");
        import("ORG.Util.Page");
        $count = $loanRecordModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));

        $list = $loanRecordModel->where($where)->order("created_at DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
        for ($i = 0; $i < count($list); $i++) {
            switch ($list[$i]['org_status']) {
                case 0:
                    $list[$i]['org_status'] = '待审核';
                    break;
                case 1:
                    $list[$i]['org_status'] = '处理中';
                    break;
                case 2:
                    $list[$i]['org_status'] = '已拒绝';
                    break;
                case 3:
                    $list[$i]['org_status'] = '还款中';
                    break;
                case 4:
                    $list[$i]['org_status'] = '续期中';
                    break;
                case 5:
                    $list[$i]['org_status'] = '已还清';
                    break;
                case 6:
                    $list[$i]['org_status'] = '已逾期';
                    break;
            }
            switch ($list[$i]['action']) {
                case 0:
                    $list[$i]['action'] = '待审核';
                    break;
                case 1:
                    $list[$i]['action'] = '处理中';
                    break;
                case 2:
                    $list[$i]['action'] = '已拒绝';
                    break;
                case 3:
                    $list[$i]['action'] = '还款中';
                    break;
                case 4:
                    $list[$i]['action'] = '续期中';
                    break;
                case 5:
                    $list[$i]['action'] = '已还清';
                    break;
                case 6:
                    $list[$i]['action'] = '已逾期';
                    break;
            }
            $list[$i]['position'] = $list[$i]['position'] == 1 ? "后台" : "前台";
            $list[$i]['created_at'] = date('Y-m-d H:i:s', $list[$i]['created_at']);
        }
        echo json_encode([
            "code" => 0,
            "msg" => "",
            "count" => $count,
            "data" => $list,
        ]);
        exit;
    }

    public function reGenerateOrder()
    {
        $id = I("id");
        if (!$id) {
            $this->error("参数有误");
        }
        $loanorderModel = D("Loanorder");
        $order = $loanorderModel->where(array("id" => $id))->find();
        if (!$order) {
            $this->error("订单不存在");
        }
        $pending = intval($order["pending"]);
        if ($pending != -2 && $pending != -3) {
            $this->error("订单只有没有提交成功或者回调失败才能重新提交");
        }
        $lanType = $order['oid'];
        if (($lanIndex = stripos($lanType, 'R')) != -1) {
            $lanType = substr($lanType, $lanIndex);
            $lanType = str_replace('R', '', $lanType);
        }
        $generateData = array(
            "loantype" => "R" . $lanType,
            "uid" => substr($order['oid'], 13, 1),
        );
        $newOrderId = $loanorderModel->newOrder($generateData);
        if (!$newOrderId) {
            $this->error("Failed to generate order");
        }
        $paymentModel = D('Payment');
        $order['rid'] = $order['oid'];
        $order['oid'] = $newOrderId;
        $orderDataInfo = json_decode($order['data'], true);
        $orderDataInfo['oid'] = $newOrderId;
        $order['data'] = json_encode($orderDataInfo);
        $flag = $loanorderModel->where(array("id" => $id))->save($order);
        if ($flag) {
            $response = $paymentModel->dispensing($order); //执行出款给商家或用户的操作
            if (intval($response['code']) == 200) {
                $loanorderModel->where(array('id' => $id))->save(array('pending' => -1));
                $this->success("操作成功");
            } else {
                $loanorderModel->where(array('id' => $id))->save(array('reqerror' => $response['msg'], 'pending' => -2));
                $this->error("操作失败" . $response['msg']);
            }
        } else {
            $this->error("操作失败,订单修改失败");
        }

    }

    /**
     * 处理中
     * @return void
     */
    public function process()
    {
        $admin = $this->isLogin();
        if (!ISADMIN) {
            $where = array("pending" => array("in", "-1, -2, -3"), "sid" => $admin['id']);
        } else {
            $where = array("pending" => array("in", "-1, -2, -3"));
        }
        if (I("oid")) {
            $where["oid"] = I("oid");
        }
        if (I("stimeStart")) {
            $where["add_time"] = array("EGT", strtotime(I("stimeStart")));
        }
        if (I("stimeEnd")) {
            $where["add_time"] = array("ELT", strtotime(I("stimeEnd")));
        }
        if (I("stimeStart") && I("stimeEnd")) {
            $where["add_time"] = array(array("EGT", strtotime(I("stimeStart"))), array("ELT", strtotime(I("stimeEnd"))));
        }
        $loanorderModel = D("Loanorder");
        import("ORG.Util.Page");
        $count = $loanorderModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $loanorderModel->where($where)->order("add_time DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
        $i = 0;
        while ($i < count($list)) {
            $list[$i]["interest_money"] = toMoney($list[$i]["interest"] * $list[$i]["time"] * $list[$i]["money"]);
            $i = $i + 1;
        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }


    public function overdue()
    {
        $admin = $this->isLogin();
        if (!ISADMIN) {
            $where['_string'] = 'status=1 OR (`repayment_time`<' . time() . ' AND status!=2 AND status!=3)';
        } else {
            $where['_string'] = 'status=1 OR (`repayment_time`<' . time() . ' AND status!=2 AND status!=3)';
        }
        if (I("oid")) {
            $where["oid"] = trim(I("oid"));
        }
        if (I("stimeStart")) {
            $where["repayment_time"] = array("EGT", strtotime(urldecode(I("stimeStart"))));
        }
        if (I("stimeEnd")) {
            $where["repayment_time"] = array("ELT", strtotime(urldecode(I("stimeEnd"))));
        }
        if (I("stimeStart") && I("stimeEnd")) {
            $where["repayment_time"] = array(array("EGT", strtotime(urldecode(I("stimeStart")))), array("ELT", strtotime(urldecode(I("stimeEnd")))));
        }
        $loanbillModel = D("Loanbill");
        import("ORG.Util.Page");
        $count = $loanbillModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();

        $list = $loanbillModel->where($where)->order("repayment_time DESC,billnum DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
//        echo $loanbillModel->getLastSql();
        $i = 0;
        while ($i < count($list)) {
            $list[$i]["bill_money"] = toMoney($list[$i]["money"]);
//            $list[$i]["repayment_time"] = $loanbillModel->calcNeedRepayTime($list[$i]);
            $list[$i]["overdue_time"] = floor((time() - $list[$i]["repayment_time"]) / 86400);
            $list[$i]["to_money"] = $loanbillModel->calcNeedRepayMoney($list[$i]);
            if ($list[$i]["overdue_time"] >= 1 || !$list[$i]["overdue"]) {
                $list[$i]["overdue"] = $loanbillModel->calcBillOverDueFee($list[$i]);
            }
            $i = $i + 1;
        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }

    public function refuse()
    {
        $admin = $this->isLogin();
        if (!ISADMIN) {
            $where = array("pending" => 2, "sid" => $admin['id']);
        } else {
            $where = array("pending" => 2);
        }

        if (I("soid")) {
            $where["oid"] = trim(I("soid"));
        }
        if (I("stimeStart")) {
            $where["add_time"] = array("EGT", strtotime(I("stimeStart")));
        }
        if (I("stimeEnd")) {
            $where["add_time"] = array("ELT", strtotime(I("stimeEnd")));
        }
        if (I("stimeStart") && I("stimeEnd")) {
            $where["add_time"] = array(array("EGT", strtotime(I("stimeStart"))), array("ELT", strtotime(I("stimeEnd"))));
        }
        $loanorderModel = D("Loanorder");
        import("ORG.Util.Page");
        $count = $loanorderModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $loanorderModel->where($where)->order("add_time DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
        $i = 0;
        while ($i < count($list)) {
            $list[$i]["interest_money"] = toMoney($list[$i]["interest"] * $list[$i]["time"] * $list[$i]["money"]);
            $i = $i + 1;
        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }

    public function all()
    {
        $admin = $this->isLogin();
        $where = $this->createCondition();
        $filter=I("filter");

        $loanorderModel = D("Loanorder");
        if($filter==1){
            $data=$loanorderModel->query("select uid,count(*) as cnt from cv_loanorder group by uid having cnt>1");
            $uids=array_column($data,'uid');
            if(!empty($uids))$where['uid']=array('in',$uids);
            if(empty($uids))$where['uid']=0;
        }
        $payorderModel = D("Payorder");
        import("ORG.Util.Page");
        $count = $loanorderModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $loanorderModel->where($where)->order("add_time DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
//        echo $loanorderModel->getLastSql();
        $loanbillModel = D("Loanbill");
        $i = 0;

        while ($i < count($list)) {
            $interestMoney = intval($list[$i]["interest"] * $list[$i]["time"] * $list[$i]["money"]);
            $list[$i]["interest_money"] = toMoney($interestMoney);
            //到账金额
            $list[$i]["get_money"] = toMoney(intval($list[$i]["money"] - $interestMoney));
            //还款费用
            $list[$i]['repay_fee'] = $loanorderModel->calcRepayFee($list[$i]);
            //续期费用
            $list[$i]['delay_fee'] = intval($list[$i]["money"] * $list[$i]["delay_day"] * $list[$i]["delay_rate"]);
            //应还金额
//            到期时间
            $list[$i]["end_time"] = $list[$i]["start_time"] + ($list[$i]["time"] * 86400);
            $list[$i]["overdue_fee"] = $loanorderModel->calcOverDueFee($list[$i]);
            if ($list[$i]["status"] == 2) { //已还清
                $payorderModel = D("Payorder");
                $payOrder = $payorderModel->where(['pay_type' => 2, 'loid' => $list[$i]['oid'], 'status' => 1])->find();
                $payTime = $payOrder['pay_time'];
                $repaymentTime = $loanorderModel->getOrderRepayTime($list[$i]);
                $payOverdueDay = intval(($payTime - $repaymentTime) / 86400);
                if ($payOverdueDay >= 1) {
                    $list[$i]["overdue_fee"] = $list[$i]['money'] * $payOverdueDay * floatval($list[$i]['overdue']);
                }
            }
            if ($list[$i]["delay"] && $list[$i]["delay_day"] > 1) {
                $list[$i]["end_time"] += $list[$i]["delay_day"] * 86400;
            }
            $list[$i]["to_money"] = $loanorderModel->calcRepayMoney($list[$i]);
            $loanbills = $loanbillModel->where(array("toid" => $list[$i]["id"]))->select();
            $list[$i]["hasBillNum"] = 0;
            $list[$i]["BillNum"] = 0;
            $list[$i]["overdueBillNum"] = 0;
            $repayMoney = 0;
            if (intval($list[$i]["status"]) != 2) {
                $repaymentTime = $loanorderModel->getOrderRepayTime($list[$i]);
                if ($repaymentTime < time()) {
                    $list[$i]["status"] = 1;
                }
            }
            foreach ($loanbills as $loanbill) {
                $list[$i]["BillNum"]++;
                if (in_array(intval($loanbill['status']), array(2, 3))) {
                    $list[$i]["hasBillNum"]++;
                    $repayMoney += floatval($loanbill['money']);
                }
                if ($loanbill["status"] == 3) {
                    $list[$i]["overdueBillNum"]++;
                }
            }
            $list[$i]["repay_money"] = 0;
            if ($list[$i]['status'] != 2 && $list[$i]['pending'] != 2) $list[$i]["repay_money"] = $loanorderModel->calcRepayMoney($list[$i]);
            $i = $i + 1;
        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $query = I();
        if (!empty($query)) {
            foreach ($query as $k => $q) {
                $this->assign($k, $q);
            }
        }
        $this->display();
    }

    public function index()
    {
        $admin = $this->isLogin();
        if (!ISADMIN) {
            $where = array("pending" => 1, "status" => array('IN', '0,5'), 'delay' => array('eq', 0), "sid" => $admin['id']);
        } else {
            $where = array("pending" => 1, "status" => 0, 'delay' => array('eq', 0));
        }

        if (I("oid")) {
            $where["oid"] = trim(I("oid"));
        }
        if (I("stimeStart")) {
            $where["add_time"] = array("EGT", strtotime(I("stimeStart")));
        }
        if (I("stimeEnd")) {
            $where["add_time"] = array("ELT", strtotime(I("stimeEnd")));
        }
        if (I("stimeStart") && I("stimeEnd")) {
            $where["add_time"] = array(array("EGT", strtotime(I("stimeStart"))), array("ELT", strtotime(I("stimeEnd"))));
        }
        $loanorderModel = D("Loanorder");
        import("ORG.Util.Page");
        $count = $loanorderModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $loanorderModel->where($where)->order("add_time DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
//        echo $loanorderModel->getLastSql();
        $loanbillModel = D("Loanbill");
        $infoModel = D('Info');
        $i = 0;
        $loanbillModel = D("Loanbill");
        while ($i < count($list)) {
            $loanbills = $loanbillModel->where(array("toid" => $list[$i]["id"]))->select();
            $list[$i]["hasBillNum"] = 0;
            $list[$i]["BillNum"] = 0;
            $list[$i]["overdueBillNum"] = 0;
            $repayMoney = 0;
            foreach ($loanbills as $loanbill) {
                $list[$i]["BillNum"]++;
                if (in_array(intval($loanbill['status']), array(2, 3))) {
                    $list[$i]["hasBillNum"]++;
                    $repayMoney += floatval($loanbill['money']);
                }
                if ($loanbill["status"] == 3) {
                    $list[$i]["overdueBillNum"]++;
                }
            }
            $list[$i]["to_money"] = $loanorderModel->calcRepayMoney($list[$i]);
            $list[$i]["interest_money"] = toMoney($list[$i]["interest"] * $list[$i]["time"] * $list[$i]["money"]);
            $list[$i]["repay_money"] = toMoney($repayMoney);

            $i = $i + 1;
        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }

    public function changeRepayMoney()
    {
        $admin = $this->isLogin();
        if (!ISADMIN) {
            $where = array("sid" => $admin['id']);
        } else {
            $where = array();
        }
        $lid = I('lid');
        $money = I('money');
        if (!$lid || $money < 0) {
            $this->error('parameter missing');
        }
        $loanorderModel = D("Loanorder");
        $LoanbillModel = D("Loanbill");
        $logRepayMoneyModifyModel = D("LogRepayMoneyModify");
        $where['id'] = $lid;
        $loanorder = $loanorderModel->where($where)->find();
        if ($loanorder) {
            $needRepayMoney = $loanorderModel->calcRepayMoney($loanorder);
            if ($money >= $needRepayMoney + $loanorder['repaid_money']) {
                $this->error('已还款金额不能大于应还款金额');
            }
            try {
                $trans = M();
                $trans->startTrans();
                $status1 = $loanorderModel->where($where)->save(array('repaid_money' => $money));
                if (!$status1) {
                    throw new Exception('修改借款订单失败');
                }
                $status2 = $LoanbillModel->where(['toid' => $lid])->save(array('repaid_money' => $money));
                if (!$status2) {
                    throw new Exception('修改账单失败');
                }
                $log = [
                    "lid" => $loanorder['id'],
                    "oid" => $loanorder['oid'],
                    "before" => $loanorder['repaid_money'],
                    "after" => $money,
                    "change" => floatval($money) - floatval($loanorder['repaid_money']),
                    "sid" => $admin['id'],
                    "ip" => get_client_ip(),
                    "add_time" => time(),

                ];
                $status3 = $logRepayMoneyModifyModel->add($log);
                if (!$status3) {
                    throw new Exception('修改账单失败');
                }
                $trans->commit();
                $this->success("操作成功");
            } catch (Exception $e) {
                $trans->rollback();
                $this->error($e->getMessage());
            }
        } else {
            $this->error('loanorder is not exist');
        }

    }

    public function payoff()
    {
        $admin = $this->isLogin();
        if (!ISADMIN) {
            $where = array('pending' => 1, 'status' => 2, "sid" => $admin['id']);
        } else {
            $where = array('pending' => 1, 'status' => 2);
        }

        if (I("oid")) {
            $where["oid"] = trim(I("oid"));
        }
        if (I("stimeStart")) {
            $where["add_time"] = array("EGT", strtotime(I("stimeStart")));
        }
        if (I("stimeEnd")) {
            $where["add_time"] = array("ELT", strtotime(I("stimeEnd")));
        }
        if (I("stimeStart") && I("stimeEnd")) {
            $where["add_time"] = array(array("EGT", strtotime(I("stimeStart"))), array("ELT", strtotime(I("stimeEnd"))));
        }
        $loanorderModel = D("Loanorder");
        import("ORG.Util.Page");
        $count = $loanorderModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $loanorderModel->where($where)->order("add_time DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
        $loanbillModel = D("Loanbill");
        $i = 0;
        while ($i < count($list)) {
            $list[$i]["interest_money"] = toMoney($list[$i]["interest"] * $list[$i]["time"] * $list[$i]["money"]);
            $loanbills = $loanbillModel->where(array("toid" => $list[$i]["id"]))->select();
            $list[$i]["BillNum"] = 0;
            $list[$i]["overdueBillNum"] = 0;
            foreach ($loanbills as $loanbill) {
                $list[$i]["BillNum"]++;
                if (in_array(intval($loanbill['status']), array(3))) {
                    $list[$i]["overdueBillNum"]++;
                }
            }
//            $list[$i]["BillNum"] = $loanbillModel->where(array("toid" => $list[$i]["id"]))->count();
//            $list[$i]["overdueBillNum"] = $loanbillModel->where(array("toid" => $list[$i]["id"], "status" => 3))->count();
            $i = $i + 1;
        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }

    public function bill()
    {
        $where = array();
        if (I("oid")) {
            $where["oid"] = trim(I("oid"));
        }
        if (isset($_GET['status'])) {
            $status = I("status", -100);
            if ($status != -100) $where["status"] = $status;
        }
        $loanbillModel = D("Loanbill");
        import("ORG.Util.Page");
        $count = $loanbillModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $loanbillModel->where($where)->order("add_time DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
        foreach ($list as $key => $row) {
            $list[$key]['overdue'] = $loanbillModel->calcBillOverDueFee($row);
        }
//        echo $loanbillModel->getLastSql();
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }

    public function delay()
    {
        $where = array('delay' => 1, 'status' => array('IN', '4'));
        if (I("oid")) {
            $where["oid"] = trim(I("oid"));
        }
        $loanbillModel = D("Loanbill");
        import("ORG.Util.Page");
        $where['_string'] = 'repayment_time>' . time();
        $count = $loanbillModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $loanbillModel->where($where)->order("add_time DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
        foreach ($list as $key => $row) {
            $list[$key]["overdue"] = $loanbillModel->calcBillOverDueFee($row);
        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }

    public function setPendingStatus()
    {
        $id = trim(I("id"));
        if (!$id) {
            $this->error("参数有误");
        }
        $loanorderModel = D("Loanorder");
        $order = $loanorderModel->where(array("id" => $id))->find();
        if (!$order) {
            $this->error("订单不存在");
        }
        $pendingStatus = intval($order["pending"]);
        if (!in_array($pendingStatus, [0, -2, -3])) {
            $this->error("订单已审核");
        }
        $status = I("status");
        $error = I("error");
//		$error = "";
//		if ($status == 2) {
//			$error = I("error");
//		}
//        if ($status == 1) {
//
//        }
        $userModel = D("User");
        $number = $userModel->getInfo("id", $order["uid"], "telnum");
        $smsModel = D("Sms");
        if ($status == 1) {
            $content = htmlspecialchars_decode(htmlspecialchars_decode(C("loan_adopt")));
            $content = str_replace("<@>", $order["oid"], $content);
            $content = str_replace("《@》", $order["oid"], $content);
        } else {
            $content = htmlspecialchars_decode(htmlspecialchars_decode(C("loan_refuse")));
            $content = str_replace("<@>", $order["oid"], $content);
            $content = str_replace("《@》", $order["oid"], $content);
            $content = str_replace("<@sitename@>", C("siteName"), $content);
            $content = str_replace("《@sitename@》", C("siteName"), $content);
        }
        $smsModel->sendSms($number, $content);
        $result = $loanorderModel->where(array("id" => $id))->save(array("pending" => $status, "error" => $error));
        if (!$result) {
            $this->error("订单操作失败");
        }
        $paymentModel = D('Payment');
        $loanOrder = $loanorderModel->where(array("id" => $id))->find();
        if ($status == 1) {
            $loanOrderModel = D('loanorder');
            $daifuWitch = C('DAIFU_SUBMIT_TO_PAYER');
            if (intval($daifuWitch) == 1) {
                $response = $paymentModel->dispensing($loanOrder); //执行出款给商家或用户的操作
                if (intval($response['code']) == 200) {
                    $loanOrderModel->where(array('id' => $id))->save(array('pending' => -1));
                    $this->success("操作成功");
                } else {
                    $loanOrderModel->where(array('id' => $id))->save(array('reqerror' => $response['msg'], 'pending' => -2));
                    $this->error("操作失败" . $response['msg']);
                }
            } else {
                $loanbillModel = D("Loanbill");
                $loanbillModel->createOrderBill($loanOrder);
                $this->success("操作成功");
            }
        } else {
            $this->success("操作成功");
        }
    }

    /**
     * 修改贷款状态
     * @return void
     */
    public function ChangeLoanOrderStatus()
    {
        $ids = I("ids");
        if (!$ids) {
            $this->error("订单参数有误");
        }
        $status = I("status", 0);
        if (is_null($status)) {
            $this->error("订单参数有误");
        }
        $admin = $this->isLogin();
        $loanOrderModel = D('loanorder');
        $loanRecordModel = D('loanRecord');
        $delayRate = C('PostPone_RATE');
        $delayDay = C('DEFAULT_DELAY_DAY');
        $delayDay = $delayDay && $delayDay > 1 ? $delayDay : 7;
        switch ($status) {
            case 2://已拒绝
                $saveLoanOrderInfo = array('pending' => 2, 'status' => 0, 'delay' => 0, 'delay_day' => 0, 'delay_num' => 0, 'delay_unpaid' => 0);
                break;
            case 1://已同意
                $saveLoanOrderInfo = array('pending' => 1, 'status' => 0, 'delay' => 0, 'delay_day' => 0, 'delay_num' => 0);
                break;
            case 0://待审核
                $saveLoanOrderInfo = array('pending' => 0, 'status' => 0, 'delay' => 0, 'delay_day' => 0, 'delay_num' => 0, 'delay_unpaid' => 0);
                break;
            case 3://还款中
                $saveLoanOrderInfo = array('pending' => 1, 'status' => 0, 'delay' => 0, 'delay_day' => 0, 'delay_num' => 0, "start_time" => time());
                break;
            case 4://续期中

                $saveLoanOrderInfo = array('pending' => 1, 'status' => 4, 'delay' => 1, 'delay_rate' => $delayRate,
                    'delay_day' => array('exp', "delay_day+$delayDay"),
                    'delay_num' => array('exp', "delay_num+1"),);
                break;
            case 5://已还清
                $saveLoanOrderInfo = array('pending' => 1, 'status' => 2, 'delay' => 0, 'delay_day' => 0, 'delay_num' => 0);
                break;
            case 6://已逾期
                $saveLoanOrderInfo = array('pending' => 1, 'status' => 1, 'delay' => 0, 'delay_day' => 0, 'delay_num' => 0);
                break;
        }
        $paymentModel = D('Payment');
        $loanbillModel = D("Loanbill");


        $loanOrderModel = D('Loanorder');
        $daifuWitch = C('DAIFU_SUBMIT_TO_PAYER');
        $audit_type = I('audit_type');

        if ($audit_type == 'all') {
            $condition = $this->createCondition();

        } else {
            $idIn = implode(',', (array)$ids);
            $condition = array('id' => array('in', $idIn));
        }

//        print_r($condition);
        $loanOrders = $loanOrderModel->where($condition)->select();
//        echo $loanOrderModel->getLastSql();
////        echo $status;
//        print_r(count($loanOrders));
//        exit();
        //全部修改

        foreach ($loanOrders as $loanOrder) {
            $delayFee = $delayDay * $delayRate * $loanOrder['money'];
            $id = $loanOrder['id'];
            $canLoanAgain = $loanOrderModel->canLoanAgain($loanOrder['uid'], 0);
            if (!$canLoanAgain) {
                //判断当前订单是否是最新的
                $lastorder = $loanOrderModel->where(array('uid' => $loanOrder['uid']))->order('add_time desc')->limit(1)->find();
                if (intval($lastorder['id']) != intval($id)) {
                    continue;
                }
            }
            if (!empty($saveLoanOrderInfo)) {
//                if ($status == 4) $saveLoanOrderInfo['delay_unpaid'] = array("exp", "delay_unpaid+$delayFee");
                $loanOrderModel->where(array('id' => $id))->save($saveLoanOrderInfo);
            }

            if ($loanOrder['pending'] == 0) $dstatus = 0;
            if ($loanOrder['pending'] == 2) $dstatus = 2;
            if ($loanOrder['status'] == 0 && $loanOrder['pending'] == 1) $dstatus = 3;
            if ($loanOrder['status'] == 1 && $loanOrder['pending'] == 1) $dstatus = 6; //预期
            if ($loanOrder['status'] == 4 && $loanOrder['pending'] == 1) $dstatus = 4;
            if ($loanOrder['status'] == 2 && $loanOrder['pending'] == 1) $dstatus = 5;//已还清

            $record = [
                "oid" => $loanOrder['oid'],
                "action" => $status,
                "position" => 1,
                "org_status" => $dstatus,
                "sid" => $admin['id'],
                "created_at" => time(),
            ];
            $loanRecordModel->add($record);
            if ($status == 1) {
                $loanBill = $loanbillModel->where(array('toid' => $id))->find();
                if (!$loanBill || empty($loanBill)) {
                    $this->createOrderBill($daifuWitch, $loanOrderModel, $paymentModel, $loanbillModel, $loanOrder, $id);
                } else {
                    $loanbillModel->where(array('toid' => $id))->save(array("status" => 0, "start_time" => time()));
                }
            } elseif ($status == 2) {//已拒绝
                $loanbillModel->where(array('toid' => $id))->save(array("status" => -4, 'delay' => 1, 'delay_num' => 0, 'delay_unpaid' => 0));
            } elseif ($status == 0) {//待审核
                $loanbillModel->where(array('toid' => $id))->save(array("status" => -4, 'delay' => 1, 'delay_num' => 0, 'delay_unpaid' => 0));
            } elseif ($status == 3) { //还款中
                $loanBill = $loanbillModel->where(array('toid' => $id))->find();
                if (!$loanBill || empty($loanBill)) {
                    $this->createOrderBill($daifuWitch, $loanOrderModel, $paymentModel, $loanbillModel, $loanOrder, $id);
                } else {
                    $loanbillModel->where(array('toid' => $id))->save(array("status" => 0, "add_time" => time(), 'repayment_time' => array('exp', "add_time+" . ($delayDay * 86400))));
                }
            } elseif ($status == 4) { //续期中

                $delayRate = C('PostPone_RATE');
                $delayDefaultDays = C('DELAY_DEFAULT_DAYS');
                $delayDefaultDays = $delayDefaultDays > 0 ? $delayDefaultDays : 7;
                $days = I("day", $delayDefaultDays);

                $billInfo = array(
                    'status' => 4,
                    'delay' => 1,
                    'delay_rate' => $delayRate,
                    'delay_day' => array('exp', "delay_day+$delayDay"),
//                    'delay_unpaid' => array("exp", "delay_unpaid+$delayFee"),
                    'delay_num' => array('exp', "delay_num+1"),
                    'repayment_time' => array('exp', "repayment_time+" . (intval($delayDay) * 86400)),
                );
                $loanbillModel->where(array('toid' => $id))->save($billInfo);
            } elseif ($status == 5) { //已还清
                $loanBill = $loanbillModel->where(array('toid' => $id))->find();
                if (!$loanBill || empty($loanBill)) {
                    $this->createOrderBill($daifuWitch, $loanOrderModel, $paymentModel, $loanbillModel, $loanOrder, $id);
                }
                $billInfo = array('status' => 2, 'repay_time' => time());
                $loanbillModel->where(array('toid' => $id))->save($billInfo);
            } elseif ($status == 6) { //已逾期
                $loanBill = $loanbillModel->where(array('toid' => $id))->find();
                if (!$loanBill || empty($loanBill)) {
                    $this->createOrderBill($daifuWitch, $loanOrderModel, $paymentModel, $loanbillModel, $loanOrder, $id);
                }
                $billInfo = array('status' => 1, 'overdue_settime' => time());
                $loanbillModel->where(array('toid' => $id))->save($billInfo);
            }
        }
        $this->success("操作成功");
    }

    private function createCondition()
    {
        if (I("oid")) {
            $where["oid"] = trim(I("oid"));
        }
        if (I("telnum")) {
            $telnum = trim(I("telnum"));
            $userModel = D("User");

            $user = $userModel->where(array("telnum" => $telnum))->find();
            if ($user) {
                $where["uid"] = $user['id'];
            } else {
                $where["uid"] = null;
            }
        }
        $pendingType = I("__type__");
        if ($pendingType && $pendingType == 2) {
            $where["loan_total"] = array('gt', 1);
        }
        $audit_status = I("audit_state", -100);
        if (in_array(intval($audit_status), array(0, 1, 2, 3, 4, 5, 6, 7))) {
            if (in_array($audit_status, array(0, 1, 2))) {
                $where["pending"] = $audit_status;
            }
            if ($audit_status == 1) {
                $where["status"] = 0;
            }
            if ($audit_status == 4) {
                $where["status"] = 0;
                $where["pending"] = 1;
                $where["delay"] = 1;
                $where['_string'] = '(`start_time`+(`time`+`delay_day`)*86400)>' . time();
            }
            if ($audit_status == 5) {
                $where["status"] = 2;
            }
            if ($audit_status == 6) { //已逾期
                $where["pending"] = 1;
//                $where['_query']= array(time()=>array('lt','start_time+(time+delay_day)*86400'), 'status'=>1,'OR');
                $where['_string'] = 'status=1 OR ((`start_time`+(`time`+`delay_day`)*86400)<' . time() . ' AND status!=2)';
            }
            if ($audit_status == 3) {
//                $where["status"] = 0;
//                $where["pending"] = 1;
//                $where["delay"] = array('eq', 0);
                $where['_string'] = '((`start_time`+(`time`+`delay_day`)*86400)>=' . time() . ') AND status=0 AND pending=1 AND delay=0';
            }
            if ($audit_status == 7) {
                $where["pending"] = 1;
            }
        }
        if (I("stimeStart")) {
            $where["add_time"] = array("EGT", strtotime(urldecode(I("stimeStart"))));
        }
        if (I("stimeEnd")) {
            $where["add_time"] = array("ELT", strtotime(urldecode(I("stimeEnd"))));
        }
        if (I("stimeStart") && I("stimeEnd")) {
            $where["add_time"] = array(array("EGT", strtotime(urldecode(I("stimeStart")))), array("ELT", strtotime(urldecode(I("stimeEnd")))));
        }

        if (I("p_start_time")) {
            $where["start_time"] = array("EGT", strtotime(urldecode(I("p_start_time"))));
//            $where['status'] = array('in', '0,1');
        }
        if (I("p_end_time")) {
            $where["start_time"] = array("ELT", strtotime(urldecode(I("p_end_time"))));
//            $where['status'] = array('in', '0,1');
        }
        if (I("p_start_time") && I("p_end_time")) {
            $where["start_time"] = array(array("EGT", strtotime(urldecode(I("p_start_time")))), array("ELT", strtotime(urldecode(I("p_end_time")))));
//            $where['status'] = array('in', '0,1');
        }

        if (I("repay_timeStart")) {
            $where["start_time"] = array("EGT", strtotime(urldecode(I("repay_timeStart"))) . "-86400*time-86400*delay_day");
        }
        if (I("repay_timeEnd")) {
            $where["start_time"] = array("ELT", strtotime(urldecode(I("repay_timeEnd"))) . "-86400*time-86400*delay_day");
        }
        if (I("repay_timeStart") && I("repay_timeEnd")) {
            $where["start_time"] = array(array("EGT", array('exp', strtotime(urldecode(I("repay_timeStart"))) . "-86400*time-86400*delay_day")), array("ELT", array('exp', strtotime(urldecode(I("repay_timeEnd"))) . "-86400*time-86400*delay_day")));
        }

        if (I("add_timeStart")) {
            $where["add_time"] = array("EGT", strtotime(urldecode(I("add_timeStart"))));
        }
        if (I("add_timeEnd")) {
            $where["add_time"] = array("ELT", strtotime(urldecode(I("add_timeEnd"))));
        }
        if (I("add_timeStart") && I("add_timeEnd")) {
            $where["add_time"] = array(array("EGT", strtotime(urldecode(I("add_timeStart")))), array("ELT", strtotime(urldecode(I("add_timeEnd")))));
        }
        return $where;
    }

    private function createOrderBill($daifuWitch, $loanOrderModel, $paymentModel, $loanbillModel, $loanOrder, $id)
    {
        if (intval($daifuWitch) == 1) {
            $response = $paymentModel->dispensing($loanOrder); //执行出款给商家或用户的操作
            if (intval($response['code']) == 200) {
                $flag = $loanOrderModel->where(array('id' => $id))->save(array('pending' => -1));
            } else {
                $flag = $loanOrderModel->where(array('id' => $id))->save(array('reqerror' => $response['msg'], 'pending' => -2));
            }
        } else {
            $flag = $loanbillModel->createOrderBill($loanOrder);
        }
        return $flag;
    }

    public function delLoanOrder()
    {
        $id = I("id");
        if (!$id) {
            $this->error("订单参数有误");
        }
        $loanorderModel = D("Loanorder");
        $info = $loanorderModel->where(array("id" => $id))->find();
        if (!$info) {
            $this->error("该订单不存在");
        }
        if ($info["pending"] != 2) {
            $this->error("该订单状态不可被删除");
        }
        $result = $loanorderModel->where(array("id" => $id))->delete();
        if (!$result) {
            $this->error("订单操作失败");
        }
        $this->success("删除成功");
    }

    public function viewContract()
    {
        $id = I("id");
        if (!$id) {
            $this->error("参数错误");
        }
        $loanorderModel = D("Loanorder");
        $loanInfo = $loanorderModel->where(array("id" => $id))->relation(true)->find();
        if (!$loanInfo) {
            $this->error("不存在的借款订单");
        }
        $this->assign("data", $loanInfo);
        $contractTpl = C("contractTpl");
        $contractTpl = empty($contractTpl) ? "" : htmlspecialchars_decode(htmlspecialchars_decode($contractTpl));
        $loanData = json_decode($loanInfo["data"], true);
        $timeType = $loanInfo["timetype"] == 1 ? "月" : "日";
        if ($timeType == "月") {
            $endTime = strtotime("+" . intval($loanInfo["time"]) . " Month", $loanInfo["start_time"]);
        } else {
            $endTime = strtotime("+" . intval($loanInfo["time"]) . " Day", $loanInfo["start_time"]);
        }
        $sign = "<img src='data:image/png;base64," . $loanInfo["sign"] . "' width='110px' />";
        $infoModel = D("Info");
        $userInfo = $infoModel->getAuthInfo($loanInfo["uid"]);
        $addessInfo = json_decode($userInfo["addess"], true);
        $contractTpl = str_replace("｛借款人名称｝", $loanInfo["name"], $contractTpl);
        $contractTpl = str_replace("｛借款人身份证号｝", $loanData["idcard"], $contractTpl);
        $contractTpl = str_replace("｛借款人手机号｝", $loanInfo["user"]["telnum"], $contractTpl);
        $contractTpl = str_replace("｛借款金额大写｝", cny($loanInfo["money"]), $contractTpl);
        $contractTpl = str_replace("｛借款金额小写｝", $loanInfo["money"], $contractTpl);
        $contractTpl = str_replace("｛借款期限类型｝", $timeType, $contractTpl);
        $contractTpl = str_replace("｛借款利息｝", floatval($loanInfo["interest"]), $contractTpl);
        $contractTpl = str_replace("｛借款开始日｝", date("Y年m月d日", $loanInfo["start_time"]), $contractTpl);
        $contractTpl = str_replace("｛借款结束日｝", date("Y年m月d日", $endTime), $contractTpl);
        $contractTpl = str_replace("｛借款人用户名｝", $loanInfo["user"]["telnum"], $contractTpl);
        $contractTpl = str_replace("｛收款银行账号｝", $loanInfo["banknum"], $contractTpl);
        $contractTpl = str_replace("｛收款银行开户行｝", $loanInfo["bankname"], $contractTpl);
        $contractTpl = str_replace("｛逾期利息｝", floatval($loanInfo["overdue"]), $contractTpl);
        $contractTpl = str_replace("｛借款人签名｝", $sign, $contractTpl);
        $contractTpl = str_replace("｛合同签订日期｝", date("Y 年 m 月 d 日", $loanInfo["add_time"]), $contractTpl);
        $contractTpl = str_replace("｛借款人住所｝", $addessInfo["addess"] . $addessInfo["addessMore"], $contractTpl);
        $this->assign("tpl", $contractTpl);
        $this->display();
    }

    public function setoverdue()
    {
        $id = I("id", 0, "intval");
        $oid = I("oid", 0, "intval");
        $quota = I("quota", 0, "floatval");
        if (!$id || !$quota || !$oid) {
            $this->error("参数有误");
        }
        $trans = M();
        $trans->startTrans();
        try {
            $loanbillModel = D("Loanbill");
            $loanorderModel = D("Loanorder");
            $days = I("day", 7);
            $delayDefaultDays = C('DELAY_DEFAULT_DAYS');
            $delayDefaultDays = $delayDefaultDays > 0 ? $delayDefaultDays : 7;
            $delayRate = C('PostPone_RATE');
            $delayInfo = array(
                'delay' => 1,
                'delay_day' => array("exp", "delay_day+$delayDefaultDays"),
//                'delay_unpaid' => array("exp", "delay_unpaid+$quota"),
                'delay_rate' => $delayRate, 'status' => 4);
            $res = $loanbillModel->where(array("id" => $id))->save(array_merge($delayInfo, ['overdue_xq' => $quota]));
            if (!$res) {
                throw new Exception("Failed to save loanbill");
            }
            $res2 = $loanorderModel->where(array("oid" => $oid))->save($delayInfo);
            if ($res2) {
                throw new Exception("Failed to save loanorder");
            }
            $trans->commit();
            $this->success("操作成功");
        } catch (Exception $e) {
            $trans->rollback();
            $this->error("操作失败");
        }
    }

    public function importOrder()
    {
        vendor("PHPExcel.Classes.PHPExcel");
        $path = I("path");
        if (!$path) {
            $this->error("参数有误");
        }
        $basePATH = APP_PATH . "../Public/Upload/";
        $filePath = $basePATH . $path;
        if (!file_exists($filePath)) {
            $this->error("文件不存在");
        }

        $phpExcel = \PHPExcel_IOFactory::load($filePath);
        $excel_array = $phpExcel->getSheet(0)->toArray();
        $data = $this->_filterExecel($excel_array);

        $add_total = $this->_importOrderInfos($data);
        if ($add_total > 0) {
            $last_count = count($data) - $add_total;
            $this->success('数据导入成功，总共导入' . $add_total . '数据,剩余' . $last_count . '条数据未导入成功');
        } else {
            $this->error('数据导入失败！');
        }
    }

    private function _filterExecel($rows)
    {
        $headers = $rows[0];
        $headers = array_filter($headers);
        $data = [];
        foreach ($rows as $key => $row) {
            $tmpRow = [];
            if ($key != 0) {
                foreach ($headers as $k => $name) {
                    $tmpRow[$name] = $row[$k];
                }
                array_push($data, $tmpRow);
            }
        }
        return $data;
    }

    public function _importOrderInfos($rows)
    {
        $add_total = 0;
        $userModel = D("User");
        $loanorderModel = D("Loanorder");
        $infoModel = D("info");

        foreach ($rows as $key => $row) {
            $username = isset($row['用户名/电话']) ? $row['用户名/电话'] : null;
            $uid = isset($row['用户ID']) ? $row['用户ID'] : null;
            $firstname = isset($row['firstname']) ? $row['firstname'] : '';
            $lastname = isset($row['lastname']) ? $row['lastname'] : '';
            $bankCardNo = isset($row['银行卡号']) ? $row['银行卡号'] : '';
            $bankName = isset($row['开户行']) ? $row['开户行'] : '';
            $bankPhone = isset($row['银行预留手机号']) ? $row['银行预留手机号'] : '';
            $loanTime = isset($row['借款期限']) ? $row['借款期限'] : 7;
            $quota = isset($row['借款额度']) ? $row['借款额度'] : C('DEFAULT_QUOTA');
            $loantype = C('Loan_TYPE');
            if ($loanTime <= 0) {
                $loanTime = 7;
            }
            if (isMobile($username)) {
                $currentMobile = $username;
            } else if ($bankPhone) {
                $currentMobile = $bankPhone;
            }
            if ($uid) {
                $user = $userModel->where(array("id" => $uid))->find();
            } else {
                $user = $userModel->where(array("telnum" => $username))->find();
            }

            if ($user) {
//                $userInfo = $infoModel->where(array("uid" => $user['id']))->find();
//                $bankInfo = json_decode($userInfo['bank'], true);
//                $identityInfo = json_decode($userInfo['identity'], true);
//                $currentMobile = $currentMobile && strlen($currentMobile) > 0 ? $currentMobile : $userInfo['mobile'];
//                $realName = $firstname && strlen($firstname) > 0 ? $firstname : 0;
//                $realName .= $lastname && strlen($lastname) > 0 ? $lastname : 0;
//                $realName = $realName && strlen($realName) > 0 ? $realName : $identityInfo['name'];
//                $bankName = $bankName && strlen($bankName) > 0 ? $bankName : $bankInfo['bankName'];
//                $bankPhone = $bankPhone && strlen($bankPhone) > 0 ? $bankPhone : $bankInfo['bankPhone'];
//                $bankCardNo = $bankCardNo && strlen($bankCardNo) > 0 ? $bankCardNo : $bankInfo['bankNum'];
//                $loanInfo = array(
//                    "uid" => $user['id'],
//                    "oid" => $loanorderModel->newOrder(array("loantype" => $loantype)),
//                    "money" => $quota,
//                    "time" => $loanTime,
//                    "timetype" => $loantype,
//                    "name" => $realName,
//                    "bankname" => $bankName,
//                    "banknum" => $bankCardNo,
//                    "interest" =>intval($loantype)==0?C('Interest_D'):C('Interest_M'),
//                    "reply_rate" =>C('Interest_R'),
//                    "start_time" =>time(),
//                    "overdue" => ,
//                    "add_time" => $user[''],
//                    "sign" => $user[''],
//                    "data" => $user[''],
//                    "status" => $user[''],
//                    "pending" => $user[''],
//                    "delay" => $user[''],
//                    "delay_day" => $user[''],
//                    "delay_rate" => $user[''],
//                    "delay_num" => $user[''],
//                    "error" => $user[''],
//                    "sid" => $user[''],
//                    "reqerror" => $user[''],
//                    "notify" => $user[''],
//                    "rid" => $user['']
//                );
//                ($uid, $loanTime = 7, $sid = 1, $loanTimeInfo = array(), $user = null, $infoData = null, $loanTotal = 0)
                $flag = $loanorderModel->addUserOrder($uid, $loanTime, 1, array(), $user);
                if ($flag) {
                    $add_total++;
                }
            }
        }
        return $add_total;
    }

    public function DeleteLoan()
    {
        if ($this->ispost()) {
            $ids = I('ids');
            $type = I('type');
            if (($type == 1 && count($ids) == 0) || empty($type)) {
                $this->error('要删除的数据必须填写');
            }
            $where = $this->createCondition();
            $trans = M();

            $trans->startTrans();
            try {
                $loanbillModel = D("Loanbill");
                $loanOrderModel = D('Loanorder');
                if ($type == 2) {
                    $where = $this->createCondition();
                    if (empty($where)) {
                        throw new Exception("请先搜索再执行删除");
                    }
                    $loans = $loanOrderModel->where($where)->select();
                    $idset = array();
                    foreach ($loans as $loan) {
                        array_push($idset, $loan['id']);
                    }
                    $idIn = implode(',', (array)$idset);
                    if (empty($idset) || count($idset) == 0) {
                        throw new Exception('没有要删除的数据');
                    }
                } else {
                    $idIn = implode(',', (array)$ids);
                }
                $billTotal = $loanbillModel->where(array('toid' => array('in', $idIn)))->count();
                if ($billTotal > 0) {
                    $billStatus = $loanbillModel->where(array('toid' => array('in', $idIn)))->delete();
                    if (!$billStatus) {
                        throw new Exception('Unable to delete loan bill');
                    }
                }
                $status = $loanOrderModel->where(array('id' => array('in', $idIn)))->delete();
                if (!$status) {
                    throw new Exception('Unable to delete loan order,' . $loanOrderModel->getLastSql());
                }
                $trans->commit();
                $this->success("操作完成");
            } catch (Exception $e) {
                $trans->rollback();
                $this->error('操作失败:' . $e->getMessage());
            }

        } else {
            $this->error('请求方法不支持！');
        }
    }

    public function generatePayUrl()
    {
        if ($this->ispost()) {
            //pay_type: pay_type, amount: amount, orderId: orderId
            $orderId = I('orderId');
            $payType = I('pay_type');
            $uid = I('uid');
            if (!$uid || !$orderId || !$payType) {
                $this->error('参数错误');
            }
            $amount = I('amount');
            if ($payType == 1 && !$amount) {
                $this->error('金额必填');
            }
            $paymentModel = D("Payment");
            $payorderModel = D("Payorder");
            $ccyNo = C('CURRENCY');
            $pemail = C('PAY_ORDER_EMAIL') ? C('PAY_ORDER_EMAIL') : "admin@cashgo.com";
            $userModel = D("User");
            $userInfo = $userModel->where(["id" => $uid])->find();
            $pname = $userInfo['firstname'] . $userInfo['lastname'];

            $needAdd = true;
            $billIdArray = [];
            if ($payType == 2) {
//                $payOrder = $payorderModel->where(["loid" => $orderId, "uid" => $userInfo["id"], 'pay_type' => 2])->find();
//                if ($payOrder && $payOrder['status'] == 0) {
//                    $needAdd = false;
//                    $localOrderId = $payOrder["oid"];
//                    $amount = $payOrder["money"];
//                } else {
//
//                }
                $loanorderModel = D("Loanorder");
                $loanbillModel = D("Loanbill");
                $loanOrder = $loanorderModel->where(["uid" => $userInfo["id"], "oid" => $orderId])->find();
                $loanBill = $loanbillModel->where(["uid" => $userInfo["id"], "oid" => $orderId])->select();
                if ($loanBill && !empty($loanBill)) {
                    $billIdArray = [];
                    foreach ($loanBill as $bill) {
                        $billIdArray[] = $bill['id'];
                    }
                }
                $amount = $loanorderModel->calcRepayMoney($loanOrder); //全额还款需要支付的费用
            }
            $localOrderId = "GL" . date("YmdHis") . rand(10000, 99999) . ($userInfo["id"]) . rand(0, 9);
            if ($needAdd) {
                $payOrderInfo = array(
                    'pay_type' => $payType,
                    "uid" => $userInfo["id"],
                    "loid" => $orderId,
                    "billlist" => json_encode($billIdArray, JSON_UNESCAPED_UNICODE),
                    "money" => $amount,
                    'oid' => $localOrderId,
                    "status" => 0,
                    "add_time" => time(),
                    "pay_time" => 0);
                $payorderModel->add($payOrderInfo);
            }
            $order = array(
                'oid' => $orderId,
                'name' => "订单部分付款",
                'money' => $amount,
                'name' => $pname,
                'pemail' => $pemail,
                'phone' => $userInfo['telnum'],
                'ccy_no' => $ccyNo,
                'bank_code' => 1,
            );
            $url = $paymentModel->buildRequestUrl($order, $localOrderId);
            if ($url) {
                $matches = [];
                if (preg_match("#\/qrcode\/([a-zA-ZA-Z0-9]+)\/#", $url, $matches)) {
                    if ($matches && count($matches) > 1) {
                        $upid = $matches[1];
                        $payorderModel->where(['oid' => $localOrderId])->save(array("upid" => $upid));
                    }
                }
            }
            $this->success($url);
        } else {
            $this->error('请求方法不支持！');
        }
    }

    public function export()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL | E_STRICT | E_NOTICE);
        ini_set('max_execution_time', 864000);
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $where = $this->createCondition();
        $loanorderModel = D("Loanorder");
        $loans = $loanorderModel->where($where)->select();
//        echo $loanorderModel->getLastSql();
//        exit;
        $loanbillModel = D("Loanbill");
        $infoModel = D('Info');
        $headers = array("订单号", "用户名", '手机号码', "借款金额", "借款期限", "预期收益", '预期用户到账金额', '预期用户应还金额', "申请时间", '最终还款时间', '续期次数', '续期总天数', "开户名称", "收款银行", "银行卡号", "还款进度", "已还金额", '最近一笔账单还款', '逾期费用', '驳回原因', '备注', "状态");
        $headerKeys = array("oid", "username", "money", "times", "interest_money", "add_time", "name", "bankname", "banknum", "bill_status", "repay_money", "delay_num", 'remark', "status");
        include_once(APP_PATH . "Lib/Util/XlsTools.php");
        $xlsTools = new XlsTools();
//        $xlsTools->download();
        $xlsTools->start(array(
            'title' => $headers,//列名
            'type' => 'xlsx', //导出的excel的类型
            'name' => 'loanorder' //导出的excel的文件名
        ));
        $orderDatas = array();
        foreach ($loans as $i => $loan) {
            $loans[$i]["interest_money"] = toMoney($loans[$i]["interest"] * $loans[$i]["time"] * $loans[$i]["money"]);
            $bills = $loanbillModel->field(['status', 'money', 'repayment_time'])->where(array("toid" => $loans[$i]["id"]))->select();
//            $repayMoney = $loanbillModel->where(array("oid" => $loans[$i]['oid'], "status" => array("IN", "2,3")))->sum("money");
            $loans[$i]["hasBillNum"] = 0;
            $loans[$i]["overdueBillNum"] = 0;
            $loans[$i]["BillNum"] = 0;
            $repayMoney = 0;
            $lastRepayMentTime = 0;
            foreach ($bills as $bill) {
                if (in_array(intval($bill['status']), array(2, 3))) {
                    $loans[$i]["hasBillNum"]++;
                    $repayMoney += floatval($bill['money']);
                }
                if (in_array(intval($bill['status']), array(2))) {
                    $lastRepayMentTime = $bill['repayment_time'];
                }
                if (in_array(intval($bill['status']), array(3))) {
                    $loans[$i]["hasBillNum"]++;
                }
                $loans[$i]["BillNum"]++;
            }
            $interestMoney = intval($loans[$i]["interest"] * $loans[$i]["time"] * $loans[$i]["money"]);
            $loans[$i]["interest_money"] = toMoney($interestMoney);
            //到账金额
            $loans[$i]["get_money"] = toMoney($loans[$i]["money"] - $interestMoney);
            //还款费用
//            $loans[$i]['repay_fee'] = intval($loans[$i]["money"] * $loans[$i]["time"] * $loans[$i]["reply_rate"]);
            $loans[$i]['repay_fee'] = $loanorderModel->calcRepayFee($loans[$i]);
            //续期费用
            $loans[$i]['delay_fee'] = intval($loans[$i]["money"] * $loans[$i]["delay_day"] * $loans[$i]["delay_rate"]);
            //应还金额
//            到期时间
            $loans[$i]["end_time"] = $loans[$i]["add_time"] + ($loans[$i]["time"] * 86400);
            if ($loans[$i]["delay"] && $loans[$i]["delay_day"] > 1) {
                $loans[$i]["end_time"] += $loans[$i]["delay_day"] * 86400;
            }
            $loans[$i]["to_money"] = toMoney(intval($loans[$i]["money"] + $loans[$i]["money"] * $loans[$i]["time"] * $loans[$i]["reply_rate"]));
            // "status" => array("IN", "2,3")
//             = $loanbillModel->where(array("toid" => $loans[$i]["id"]))->count();
//             = $loanbillModel->where(array("toid" => $loans[$i]["id"], "status" => 3))->count();
//            $userinfo = $infoModel->getAuthInfo($loans[$i]['uid'], 'identity');
//            $userinfo = json_decode($userinfo, true);
            $loans[$i]["username"] = $loans[$i]["name"];

            $loans[$i]["repay_money"] = toMoney(intval($repayMoney));
            $loans[$i]['bill_status'] = "已还 {$loans[$i]['BillNum']} 期 | 共 {$loans[$i]['BillNum']} 期 | 逾期还款 {$loans[$i]['overdueBillNum']} 次";
            $loans[$i]['times'] = $loans[$i]['time'] . ($loans[$i]['timetype'] == 1 ? "月" : "天");
            $loans[$i]['add_time'] = date('Y-m-d H:i:s', $loans[$i]['add_time']);
            $loanData = json_decode($loans[$i]['data'], true);
            $phoneNumber = $loanData['phoneNumber'];
            if (!$phoneNumber) {
                $userModel = D("User");
                $cUser = $userModel->where(array("id" => $loan['uid']))->find();
                if (!empty($cUser)) $phoneNumber = $cUser['telnum'];
            }
            $statusText = "";
            if ($loan['pending'] == 0) {
                $statusText = "待审核";
            } else if ($loan['pending'] == 2) {
                $statusText = "已拒绝";
            } else if ($loan['pending'] == 1) {
                $repayTime = $loanorderModel->getOrderRepayTime($loan);
                if ($repayTime < time() && $loan['status'] != 2) {
                    $statusText = "已逾期";
                } elseif ($repayTime > time() && $loan['status'] != 2 && $loan['delay'] == 1) {
                    $statusText = "已延期";
                } elseif ($loan['status'] == 0) {
                    $statusText = "申请通过,待还款";
                } elseif ($loan['status'] == 1) {
                    $statusText = "已逾期";
                } elseif ($loan['status'] == 2) {
                    $statusText = "已还清";
                }
            }
            $loans[$i]['status'] = $statusText;

            $lastRepayTime = $loan['start_time'] + $loan['time'] * 86400;
            if ($loan['delay_num'] > 0) {
                $lastRepayTime += $loan['delay_day'] * 86400;
            }
            $overdueFee = 0;
            $overdueDay = floor((time() - $lastRepayTime) / 86400) - $loan['time'];//逾期天数;
            if ($overdueDay > 0) {
                $overdueFee = intval($loan['overdue'] * $overdueDay * $loan['money']);
            }

//                $loans[$i]['money_b']= $loans[$i]['money_b']."".C('CURRENCY');
//                $loans[$i]['interest_money_b']= $loans[$i]['interest_money']."".C('CURRENCY');
//                $loans[$i]['repay_money_b']= $loans[$i]['repay_money']."".C('CURRENCY');
            $orderData = array(
                "oid" => $loans[$i]["oid"],
                "username" => $loans[$i]["username"],
                "phoneNumber" => $phoneNumber,
                "money" => $loans[$i]["money"],
                "times" => $loans[$i]["times"],
                "interest_money" => $loans[$i]["interest_money"],
                "get_money" => $loans[$i]["get_money"],
                "repayment_money" => $loanorderModel->calcRepayMoney($loans[$i]),
                "add_time" => $loans[$i]["add_time"],
                "last_repay_time" => date('Y-m-d H:i:s', $lastRepayTime),
                "delay_num" => $loans[$i]["delay_num"],
                "delay_day" => $loans[$i]["delay_day"],
                "name" => $loans[$i]["name"],
                "bankname" => $loans[$i]["bankname"],
                "banknum" => $loans[$i]["banknum"],
                "bill_status" => $loans[$i]["bill_status"],
                "repay_money" => $loans[$i]["repay_money"],
                "lastRepayMentTime" => $lastRepayMentTime > 0 ? date('Y-m-d H:i:s', $lastRepayMentTime) : null,
                'overdueFee' => $overdueFee,
                'error' => $loans[$i]["error"],
                'remark' => $loans[$i]["remark"],
                "status" => $loans[$i]["status"]
            );
            $xlsTools->oneData($orderData);

//            $xlsTools->multiData($orderData);
        }
        $xlsTools->end();

    }

    public function export1()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL | E_STRICT | E_NOTICE);
        ini_set('max_execution_time', 864000);
        ini_set('memory_limit', '2048M');
        set_time_limit(0);


        $where = $this->createCondition();
        $loanorderModel = D("Loanorder");
        $loans = $loanorderModel->where($where)->select();
        $loanbillModel = D("Loanbill");
        $infoModel = D('Info');
        $headers = array("订单号", "用户名", "借款金额", "借款期限", "预期收益", "申请时间", "开户名称", "收款银行", "银行卡号", "还款进度", "已还金额", "续期次数", '备注', "状态");
        $headerKeys = array("oid", "username", "money", "times", "interest_money", "add_time", "name", "bankname", "banknum", "bill_status", "repay_money", "delay_num", 'remark', "status");
//        include_once dirname(__DIR__) . "/../util/XlsTools.php";
//        $xlsTools = new XlsTools();
//        $xlsTools->start(array(
//            'title' => $headers,//列名
//            'type' => 'xls', //导出的excel的类型
//            'name' => 'orders.xls' //导出的excel的文件名
//        ));
        $fileName = 'orders';
        header('Content-Encoding: UTF-8');
        header("Cache-Control: max-age=0");
        header("Content-Description: File Transfer");
        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header('Content-Disposition: attachment;filename="' . $fileName . '.csv"');

        //打开php标准输出流
        $fp = fopen('php://output', 'a');

        //添加BOM头，以UTF8编码导出CSV文件，如果文件头未添加BOM头，打开会出现乱码。
        fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($fp, $headers);
        $orderDatas = array();
        foreach ($loans as $i => $loan) {
            $loans[$i]["interest_money"] = toMoney($loans[$i]["interest"] * $loans[$i]["time"] * $loans[$i]["money"]);
            $bills = $loanbillModel->field(['status', 'money'])->where(array("toid" => $loans[$i]["id"]))->select();
//            $repayMoney = $loanbillModel->where(array("oid" => $loans[$i]['oid'], "status" => array("IN", "2,3")))->sum("money");
            $loans[$i]["hasBillNum"] = 0;
            $loans[$i]["overdueBillNum"] = 0;
            $loans[$i]["BillNum"] = 0;
            $repayMoney = 0;
            foreach ($bills as $bill) {
                if (in_array(intval($bill['status']), array(2, 3))) {
                    $loans[$i]["hasBillNum"]++;
                    $repayMoney += floatval($bill['money']);
                }
                if (in_array(intval($bill['status']), array(3))) {
                    $loans[$i]["hasBillNum"]++;
                }
                $loans[$i]["BillNum"]++;
            }

            // "status" => array("IN", "2,3")
//             = $loanbillModel->where(array("toid" => $loans[$i]["id"]))->count();
//             = $loanbillModel->where(array("toid" => $loans[$i]["id"], "status" => 3))->count();
//            $userinfo = $infoModel->getAuthInfo($loans[$i]['uid'], 'identity');
//            $userinfo = json_decode($userinfo, true);
            $loans[$i]["username"] = $loans[$i]["name"];

            $loans[$i]["repay_money"] = toMoney($repayMoney);
            $loans[$i]['bill_status'] = "已还 {$loans[$i]['BillNum']} 期 | 共 {$loans[$i]['BillNum']} 期 | 逾期还款 {$loans[$i]['overdueBillNum']} 次";
            $loans[$i]['times'] = $loans[$i]['time'] . ($loans[$i]['timetype'] == 1 ? "月" : "天");
            $loans[$i]['add_time'] = date('Y-m-d H:i:s', $loans[$i]['add_time']);

            $statusText = "";
            if ($loan['pending'] == 0) {
                $statusText = "待审核";
            } else if ($loan['pending'] == 2) {
                $statusText = "已拒绝";
            } else if ($loan['pending'] == 1) {
                if ($loan['status'] == 0) {
                    $statusText = "申请通过,待还款";
                } elseif ($loan['status'] == 1) {
                    $statusText = "已逾期";
                } elseif ($loan['status'] == 2) {
                    $statusText = "已还清";
                }
            }
            $loans[$i]['status'] = $statusText;
//                $loans[$i]['money_b']= $loans[$i]['money_b']."".C('CURRENCY');
//                $loans[$i]['interest_money_b']= $loans[$i]['interest_money']."".C('CURRENCY');
//                $loans[$i]['repay_money_b']= $loans[$i]['repay_money']."".C('CURRENCY');
            $orderData = array(
                "oid" => $loans[$i]["oid"],
                "username" => $loans[$i]["username"],
                "money" => $loans[$i]["money"],
                "times" => $loans[$i]["times"],
                "interest_money" => $loans[$i]["interest_money"],
                "interest_money" => $loans[$i]["interest_money"],
                "add_time" => $loans[$i]["add_time"],
                "name" => $loans[$i]["name"],
                "bankname" => $loans[$i]["bankname"],
                "banknum" => $loans[$i]["banknum"],
                "bill_status" => $loans[$i]["bill_status"],
                "repay_money" => $loans[$i]["repay_money"],
                "delay_num" => $loans[$i]["delay_num"],
                "remark" => $loans[$i]["remark"],
                "status" => $loans[$i]["status"]
            );
//            $xlsTools->multiData($orderData);
            fputcsv($fp, $orderData);
            ob_flush();
            flush();
        }
//        $xlsTools->end();
//        $xlsTools->download();
//            echo "<pre>";
//            print_r($loans);
//            exit;
//        $fileName = $this->_exportLoanOrder($loans);
//        $baseUrl = $this->getBaseUrl();
//        $this->success("导出成功", $baseUrl . "/" . $fileName);

    }

    public function getBaseUrl()
    {
        $domainRules = C('APP_SUB_DOMAIN_RULES');
        $domans = array_keys($domainRules);
        $baseUrl = $domans [0];
        if (!strpos($baseUrl, "http") && !strpos($baseUrl, "https")) {
            $baseUrl = "http://" . $baseUrl;
        }
        return $baseUrl;
    }

    private function _exportLoanOrder($loans)
    {
        vendor("PHPExcel.Classes.PHPExcel");
        $fileName = date('Y-m-d_H.i.s') . '_loan_order' . '.xls';
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/Public/Upload/orders';
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $filePath .= "/" . $fileName;
//        if(!file_exists($filePath)) {
//            touch($filePath);
//        }
        $phpExcel = new \PHPExcel();

        $phpExcel->createSheet();
        $phpSheet = $phpExcel->getActiveSheet();

        $headers = array("订单号", "用户名", "借款金额", "借款期限", "预期收益", "申请时间", "开户名称", "收款银行", "银行卡号", "还款进度", "已还金额", "续期次数", '备注', "状态");
        $headerKeys = array("oid", "username", "money", "times", "interest_money", "add_time", "name", "bankname", "banknum", "bill_status", "repay_money", "delay_num", '备注', "status");
        $headerTotal = count($headers);
        $headerMaps = array();
        for ($i = 0; $i < $headerTotal; $i++) {
            if ($i < 26) {
                $columnKey = chr($i + 65);
                $columnHeader = $columnKey . "1";
            } else {
                $columnKeys = chr($i % 26 + 65);
                $columnKey = implode("", array_pad($columnKeys, ceil($i / 26)));
                $columnHeader = $columnKey . "1";
            }
            $phpSheet->setCellValue($columnHeader, "" . $headers[$i]);
            $headerMaps[$columnKey] = $headerKeys[$i];
        };
        // 设置个表格标题

        $i = 2;
        foreach ($loans as $m => $loan) {
            foreach ($headerMaps as $headerKey => $headerMap) {
//                echo "<pre>";
//               print_r([$headerKey,$m,$loan[$headerMap]]);
                $phpSheet->setCellValue($headerKey . "" . $i, $loan[$headerMap]);
            }
            $i++;
        }
        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
//        header('Content-Disposition: attachment;filename=' . $fileName);
//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header('Cache-Control: max-age=0');
//        $objWriter->save("php://output");
        $objWriter->save($filePath);
        return 'Public/Upload/orders/' . $fileName;
    }
}