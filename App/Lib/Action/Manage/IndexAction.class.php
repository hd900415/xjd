<?php

class IndexAction extends CommonAction
{
    public function index()
    {
        $adminInfo = $this->isLogin();
        if (!$adminInfo) {
            $this->redirect("Index/login");
            exit(0);
        }
        $this->assign("adminInfo", $adminInfo);
        $this->display();
    }

    public function main()
    {
        if (!$this->isLogin()) {
            $this->redirect("Index/login");
            exit(0);
        }

        $userModel = D("User");
        $loanorderModel = D("Loanorder");
        $loanbillModel = D("Loanbill");
        $redisModel = RedisModel::getInstance();

        $redisModel->select(5);
        if (!$redisModel->exists(C('backend_admin_index'))) {
            $dayRegNum = $userModel->where(array("reg_time" => array("EGT", strtotime(date("Y-m-d")))))->count();
            $dayLoanNum = $loanorderModel->where(array("add_time" => array("EGT", strtotime(date("Y-m-d")))))->count();
            $dayAgreeOrderNum = $loanbillModel->group("oid")->where(array("add_time" => array("EGT", strtotime(date("Y-m-d")))))->count();
            $dayAgreeOrderMoney = $loanbillModel->where(array("add_time" => array("EGT", strtotime(date("Y-m-d")))))->sum("money");
            $agreeOrderNum = $loanbillModel->count();
            $agreeOrderMoney = $loanbillModel->sum("money");
            $waitOrderNum = $loanbillModel->where(array("status" => array("IN", "0,1")))->count();
            $waitOrderMoney = $loanbillModel->where(array("status" => array("IN", "0,1")))->sum("money");
            $overdueOrderNum = $loanorderModel->where(array("status" => 1))->count();
            $overdueOrderMoney = $loanbillModel->where(array("status" => 1))->sum("money");
            $redisModel->hMSet(C('backend_admin_index'), array(
                "dayRegNum" => $dayRegNum,
                "dayLoanNum" => $dayLoanNum,
                "dayAgreeOrderNum" => $dayAgreeOrderNum,
                "dayAgreeOrderMoney" => $dayAgreeOrderMoney,
                "agreeOrderNum" => $agreeOrderNum,
                "agreeOrderMoney" => $agreeOrderMoney,
                "waitOrderNum" => $waitOrderNum,
                "waitOrderMoney" => $waitOrderMoney,
                "overdueOrderNum" => $overdueOrderNum,
                "overdueOrderMoney" => $overdueOrderMoney,
            ));
            $redisModel->expire(C('backend_admin_index'), 21600);
        } else {
            $adminInfo = $redisModel->hGetAll(C('backend_admin_index'));
            $dayRegNum = $adminInfo["dayRegNum"];
            $dayLoanNum = $adminInfo["dayLoanNum"];
            $dayAgreeOrderNum = $adminInfo["dayAgreeOrderNum"];
            $dayAgreeOrderMoney = $adminInfo["dayAgreeOrderMoney"];
            $agreeOrderNum = $adminInfo["agreeOrderNum"];
            $agreeOrderMoney = $adminInfo["agreeOrderMoney"];
            $waitOrderNum = $adminInfo["waitOrderNum"];
            $waitOrderMoney = $adminInfo["waitOrderMoney"];
            $overdueOrderNum = $adminInfo["overdueOrderNum"];
            $overdueOrderMoney = $adminInfo["overdueOrderMoney"];
        }

        $this->assign("dayRegNum", $dayRegNum);
        $this->assign("dayLoanNum", $dayLoanNum);
        $this->assign("dayAgreeOrderNum", $dayAgreeOrderNum);
        $this->assign("dayAgreeOrderMoney", toMoney($dayAgreeOrderMoney));
        $this->assign("agreeOrderNum", $agreeOrderNum);
        $this->assign("agreeOrderMoney", toMoney($agreeOrderMoney / 10000));
        $this->assign("waitOrderNum", $waitOrderNum);
        $this->assign("waitOrderMoney", toMoney($waitOrderMoney / 10000));
        $this->assign("overdueOrderNum", $overdueOrderNum);
        $this->assign("overdueOrderMoney", toMoney($overdueOrderMoney));
        $this->display();
    }

    public function apidata()
    {
        if (!$this->isLogin()) {
            $this->error("登录状态有误,请刷新页面");
        }
        $userModel = D("User");
        $loanorderModel = D("Loanorder");
        $loanbillModel = D("Loanbill");
        $data = array();
        $cityRegNum = $userModel->field(array("reg_city" => "name", "count(reg_city)" => "value"))->group("reg_city")->select();
        $cityLoanMoney = array();
        $cityRepayMoney = array();
        foreach ($cityRegNum as $val) {
            $Loanvalue = 0;
            $Repayvalue = 0;
            $tmpArr = $userModel->where(array("reg_city" => $val["name"]))->select();
            $i = 0;
            while ($i < count($tmpArr)) {
                $uid = $tmpArr[$i]["id"];
                $tmpvalue = $loanorderModel->where(array("uid" => $uid, "pending" => 1))->sum("money");
                $Loanvalue = toMoney($tmpvalue) + $Loanvalue;
                $tmpvalue = $loanbillModel->where(array("uid" => $uid, "status" => array("IN", "2,3")))->sum("money");
                $Repayvalue = toMoney($tmpvalue) + $Repayvalue;
                $tmpvalue = $loanbillModel->where(array("uid" => $uid, "status" => array("IN", "2,3")))->sum("interest");
                $Repayvalue = toMoney($tmpvalue) + $Repayvalue;
                $tmpvalue = $loanbillModel->where(array("uid" => $uid, "status" => array("IN", "2,3")))->sum("overdue");
                $Repayvalue = toMoney($tmpvalue) + $Repayvalue;
                $i = $i + 1;
            }
            $cityLoanMoney[] = array("name" => $val["name"], "value" => $Loanvalue);
            $cityRepayMoney[] = array("name" => $val["name"], "value" => $Repayvalue);
        }
        $data["cityRegNum"] = empty($cityRegNum) ? json_encode(array()) : json_encode($cityRegNum);
        $data["cityLoanMoney"] = empty($cityLoanMoney) ? json_encode(array()) : json_encode($cityLoanMoney);
        $data["cityRepayMoney"] = empty($cityRepayMoney) ? json_encode(array()) : json_encode($cityRepayMoney);
        $this->success($data);
        return NULL;
    }

    public function captcha()
    {
        import("ORG.Util.Image");
        Image::buildImageVerify();
    }


    public function login()
    {
        if ($this->isPost()) {
            $name = trim(I("username"));
            $pass = trim(I("password"));
            $captcha = trim(I("captcha"));
            if (!$name) {
                $this->error("请输入管理用户名");
            }
            if (!$pass) {
                $this->error("请输入管理登录密码");
            }
            if (!$captcha) {
                $this->error("请输入验证码");
            }

            if ($_SESSION["verify"] != md5($captcha)) {
                $this->error("验证码输入有误");
            }
            $adminModel = D("Admin");
            $info = $adminModel->where(array("username" => $name, "password" => $adminModel->str2pass($pass)))->find();
            if (!$info) {
                $this->error("管理用户名或密码有误");
            }
            if (!$info["status"] && !$info["type"]) {
                $this->error("您的账户已被禁用,请联系站点创始人");
            }
            ini_set('session.gc_maxlifetime', 1800);

            session_cache_expire(1800);
            session_set_cookie_params(1800, '/', $_SERVER['HTTP_HOST']);
            session_start();
            $this->setLogin($info);
            //将session key保存到redis
            $redisModel = RedisModel::getInstance();
            $redisModel->select(1);
            $redisModel->lPush(C('login_admin'),session_id());
            $adminModel->where(array("id" => $info["id"]))->save(array("last_ip" => get_client_ip(), "last_time" => time()));

            $this->success("登录成功");
        }
        $this->display();
    }

    public function test()
    {
//        session_set_cookie_params(1800, '/');
        echo "<pre>";
        print_r(session_get_cookie_params());
        echo ini_get('session.gc_maxlifetime');
    }

    public function test1()
    {
        session_set_cookie_params(1800, '/');
        session_cache_expire(1800);
        session_set_cookie_params(1800, '/', $_SERVER['HTTP_HOST']);
    }

    public function logout()
    {
        $this->setLogin(NULL);
        $this->redirect("Index/login");
    }

    public function changepass()
    {
        $adminInfo = $this->isLogin();
        if (!$adminInfo) {
            $this->error("您还没有登录,请先登录", U("Index/login"));
        }
        if ($this->isPost()) {
            $oldpass = trim(I("oldpass"));
            $password = trim(I("password"));
            $repass = trim(I("repass"));
            if (!$oldpass) {
                $this->error("请输入原密码");
            }
            if (!$password) {
                $this->error("请输入新密码");
            }
            if (strlen($password) < 6) {
                $this->error("密码长度必须大于 6 位");
            }
            if ($password != $repass) {
                $this->error("两次密码输入不一致");
            }
            $adminModel = D("Admin");
            $info = $adminModel->where(array("username" => $adminInfo["username"]))->find();
            if (!$info) {
                $this->error("账户异常");
            }
            if ($info["password"] != $adminModel->str2pass($oldpass)) {
                $this->error("原密码输入有误");
            }
            $result = $adminModel->where(array("username" => $adminInfo["username"]))->save(array("password" => $adminModel->str2pass($password)));
            if (!$result) {
                $this->error("新密码保存失败");
            }
            session("manage", NULL);
            $sessionPath = session_save_path();
            if(!$sessionPath) $sessionPath='/tmp';
            $redisModel = RedisModel::getInstance();
            $redisModel->select(1);
            $keys=$redisModel->lRange(C('login_admin'),0,-1);
            if(!empty($keys)){
                foreach ($keys as $key){
                    $this->deleteSessionFiles($sessionPath,$key);
                }
            }
            $this->success("修改成功");
        }
        $this->display();
    }
    public function test3(){
        echo "hello";
        $sessionPath = session_save_path();
        echo "session Path:".$sessionPath;
        if(!$sessionPath) $sessionPath='/tmp';
        $this->deleteSessionFiles($sessionPath,"admin_");

    }
    public function test2(){
        $sessionPath = session_save_path();
        if(!$sessionPath) $sessionPath='/tmp';
        $redisModel = RedisModel::getInstance();
        $redisModel->select(1);
        $keys=$redisModel->lRange('login_admin',0,-1);
        if(!empty($keys)){
            foreach ($keys as $key){
                echo $key;
                $this->deleteSessionFiles($sessionPath,$key);
            }
        }
    }
    public function deleteSessionFiles($directory, $prefix)
    {
        // 打开目录
        $handle = opendir($directory);

        // 遍历目录中的文件
        while (false !== ($file = readdir($handle))) {
            // 排除当前目录和上级目录
            if ($file != "." && $file != "..") {
                // 检查文件名是否以指定前缀开头
                if (strpos($file, $prefix)!== false) {
                    // 构造文件路径
                    $filePath = $directory . "/" . $file;
                    // 删除文件
                    @unlink($filePath);
                }
            }
        }

        // 关闭目录句柄
        closedir($handle);
    }
}