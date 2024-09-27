<?php

class AdminAction extends CommonAction
{
    public function view()
    {
        $adminInfo = $this->isLogin();
        if (!$adminInfo["status"]) {
            $this->error("您没有权限进行该操作");
        }
        $sid = trim(I('id'));
        $adminModel = D("Admin");
        $where = array();
        $shorder = M('loanorder')->where(array('sid' => $sid))->count();
        $shinfo = M('info')->where(array('sid' => $sid))->count();
        $dkmoney = M('loanorder')->where(array('sid' => $sid, "pending" => 1))->sum('money');
        $admin = M('admin')->where(array('id' => $sid))->find();
        //var_dump($dkmoney);
        $this->assign("shorder", $shorder);
        $this->assign("shinfo", $shinfo);
        $this->assign("dkmoney", $dkmoney);
        $this->assign("admin", $admin);
        //$this->assign("page", $show);
        $this->display();
    }

    public function index()
    {
        $adminInfo = $this->isLogin();
        if (!$adminInfo["status"]) {
            $this->error("您没有权限进行该操作");
        }
        $adminModel = D("Admin");
        $where = array();
        import("ORG.Util.Page");
        $count = $adminModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $adminModel->where($where)->order("id Desc")->limit($Page->firstRow . "," . $Page->listRows)->select();
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }

    public function add()
    {
        $adminInfo = $this->isLogin();
        if (!$adminInfo["status"]) {
            $this->error("您没有权限进行该操作");
        }
        if ($this->isPost()) {
            $name = trim(I("username"));
            $pass = trim(I("password"));
            $repass =trim(I("repassword"));
            $type = trim(I("type"));
            $status = trim(I("status"));
            if (!$name) {
                $this->error("请输入管理用户名");
            }
            if (!$pass) {
                $this->error("请输入管理登录密码");
            }
            if (strlen($pass) < 6) {
                $this->error("管理登录密码长度不能小于 6 位");
            }
            if ($repass != $pass) {
                $this->error("两次密码输入不一致");
            }
            $adminModel = D("Admin");
            if ($adminModel->where(array("username" => $name))->find()) {
                $this->error("管理用户名重复");
            }
            $result = $adminModel->add(array("username" => $name, "password" => $adminModel->str2pass($pass), "type" => $type, "status" => $status, "last_ip" => "", "last_time" => 0));
            if (!$result) {
                $this->error("保存数据失败");
            }
            $this->success("操作成功");
        }
        $this->display();
    }

    public function edit()
    {
        $adminInfo = $this->isLogin();
        if (!$adminInfo["status"]) {
            $this->error("您没有权限进行该操作");
        }
        $id = trim(I("get.id"));
        if (!$id) {
            $this->error("参数有误");
        }
        $adminModel = D("Admin");
        if ($this->isPost()) {
            $name =trim( I("username"));
            $pass = trim(I("password"));
            $repass = trim(I("repassword"));
            $status = trim(I("status"));
            if (!$name) {
                $this->error("请输入管理用户名");
            }
            if ($pass) {
                if (strlen($pass) < 6) {
                    $this->error("管理登录密码长度不能小于 6 位");
                }
                if ($repass != $pass) {
                    $this->error("两次密码输入不一致");
                }
            }
            $adminModel = D("Admin");
            $tmp = $adminModel->where(array("username" => $name))->find();
            if ($tmp && $tmp["id"] != $id) {
                $this->error("管理用户名重复");
            }
            $data = array("username" => $name, "type" => 0, "status" => $status, "last_ip" => "", "last_time" => 0);
            if ($pass) {
                $data["password"] = $adminModel->str2pass($pass);
            }
            $result = $adminModel->where(array("id" => $id))->save($data);
            if (!$result) {
                $this->error("保存数据失败");
            }
            $this->success("操作成功");
        }
        $data = $adminModel->where(array("id" => $id))->find();
        if (!$data) {
            $this->error("不存在该数据");
        }
        $this->assign("data", $data);
        $this->display();
    }

    public function del()
    {
        $adminInfo = $this->isLogin();
        if (!$adminInfo["status"]) {
            $this->error("您没有权限进行该操作");
        }
        $id = trim(I("id"));
        if (!$id) {
            $this->error("参数有误");
        }
        $adminModel = D("Admin");
        $info = $adminModel->where(array("id" => $id))->find();
        if (!$info) {
            $this->error("管理员不存在");
        }
        if ($info["type"] == 1) {
            $this->error("不可操作网站创始人");
        }
        $result = $adminModel->where(array("id" => $id))->delete();
        if (!$result) {
            $this->error("数据操作失败");
        }
        $this->success("删除成功");
    }
}