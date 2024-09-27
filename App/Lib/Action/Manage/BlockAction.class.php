<?php
class BlockAction extends CommonAction
{
	public function index()
	{
		$blockModel = D("Block");
		$where = array();
		import("ORG.Util.Page");
		$count = $blockModel->where($where)->count();
		$Page = new Page($count, C("PAGE_NUM_ONE"));
		$Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
		$Page->setConfig("prev", "<");
		$Page->setConfig("next", ">");
		$Page->setConfig("theme", C("PAGE_STYLE"));
		$show = $Page->show();
		$list = $blockModel->where($where)->order("id Desc")->limit($Page->firstRow . "," . $Page->listRows)->select();
		$this->assign("list", $list);
		$this->assign("page", $show);
		$this->display();
		return null;
	}
	public function add()
	{
		if ($this->isPost()) {
			$name = trim(I("name"));
			$remarks = trim(I("remarks"));
			$type = trim(I("type"));
			$content = trim(I("content"));
			if (!$name) {
				$this->error("请输入调用名称");
			}
			$blockModel = D("Block");
			if ($blockModel->where(array("name" => $name))->find()) {
				$this->error("调用名称重复");
			}
			$content = htmlspecialchars($content);
			$result = $blockModel->add(array("name" => $name, "remarks" => $remarks, "type" => $type, "content" => $content));
			if (!$result) {
				$this->error("保存数据失败");
			}
			$this->success("操作成功");
		}
		$this->display();
	}
	public function edit()
	{
		$id = I("get.id");
		if (!$id) {
			$this->error("参数有误");
		}
		$blockModel = D("Block");
		if ($this->isPost()) {
			$name = trim(I("name"));
			$remarks = trim(I("remarks"));
			$type =trim( I("type"));
			$content = trim(I("content"));
			if (!$name) {
				$this->error("请输入调用名称");
			}
			$tmp = $blockModel->where(array("name" => $name))->find();
			if ($tmp && $tmp["id"] != $id) {
				$this->error("调用名称重复");
			}
			$content = htmlspecialchars($content);
			$result = $blockModel->where(array("id" => $id))->save(array("name" => $name, "remarks" => $remarks, "type" => $type, "content" => $content));
			if (!$result) {
				$this->error("保存数据失败");
			}
			$this->success("操作成功");
		}
		$data = $blockModel->where(array("id" => $id))->find();
		if (!$data) {
			$this->error("不存在该数据");
		}
		$data["content"] = htmlspecialchars_decode(htmlspecialchars_decode($data["content"]));
		$this->assign("data", $data);
		$this->display();
	}
	public function delBlock()
	{
		$id = I("id");
		if (!$id) {
			$this->error("参数有误");
		}
		$blockModel = D("Block");
		$result = $blockModel->where(array("id" => $id))->delete();
		if (!$result) {
			$this->error("数据操作失败");
		}
		$this->success("删除成功");
	}
}