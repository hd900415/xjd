<?php

class FeedbackAction extends CommonAction
{
    public function index()
    {
        $admin = $this->isLogin();
        if (!ISADMIN) {
            $where = array("sid" => $admin['id']);
        } else {
            $where = array();
        }
        if (I("s-name")) {
            $where["uname"] = trim(I("s-name"));
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
        $feedbackModel = D("Feedback");
        import("ORG.Util.Page");
        $count = $feedbackModel->where($where)->count();
        echo $feedbackModel->getLastSql();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $feedbackModel->where($where)->order("add_time DESC")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
//        echo $loanorderModel->getLastSql();
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }
    public function remove()
    {
        $id = I("id");
        if (!$id) {
            $this->error("参数有误");
        }
        $feedbackModel = D("Feedback");

        $where=['id'=>['in',(array)$id]];
        $result = $feedbackModel->where($where)->delete();
//        echo $feedbackModel->getLastSql();
        if (!$result) {
            $this->error("操作失败");
        }
        $this->success("删除成功");
    }

}