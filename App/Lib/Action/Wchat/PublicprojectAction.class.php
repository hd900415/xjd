<?php

class PublicprojectAction extends CommonAction
{
    public function index()
    {

        $this->display();
    }

    public function repayment()
    {
        $billId = I("id");
        if (!$billId) {
            $this->error("Product parameters are wrong");
        }
        //		$payorderModel = D("Payorder");
        //		$loanbillModel = D("Loanbill");
        //		$bill = $loanbillModel->where(array("id" => $billId))->find();
        //		if (!$bill) {
        //			$this->error("Bill does not exist");
        //		}
        //		if ($bill["status"] == 2 || $bill["status"] == 3) {
        //			$this->error("Current bill paid");
        //		}
        //		if ($bill["status"] == 4) {
        //			$this->error("Current bill has expired");
        //		}
        //		$userInfo = $this->isLogin();
        //		//$billMoney = toMoney($bill["money"] + $bill["interest"] + $bill["overdue"]);
        //		$billMoney = toMoney($bill["money"] + $bill["overdue"]);
        //		$order = $payorderModel->newOrder($userInfo["id"], $billMoney, array($billId));
        //		if (!$order) {
        //			$this->error("Failed to create payment order");
        //		}
        //		//var_dump($bill);
        //		$this->assign("data", $order);

        $productModel = D('product');
        $product = $productModel->where(['id' => $billId])->find();
        if (!$product) {
            $this->error("Product does not exist");
        }
        $this->assign("product", $product);
        $this->display();
        //$this->redirect("Pay/alipay", array("order" => $order));
        exit(0);
    }

    public function order()
    {
//		$loanorderModel = D("Loanorder");
//		$userInfo = $this->isLogin();
//		$list = $loanorderModel->getNoneList($userInfo["id"]);
//		$loanbillModel = D("Loanbill");
//		$hasMoney = $loanbillModel->where(array("uid" => $userInfo["id"], "status" => array("in", "0,1")))->sum("money");
//		$hasInterest = $loanbillModel->where(array("uid" => $userInfo["id"], "status" => array("in", "0,1")))->sum("interest");
//		$hasOverdue = $loanbillModel->where(array("uid" => $userInfo["id"], "status" => array("in", "0,1")))->sum("overdue");
//		//$this->assign("noneMoney", toMoney($hasMoney + $hasInterest + $hasOverdue));
//		$this->assign("noneMoney", toMoney($hasMoney + $hasOverdue));
        $productModel = D("product");
        $list = $productModel->order('id Desc')->select();
        $this->assign("list", $list);
        $this->display();
    }
}