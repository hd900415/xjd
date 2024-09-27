<?php
class OrderAction extends CommonAction
{
	public function index()
	{
		$where = array("pending" => 1,"pid"=>array('gt',0));
		if (I("s-oid")) {
			$where["oid"] = trim(I("s-oid"));
		}
		if (I("s-timeStart")) {
			$where["add_time"] = array("EGT", strtotime(I("s-timeStart")));
		}
		if (I("s-timeEnd")) {
			$where["add_time"] = array("ELT", strtotime(I("s-timeEnd")));
		}
		if (I("s-timeStart") && I("s-timeEnd")) {
			$where["add_time"] = array(array("EGT", strtotime(I("s-timeStart"))), array("ELT", strtotime(I("s-timeEnd"))));
		}
		$loanorderModel = D("Loanorder");
		import("ORG.Util.Page");
		$count = $loanorderModel->where($where)->count();
		//var_dump($loanorderModel->getLastSql());die;
		$Page = new Page($count, C("PAGE_NUM_ONE"));
		$Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
		$Page->setConfig("prev", "<");
		$Page->setConfig("next", ">");
		$Page->setConfig("theme", C("PAGE_STYLE"));
		$show = $Page->show();
		$list = $loanorderModel->where($where)->order("add_time Asc")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
		$i = 0;
		while ($i < count($list)) {
			$list[$i]["interest_money"] = toMoney($list[$i]["interest"] * $list[$i]["time"] * $list[$i]["money"]);
			$i = $i + 1;
		}
		$this->assign("list", $list);
		$this->assign("page", $show);
		$this->display();
	}

}