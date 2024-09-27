<?php
class HelpAction extends CommonAction
{
	public function index()
	{
		$helpModel = D("Question");
		$where = array();
		import("ORG.Util.Page");
		$count = $helpModel->where($where)->count();
		$Page = new Page($count, C("PAGE_NUM_ONE"));
		$Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
		$Page->setConfig("prev", "<");
		$Page->setConfig("next", ">");
		$Page->setConfig("theme", C("PAGE_STYLE"));
		$show = $Page->show();
		$list = $helpModel->where($where)->order("id Desc")->limit($Page->firstRow . "," . $Page->listRows)->select();
		$this->assign("list", $list);
		$this->assign("page", $show);
		$this->display();
	}
	public function add()
	{
		$title = trim(I("title"));
		$sort = I("sort", 0, "intval");
		$content = trim(I("content"));
		if (!$title) {
			$this->error("请输入问题标题");
		}
		$helpModel = D("Question");
		$data = array("title" => $title, "sort" => $sort, "content" => $content, "add_time" => time());
		$result = $helpModel->add($data);
		if (!$result) {
			$this->error("添加失败");
		}
		$this->success("添加成功");
	}
	public function view()
	{
		$id = I("id", 0, "intval");
		if (!$id) {
			$this->error("参数错误");
		}
		$helpModel = D("Question");
		$data = $helpModel->find($id);
		if (!$data) {
			$this->error("数据不存在");
		}
		$data["editurl"] = U("Help/edit", array("id" => $id));
		$this->success($data);
	}
	public function edit()
	{
		$id = I("get.id");
		if (!$id) {
			$this->error("参数有误");
		}
		$title = I("post.title");
		$sort = I("post.sort", 0, "intval");
		$content = I("post.content");
		if (!$title) {
			$this->error("请输入问题标题");
		}
		$helpModel = D("Question");
		$data = array("title" => $title, "sort" => $sort, "content" => $content);
		$result = $helpModel->where(array("id" => $id))->save($data);
		if (!$result) {
			$this->error("编辑失败");
		}
		$this->success("操作成功");
	}
	public function del()
	{
		$id = I("id", 0, "intval");
		if (!$id) {
			$this->error("参数错误");
		}
		$helpModel = D("Question");
		$result = $helpModel->where(array("id" => $id))->delete();
		if (!$result) {
			$this->error("数据删除失败");
		}
		$this->success("操作成功");
	}
}