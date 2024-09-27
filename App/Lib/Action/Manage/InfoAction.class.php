<?php

class InfoAction extends CommonAction
{
    public function _initialize()
    {
        parent::_initialize();
        $domains = C("APP_SUB_DOMAIN_RULES");
        $image_base_url = $domains[0] . '/Public/Upload/';
        $this->assign("image_base_url", $image_base_url);

    }

    public function index()
    {
        $infoModel = D("Info");
        $admin = $this->isLogin();
        if (!ISADMIN) {
            $where = array("sid" => $admin['id']);
        } else {
            $where = array();
        }

        if (I("s-username")) {
            //$where["telnum"] = array("LIKE", "%" . I("s-username") . "%");
            $w['telnum'] =trim(I("s-username"));
            $userModel = D('User');
            $info = $userModel->getInfo($w);
            if ($info) {
                $where['uid'] = $info['id'];
            }
        }
        if (I("status")) {
            $where["status"] = I("status");
        }
        import("ORG.Util.Page");
        $count = $infoModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $infoModel->where($where)->order("id Desc")->limit($Page->firstRow . "," . $Page->listRows)->relation(true)->select();
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }

    public function view()
    {
        $adminInfo = $this->isLogin();
        $uid = I("uid");
        if (!$uid) {
            $this->redirect("Info/index");
        }
        $infoModel = D("Info");
        $info = $infoModel->getAuthInfo($uid);
//        if (!$info) {
//            $this->redirect("Info/index");
//        }
        //var_dump(json_decode($info['taobao']));die;
        $this->assign("data", $info);
        /*if (!is_array(json_decode($info["mobile"], true))) {
            $result = curl("http://www.xauguo.cn/Api/Mobile/getReport/", array("callid" => $info["mobile"], "appkey" => C("ugappkey")), 1);
            if ($result) {
                $arr = json_decode($result, true);
                if ($arr["code"] == 0) {
                    $data = $arr["data"];
                    $info["mobile"] = json_encode($data);
                    $infoModel->setMobile($uid, $info["mobile"]);
                }
            }
        }
        if (!is_array(json_decode($info["taobao"], true))) {
            $result = curl("http://www.xauguo.cn/Api/Taobao/getData/", array("callid" => $info["taobao"], "appkey" => C("ugappkey")), 1);
            if ($result) {
                $arr = json_decode($result, true);
                if ($arr["code"] == 0) {
                    $data = $arr["data"];
                    $info["taobao"] = json_encode($data);
                    $infoModel->setTaobao($uid, $info["taobao"]);
                }
            }
        }*/
        $this->display();
    }

    public function adopt()
    {
        $uid = I("uid", 0, "intval");
        $quota = I("quota", 0, "floatval");
        if (!$uid) {
            $this->error("参数错误");
        }
        if (!isset($quota)) {
            $this->error("请输入用户审批额度");
        }
        $infoModel = D("Info");
        if (!$infoModel->setStatus($uid, 2)) {
            $this->error("资料状态保存失败");
        }
        $userModel = D("User");
        if (!$userModel->updateInfo($uid, array("quota" => $quota))) {
            $this->error("用户额度操作失败,请进入用户管理为当前用户重新设置额度");
        }
        $smsModel = D("Sms");
        $number = $userModel->getInfo("id", $uid, "telnum");
        $content = htmlspecialchars_decode(htmlspecialchars_decode(C("info_adopt")));
        $content = str_replace("<@sitename@>", C("siteName"), $content);
        $content = str_replace("《@sitename@》", C("siteName"), $content);
        $content = str_replace("<@quota@>", $quota, $content);
        $content = str_replace("《@quota@》", $quota, $content);
        $smsModel->sendSms($number, $content);
        $this->success("操作成功");
    }

    public function refuse()
    {
        $uid = I("uid", 0, "intval");
        if (!$uid) {
            $this->error("参数错误");
        }
        $infoModel = D("Info");
        if (!$infoModel->setStatus($uid, 0 - 1)) {
            $this->error("资料状态保存失败");
        }
        $userModel = D("User");
        if (!$userModel->updateInfo($uid, array("quota" => 0))) {
            $this->error("用户额度操作失败,请进入用户管理为当前用户重新设置额度");
        }
        $smsModel = D("Sms");
        $number = $userModel->getInfo("id", $uid, "telnum");
        $content = htmlspecialchars_decode(htmlspecialchars_decode(C("info_refuse")));
        $content = str_replace("<@sitename@>", C("siteName"), $content);
        $content = str_replace("《@sitename@》", C("siteName"), $content);
        $smsModel->sendSms($number, $content);
        $this->success("操作成功");
        return NULL;
    }

    public function resetInfo()
    {
        $id = I("id", 0, "intval");
        if (!$id) {
            $this->error("参数有误");
        }
        $action = I("action");
        if (!$id) {
            $this->error("请选择重置类型");
        }
        $infoModel = D("Info");
        $info = $infoModel->where(array("id" => $id))->find();
        if (!$info) {
            $this->error("资料索引不存在");
        }
        $uid = $info["uid"];
        unset($info['id']);
        unset($info['uid']);
        unset($info['status']);
        if ($action == "all") {
            foreach ($info as $key => $val) {
                $info[$key] = "";
            }
        } else {
            if (isset($info[$action])) {
                $info[$action] = "";
            }
        }
        $info["status"] = 0;
        $result = $infoModel->where(array("id" => $id))->save($info);
        if (!$result) {
            $this->error("用户资料重置失败");
        }
        $smsModel = D("Sms");
        $userModel = D("User");
        $number = $userModel->getInfo("id", $uid, "telnum");
        $content = htmlspecialchars_decode(htmlspecialchars_decode(C("info_reset")));
        $content = str_replace("<@sitename@>", C("siteName"), $content);
        $content = str_replace("《@sitename@》", C("siteName"), $content);
        $smsModel->sendSms($number, $content);
        $this->success("操作成功");
    }

    public function modify()
    {
        $adminInfo = $this->isLogin();
        if (!$adminInfo["status"]) {
            $this->error("您没有权限进行该操作");
        }
        $uid = trim(I("uid"));
//        print_r($uid);exit;
        if (!$uid) {
            $this->redirect("Info/index");
        }
        $infoModel = D("Info");
        $info = $infoModel->getAuthInfo($uid);

//        if (!$info) {
//            $this->redirect("Info/index");
//        }
        if ($this->isPost()) {
            $name = I("name");
            $sex = I("sex");
            $birthday = I("birthday");
            $idcard = I("idcard");
            $frontimg = I("frontimg");
            $backimg = I("backimg");
            $personimg = I("personimg");
            $telnum = I("telnum");
            $age = I("age");
//            联系人信息
            $zhishuName = I("zhishuName");
            $zhishuRelation = I("zhishuRelation");
            $zhishuPhone = I("zhishuPhone");
            $jinjiRelation = I("jinjiRelation");
            $jinjiName = I("jinjiName");
            $jinjiPhone = I("jinjiPhone");
//            银行卡信息
            $bankNum = I("bankNum");
            $bankName = I("bankName");
            $bankPhone = I("bankPhone");
            $ifsc = I("ifsc");
//           其他信息
            $marriage = I("marriage");
            $education = I("education");
            $industry = I("industry");
            $addess = I("addess");
            $addessMore = I("addessMore");


            $identity = array("name" => $name, "idcard" => $idcard, "frontimg" => $frontimg, "backimg" => $backimg, "personimg" => $personimg, 'age' => $age, "birthday" => $birthday, "sex" => $sex);
            $contacts = array("zhishuRelation" => $zhishuRelation, "zhishuName" => $zhishuName, "zhishuPhone" => $zhishuPhone, "jinjiRelation" => $jinjiRelation, "jinjiName" => $jinjiName, "jinjiPhone" => $jinjiPhone);
            $banks = array("bankName" => $bankName, "bankNum" => $bankNum, "bankPhone" => $bankPhone, 'ifsc' => $ifsc);
            $addess = array("marriage" => $marriage, "education" => $education, "industry" => $industry, "addess" => $addess, "addessMore" => $addessMore);
            $save_data = array(
                'identity' => json_encode($identity),
                'contacts' => json_encode($contacts),
                'addess' => json_encode($addess),
                'bank' => json_encode($banks),
                'mobile' => "" . $telnum,
                'status' => 2,
                'sid' => $adminInfo['id'],
            );
            $flag = $infoModel->where(array("uid" => $uid))->save($save_data);
            $userModel = D("User");
            $userModel->where(array("id" => $uid))->save(['telnum' => $telnum]);
            $this->success("保存成功");
        }

        $domains = C("APP_SUB_DOMAIN_RULES");

        $image_base_ur = $domains[0] . '/Public/Upload/';

        $this->assign("data", $info);
        $this->assign("uid", $uid);
        $this->assign("image_base_url", $image_base_ur);
        $this->display();
    }
}