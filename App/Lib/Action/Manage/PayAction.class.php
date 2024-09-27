<?php

class PayAction extends CommonAction
{
    public function index()
    {
        $payorderModel = D("Payorder");
        $where = $this->createCondition();
        $count = $payorderModel->where($where)->count();
        import("ORG.Util.Page");
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $payorderModel->where($where)->order("add_time Desc")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
//        echo $payorderModel->getLastSql();
        $payTimeStart = strtotime(date('Y-m-d'));
        $payTimeEnd = strtotime(date('Y-m-d') . ' 23:59:59');
        $allPayWhere = $where;
        unset($allPayWhere['status']);
        $allSubmitTotal = $payorderModel->where($allPayWhere)->count();
        $allPayWhere['status'] = 1;
        $totalPayMoney = $payorderModel->where($allPayWhere)->sum('tomoney');
        $allPayTotal = $payorderModel->where($allPayWhere)->count();
        $yestPayWhere = $todayPayWhere = $where;
        unset($todayPayWhere['add_time']);
        $todayPayWhere['status'] = 1;
        $yestPayWhere['status'] = 1;
        $todayPayWhere = array_merge($todayPayWhere, ['pay_time' => array(array('gt', $payTimeStart), array('lt', $payTimeEnd))]);
        $todayPayMoney = $payorderModel->where($todayPayWhere)->sum('tomoney');
        echo $payorderModel->getLastSql();

        $yestdayStart = $payTimeStart - 86400;
        $yestdayEnd = $yestdayStart + 86399;
        unset($yestPayWhere['add_time']);
        $yestPayWhere = array_merge($yestPayWhere, ['pay_time' => array(array('gt', $yestdayStart), array('lt', $yestdayEnd))]);

        $yestdayPayMoney = $payorderModel->where($yestPayWhere)->sum('tomoney');
        $loanbillModel = D("Loanbill");
        $loanorderModel = D("Loanorder");
        foreach ($list as $row => $item) {
            $billlist = json_decode($list[$row]['billlist'], true);
            $orderIds = $loanbillModel->field("group_concat(oid) as oids")->where(array("id" => array("in", $billlist)))->group('id')->select();
            $orderIdArray = array();
            $order_ids = "";
            if (!empty($orderIds)) {
                foreach ($orderIds as $order) {
                    array_push($orderIdArray, $order['oids']);
                }
                $order_ids = implode(',', $orderIdArray);

            }
            $list[$row]['order_id'] = $order_ids;
            $loanInfo = $loanorderModel->field('remark')->where(array("oid" => $item['loid']))->find();
            $list[$row]['remark'] = $loanInfo['remark'];

        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->assign("totalPayMoney", $totalPayMoney);
        $this->assign("todayPayMoney", $todayPayMoney);
        $this->assign("yestdayPayMoney", $yestdayPayMoney);
        $this->assign("allSubmitTotal", $allSubmitTotal);
        $this->assign("allPayTotal", $allPayTotal);
        $this->display();
        return NULL;
    }

    public function proof()
    {
        $payProofModel = D("PayProof");
        $where = [];
        $where = $this->createProofCondition($where);
        if (!$where["status"]) {
            $where["status"] = 0;
        }
        import("ORG.Util.Page");
        $count = $payProofModel->where($where)->count();
        $pageSize = 10;
        $Page = new Page($count, $pageSize);
        $Page->setConfig("header", "条记录,每页显示" . $pageSize . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $payProofModel->where($where)->order("add_time Desc")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
        $loanOrderModel = D('loanorder');
        $payorderModel = D("Payorder");
        $loanbillDelayModel = D("LoanbillDelay");

        foreach ($list as $i => $row) {
            $loanOrder = $loanOrderModel->field('pending,status')->where(array("oid" => $row['order_id']))->find();
            $list[$i]['order_status'] = $loanOrder['status'];
            $list[$i]['pay_total'] = $payorderModel->where(array("loid" => $row['order_id']))->count();
            $list[$i]['delay_total'] = $loanbillDelayModel->where(array("oid" => $row['order_id']))->count();
        }
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }

    public function createProofCondition($where)
    {
        if (I("toid")) {
            $where['order_id'] = trim(I("toid"));
        }
        if (I("uname")) {
            $where['uname'] = trim(I("uname"));
        }
        if (I("add_timeStart")) {
            $where["add_time"] = array("EGT", strtotime(urldecode(I("add_timeStart"))));
        }
        if (I("add_timeEnd")) {
            $where["add_time"] = array("ELT", strtotime(urldecode(I("add_timeEnd"))));
        }
        if (I("add_timeStart") && I("add-timeEnd")) {
            $where["add_time"] = array(array("EGT", strtotime(urldecode(I("add_timeStart")))), array("ELT", strtotime(urldecode(I("add_timeEnd")))));
        }
        if (I("status")) {
            $where["status"] = trim(I("status"));
        }
        return $where;
    }

    public function delProof()
    {
        $id = I("id");
        $status = I("status", 0);
        if (!$id) {
            $this->error("订单参数有误");
        }
        $payProofModel = D("PayProof");
        $info = $payProofModel->where(array("id" => $id))->find();
        if (!$info) {
            $this->error("该订单不存在");
        }
        if ($status == -1) {
            $result = $payProofModel->where(array("id" => $id, 'status' => -4))->delete();
        } else {
            $result = $payProofModel->where(array("id" => $id, 'status' => ['neq', $status]))->save(['status' => $status]);
        }

        if (!$result) {
            $this->error("订单操作失败");
        }
        if ($status == -4) {
            $this->success("删除成功");
        } else {
            $this->success("移除到回收站成功");
        }

    }

    public function delOrder()
    {
        $id = I("id");
        if (!$id) {
            $this->error("订单参数有误");
        }
        $payorderModel = D("Payorder");
        $info = $payorderModel->where(array("id" => $id))->find();
        if (!$info) {
            $this->error("该订单不存在");
        }
        $result = $payorderModel->where(array("id" => $id))->delete();
        if (!$result) {
            $this->error("订单操作失败");
        }
        $payRecordModel = D('PayRecord');
        $admin = $this->isLogin();
        $payRecordInfo = array(
            "oid" => $info['loid'],
            "action" => 2,
            "pay_id" => $info['oid'],
            "mark" => '删除支付订单',
            "type" => 2,
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
            $where["loid"] = trim(I("toid"));
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
            $where["oid"] = trim(I("payid"));
        }
        if (I("upid")) {
            $where["upid"] = trim(I("upid"));
        }
        $status = I("status", -100);
        if ($status != -100) {
            $where["status"] = $status;
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
        if (I("pay_timeStart")) {
            $where["pay_time"] = array("EGT", strtotime(urldecode(I("pay_timeStart"))));
        }
        if (I("pay_timeEnd")) {
            $where["pay_time"] = array("ELT", strtotime(urldecode(I("pay_timeEnd"))));
        }
        if (I("pay_timeStart") && I("pay_timeEnd")) {
            $where["pay_time"] = array(array("EGT", strtotime(urldecode(I("pay_timeStart")))), array("ELT", strtotime(urldecode(I("pay_timeEnd")))));
        }
        if (I("__type__") && I("__type__") == 2) {
            $where["utr"] = array('NEQ', '');
        }
        return $where;
    }

    public function batchPayProofDelete()
    {
        $type = I('type');
        $payProofModel = D("PayProof");
        $ids = I("ids");
        if (empty($ids) || count($ids) <= 0) {
            $this->error("订单操作失败");
        }
//         if ($type == 2) {
//             $where = [];
//             $where = $this->createProofCondition($where);
// //            $where['status'] = 1;
//             $payIds = $payProofModel->field("group_concat(id) as pids")->where($where)->find();
//             $inIds = "";
//             if ($payIds && $payIds['pids']) {
//                 $inIds = $payIds['pids'];
//             }
//             if (strlen($inIds) <= 0) {
//                 $this->error("请选择要删除的内容");
//             }
//         } else {

//         }
        $inIds = implode(',', $ids);
        $result = $payProofModel->where(array("id" => array('in', $inIds)))->save(['status' => -4]);
        if (!$result) {
            $this->error("订单操作失败");
        }
        $this->success("操作成功");
    }

    public function proofTrash()
    {
        $payProofModel = D("PayProof");
        $result = $payProofModel->where(array("status" => -4))->delete();
        if (!$result) {
            $this->error("清理失败");
        }
        $this->success("回收站已清空");
    }

    public function batchPayDelete()
    {
        $type = I('type');
        $payorderModel = D("Payorder");
        $ids = I("ids");
        $payRecordModel = D('PayRecord');
        $admin = $this->isLogin();
        if ($type == 2) {
            $where = $this->createCondition();
            $where['status'] = 1;
            $payIds = $payorderModel->field("group_concat(id) as pids")->where($where)->find();
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
        $result = $payorderModel->where(array("id" => array('in', $inIds)))->delete();
        if (!$result) {
            $this->error("订单操作失败");
        }
        foreach ($inIds as $id) {
            $payorder = $payorderModel->where(array("id" => $id))->find();
            $payRecordInfo = array(
                "oid" => $payorder['loid'],
                "action" => 2,
                "pay_id" => $payorder['oid'],
                "mark" => '删除支付订单',
                "type" => 2,
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
        $payorderModel = D("Payorder");
        $payOrders = $payorderModel->where($where)->select();
        $loanbillModel = D("Loanbill");
        $loanorderModel = D("Loanorder");

        $infoModel = D('Info');
        $headers = array("订单流水号", "用户ID", "用户名/手机号", "借款人姓名", "借款金额", "关联贷款订单号", "支付单号", "还款金额", "UTR", "UTR图片", "付款凭证", "状态", "创建时间", "支付时间", "备注");
        include_once(APP_PATH . "Lib/Util/XlsTools.php");
        $xlsTools = new XlsTools();
        $xlsTools->download();
        $xlsTools->start(array(
            'title' => $headers,//列名
            'type' => 'xls', //导出的excel的类型
            'name' => 'payorder' //导出的excel的文件名
        ));

        foreach ($payOrders as $i => $item) {
            $billlist = json_decode($item['billlist'], true);
            $orderBills = $loanbillModel->field("group_concat(oid) as oids,sum(money) as allmoney")->where(array("id" => array("in", $billlist)))->group('id')->find();
            $order_ids = "";
            $userInfo = $infoModel->where(array("uid" => $item['uid']))->find();
            $identity = json_decode($userInfo["identity"], true);
            $loanInfo = $loanorderModel->field('remark')->where(array("oid" => $item['loid']))->find();
            $orderData = array(
                "订单流水号" => $item['id'],
                "用户ID" => $item["uid"],
                "用户名/手机号" => $userInfo["mobile"],
                "借款人姓名" => $userInfo["mobile"],
                "借款金额" => $identity['name'],
                "关联贷款订单号" => $orderBills['oids'],
                "支付单号" => $item['oid'],
                "还款金额" => $item['money'],
                "UTR" => $item['utr'],
                "UTR图片" => $item['utr_image'],
                "付款凭证" => $item["repay_image"],
                "状态" => $item["status"] == 1 ? "已支付" : "未支付",
                "创建时间" => $item["add_time"] ? date("Y-m-d H:i:s", $item["add_time"]) : "",
                "支付时间" => $item["pay_time"] ? date("Y-m-d H:i:s", $item["pay_time"]) : "",
                "备注" => $loanInfo['remark'],
            );
            $xlsTools->multiData($orderData);
        }
        $xlsTools->end();

    }

    public function proofExport()
    {

        $payProofModel = D("PayProof");
        $where = [];
        $where = $this->createProofCondition($where);
        $payOrders = $payProofModel->where($where)->select();

        $headers = array("订单流水号", "用户ID", "用户名/手机号", "借款人姓名", "借款金额", "关联贷款订单号", "支付单号", "还款金额", "UTR", "UTR图片", "付款凭证", "创建时间");
        include_once(APP_PATH . "Lib/Util/XlsTools.php");
        $xlsTools = new XlsTools();
        $xlsTools->download();
        $xlsTools->start(array(
            'title' => $headers,//列名
            'type' => 'xls', //导出的excel的类型
            'name' => 'payorder' //导出的excel的文件名
        ));

        foreach ($payOrders as $i => $item) {
//            $loanOrder=
            $orderData = array(
                "订单流水号" => $item['id'],
                "用户ID" => $item["uid"],
                "用户名/手机号" => $item["uname"],
                "借款人姓名" => $item["realname"],
                "借款金额" => $item['money'],
                "关联贷款订单号" => $item['order_id'],
                "支付单号" => $item['pay_id'],
                "还款金额" => $item['money'],
                "UTR" => $item['utr'],
                "UTR图片" => $item['utr_image'],
                "付款凭证" => $item["repay_image"],
//                "状态" => $item["status"] == 1 ? "已支付" : "未支付",
                "创建时间" => $item["add_time"] ? date("Y-m-d H:i:s", $item["add_time"]) : "",
            );
            $xlsTools->multiData($orderData);
        }
        $xlsTools->end();

    }
}