<?php
class PageAction extends CommonAction
{
	public function protocol()
	{
		$tpl = htmlspecialchars_decode(htmlspecialchars_decode(C("agreementTpl")));
		$this->assign("tpl", $tpl);
		$this->display();
	}
	public function about()
	{
		$this->display();
	}
	public function problem()
	{
		$helpModel = D("Question");
		import("ORG.Util.Page");
		$count = $helpModel->count();
		$Page = new Page($count, C("PAGE_NUM_ONE"));
		$Page->setConfig("theme", "%linkPage%");
		$show = $Page->show();
		$list = $helpModel->order("sort Desc")->limit($Page->firstRow . "," . $Page->listRows)->select();
		$this->assign("list", $list);
		$this->assign("page", $show);
		$this->display();
	}
	public function setting()
	{
		$this->display();
	}
}