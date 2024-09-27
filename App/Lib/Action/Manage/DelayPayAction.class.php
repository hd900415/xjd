<?php

class DelayPayAction extends CommonAction
{
    public function index()
    {
        $loanbillDelayModel = D("LoanbillDelay");
        $where = $this->createCondition();
        $count = $loanbillDelayModel->where($where)->count();
        import("ORG.Util.Page");
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $loanbillDelayModel->where($where)->order("updated_at Desc,created_at Desc")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
        $payTimeStart = strtotime(date('Y-m-d'));
        $payTimeEnd = strtotime(date('Y-m-d') . ' 23:59:59');
        $allPayWhere = $where;
        unset($allPayWhere['delay_pay_status']);
        $allSubmitTotal = $loanbillDelayModel->where($allPayWhere)->count();
        $allPayWhere['delay_pay_status'] = 2;
        $totalPayMoney = $loanbillDelayModel->where($allPayWhere)->sum('tomoney');
        $allPayTotal = $loanbillDelayModel->where($allPayWhere)->count();
        $yestPayWhere = $todayPayWhere = $where;
        unset($todayPayWhere['created_at']);
        $todayPayWhere['delay_pay_status'] = 2;
        $yestPayWhere['delay_pay_status'] = 2;
        $todayPayWhere = array_merge($todayPayWhere, ['updated_at' => array(array('gt', $payTimeStart), array('lt', $payTimeEnd))]);
        $todayPayMoney = $loanbillDelayModel->where($todayPayWhere)->sum('tomoney');

        $yestdayStart = $payTimeStart - 86400;
        $yestdayEnd = $yestdayStart + 86399;
        unset($yestPayWhere['created_at']);
        $yestPayWhere = array_merge($yestPayWhere, ['updated_at' => array(array('gt', $yestdayStart), array('lt', $yestdayEnd))]);
        $yestdayPayMoney = $loanbillDelayModel->where($yestPayWhere)->sum('tomoney');

        $infoModel = D("Info");
        $loanorderModel = D("Loanorder");
        foreach ($list as $row => $item) {
            if ($item['uid']) {
                $user = $infoModel->where(array("uid" => $item['uid']))->find();
                $list[$row]['uname'] = $user['mobile'];
                $identity = json_decode($user['identity'], true);
                $list[$row]['realname'] = $identity['name'];
                $list[$row]['idcard'] = $identity['idcard'];
                $loanInfo = $loanorderModel->field('remark')->where(array("oid" => $item['oid']))->find();
                $list[$row]['remark'] = $loanInfo['remark'];
            }
//            if ($item['delay_pay_status'] == 2) $totalPayMoney += $item['delay_fee'];
        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->assign("totalPayMoney", $totalPayMoney);
        $this->assign("todayPayMoney", $todayPayMoney);
        $this->assign("yestdayPayMoney", $yestdayPayMoney);
        $this->assign("allSubmitTotal", $allSubmitTotal);
        $this->assign("allPayTotal", $allPayTotal);
        $this->display();
    }


    public function delOrder()
    {
        $id = I("id");
        if (!$id) {
            $this->error("订单参数有误");
        }
        $loanbillDelayModel = D("LoanbillDelay");
        $info = $loanbillDelayModel->where(array("id" => $id))->find();
        if (!$info) {
            $this->error("该订单不存在");
        }
        $result = $loanbillDelayModel->where(array("id" => $id))->delete();
        if (!$result) {
            $this->error("订单操作失败");
        }
        $payRecordModel = D('PayRecord');
        $admin = $this->isLogin();
        $payRecordInfo = array(
            "oid" => $info['oid'],
            "action" => 2,
            "pay_id" => $info['pay_id'],
            "mark" => '延期付款删除付款单',
            "type" => 1,
            "sid" => $admin['id'],
            "ip" => get_client_ip(),
            "create_at" => time(),
            "updated_at" => time()
        );
        $payRecordModel->add($payRecordInfo);
        $this->success("删除成功");
    }

    private function createCondition($where = array())
    {

        if (I("toid")) {

            $where["oid"] = trim(I("toid"));
        }
        if (I("uid")) {
            $where["uid"] = trim(I("uid"));
        }
        if (I("uname")) {
            $uname = trim(I("uname"));
            $userModel = D("User");
            $user = $userModel->where(array("telnum" => $uname))->find();
            $where["uid"] = $user['id'];
        }
        if (I("payid")) {
            $where["pay_id"] = trim(I("payid"));
        }
        if (I("upid")) {
            $where["upid"] = trim(I("upid"));
        }
        $status = I("status", -100);
        if ($status != -100) {
            $where["delay_pay_status"] = $status;
        }
        if (I("add_timeStart")) {
            $where["created_at"] = array("EGT", strtotime(urldecode(I("add_timeStart"))));
        }
        if (I("add_timeEnd")) {
            $where["created_at"] = array("ELT", strtotime(urldecode(I("add_timeEnd"))));
        }
        if (I("add_timeStart") && I("add_timeEnd")) {
            $where["created_at"] = array(array("EGT", strtotime(urldecode(I("add_timeStart")))), array("ELT", strtotime(urldecode(I("add_timeEnd")))));
        }
        if (I("pay_timeStart")) {
            $where["updated_at"] = array("EGT", strtotime(urldecode(I("pay_timeStart"))));
        }
        if (I("pay_timeEnd")) {
            $where["updated_at"] = array("ELT", strtotime(urldecode(I("pay_timeEnd"))));
        }

        if (I("pay_timeStart") && I("pay_timeEnd")) {
            $where["updated_at"] = array(array("EGT", strtotime(urldecode(I("pay_timeStart")))), array("ELT", strtotime(urldecode(I("pay_timeEnd")))));
        }
        if (I("__type__") && I("__type__") == 2) {
            $where["utr"] = array('NEQ', '');
        }
        return $where;
    }


    public function batchPayDelete()
    {
        $type = I('type');
        $loanbillDelayModel = D("LoanbillDelay");
        $ids = I("ids");
        $payRecordModel = D('PayRecord');
        $admin = $this->isLogin();
        if ($type == 2) {
            $where = $this->createCondition();
            $where['status'] = 1;
            $payIds = $loanbillDelayModel->field("group_concat(id) as pids")->where($where)->find();
            $inIds = "";
            if ($payIds && $payIds['pids']) {
                $inIds = $payIds['pids'];
            }
            if (strlen($inIds) <= 0) {
                $this->error("请选择要删除的内容");
            }
        } else {
            $inIds = implode(',', $ids);
        }
        $result = $loanbillDelayModel->where(array("id" => array('in', $inIds)))->delete();

        if (!$result) {
            $this->error("订单操作失败");
        }
        foreach ($inIds as $id) {
            $loanBillDelay = $loanbillDelayModel->where(array("id" => $id))->find();
            $payRecordInfo = array(
                "oid" => $loanBillDelay['oid'],
                "action" => 2,
                "pay_id" => $loanBillDelay['pay_id'],
                "mark" => '延期付款删除付款单',
                "type" => 1,
                "sid" => $admin['id'],
                "ip" => get_client_ip(),
                "create_at" => time(),
                "updated_at" => time()
            );
            $payRecordModel->add($payRecordInfo);
        }
        $this->success("删除成功");
    }

    public function export()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL | E_STRICT | E_NOTICE);
        ini_set('max_execution_time', 864000);
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $where = $this->createCondition();
        $loanbillDelayModel = D("LoanbillDelay");
        $payOrders = $loanbillDelayModel->where($where)->select();
        $loanbillModel = D("Loanbill");
        $loanorderModel = D("Loanorder");
        $infoModel = D('Info');
        $headers = array("ID", "用户ID", "用户名/手机号", "借款人姓名", "借款金额", "关联贷款订单号", "延期支付单号", "延期支付费用", "状态", "创建时间", "支付时间", '备注');
        include_once(APP_PATH . "Lib/Util/XlsTools.php");
        $xlsTools = new XlsTools();
        $xlsTools->download();
        $xlsTools->start(array(
            'title' => $headers,//列名
            'type' => 'xls', //导出的excel的类型
            'name' => 'payorder' //导出的excel的文件名
        ));

        foreach ($payOrders as $i => $item) {


            $order_ids = "";
            $userInfo = $infoModel->where(array("uid" => $item['uid']))->find();
            $identity = json_decode($userInfo["identity"], true);
            $loanInfo = $loanorderModel->field('remark')->where(array("oid" => $item['oid']))->find();
            $orderData = array(
                "ID" => $item['id'],
                "用户ID" => $item["uid"],
                "用户名/手机号" => $userInfo["mobile"],
                "借款人姓名" => $userInfo["mobile"],
                "借款金额" => $identity['name'],
                "关联贷款订单号" => $item['oid'],
                "延期支付单号" => $item['pay_id'],
                "延期支付费用" => $item['delay_fee'],
                "状态" => $item["delay_pay_status"] == 2 ? "支付" : "未支付",
                "创建时间" => $item["created_at"] ? date("Y-m-d H:i:s", $item["created_at"]) : "",
                "支付时间" => $item["updated_at"] ? date("Y-m-d H:i:s", $item["updated_at"]) : "",
                "备注" => $loanInfo['remark'],
            );
            $xlsTools->multiData($orderData);
        }
        $xlsTools->end();

    }

}