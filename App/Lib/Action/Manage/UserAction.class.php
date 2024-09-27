<?php

class UserAction extends CommonAction
{
    public function index()
    {
        $userModel = D("User");
        $where = array();
        if (I("id")) {
            $where["id"] = I("id");
        }
        if (I("username")) {
            $where["telnum"] = array("LIKE", "%" . trim(I("username")) . "%");
        }
        if (I("telnum")) {
            $where["telnum"] = array("LIKE", "%" . trim(I("username")) . "%");
        }
        if (I("timeStart")) {
            $where["reg_time"] = array("EGT", strtotime(urldecode(I("timeStart"))));
        }
        if (I("timeEnd")) {
            $where["reg_time"] = array("ELT", strtotime(urldecode(I("timeEnd"))));
        }
        if (I("timeStart") && I("timeEnd")) {
            $where["reg_time"] = array(array("EGT", strtotime(urldecode(I("timeStart")))), array("ELT", strtotime(urldecode(I("timeEnd")))));
        }
        //var_dump($where);die;
        import("ORG.Util.Page");
        $count = $userModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();

        $login_count=$userModel->where(array_merge($where,["login_count"=>['egt',1]]))->count();
        $reply_count=$userModel->where(array_merge($where,["reply_count"=>['egt',1]]))->count(); //点过还款按钮的人数：
        $get_order=$userModel->where(array_merge($where,["get_order"=>['egt',1]]))->count(); //拉单过单的人数：：
        $pay_count=$userModel->where(array_merge($where,["pay_count"=>['egt',1]]))->count(); //支付成功数：

        echo $userModel->getLastSql();
        $list = $userModel->where($where)->order("id Desc")->limit($Page->firstRow . "," . $Page->listRows)->select();

        $loanorderModel = D("Loanorder");
        $loanbillModel = D("Loanbill");
        $i = 0;
        while ($i < count($list)) {
            $uid = $list[$i]["id"];
            if ($list[$i]['vipid'] == 1) {
                $list[$i]['viptitle'] = "银牛卡";
            } else if ($list[$i]['vipid'] == 2) {
                $list[$i]['viptitle'] = "金牛卡";
            } else {
                $list[$i]['viptitle'] = "普通卡";
            }
            $list[$i]["succLoan"] = 0;
            $list[$i]["errLoan"] = 0;
            $list[$i]["overdueNum"] = 0;
            $list[$i]["repayLoan"] = 0;
            $list[$i]["unpaid"] = 0;
            $list[$i]["loanMoney"] = 0;
            $list[$i]["overdueMoney"] = 0;
            $list[$i]["notrepayMoney"] = 0;
            $loanOrders = $loanorderModel->where(array("uid" => $uid))->select();
            foreach ($loanOrders as $loanOrder) {
                if ($loanOrder['pending'] == 1) {
                    $list[$i]["succLoan"]++;
                    $list[$i]["repayLoan"] += $loanOrder['money'];
                    $list[$i]["loanMoney"] += $loanOrder['money'];
                    if ($loanOrder['status'] != 2) {
                        $list[$i]["notrepayMoney"] += $loanOrder['money'] - $loanOrder['repaid_money'];
                    }
                }
                if ($loanOrder['pending'] == 2) {
                    $list[$i]["errLoan"]++;
                }
                if ($loanOrder['status'] == 1) {
                    $list[$i]["repayLoan"]++;
                }
                if ($loanOrder['status'] == 2 && $loanOrder['pending'] == 1) {
                    $list[$i]["unpaid"]++;
                }
                $repaymentTime = $loanorderModel->getOrderRepayTime($loanOrder);
                if ($repaymentTime < time() && $loanOrder['status'] != 2) {
                    $list[$i]["overdueNum"]++;
                    $list[$i]["overdueMoney"] += $loanOrder['money'];
                }

            }
//            $loanBills = $loanbillModel->where(array("uid" => $uid))->select();
//            foreach ($loanBills as $loanBill) {
//                if (in_array(intval($loanBill['status']), array(1, 3))) {
//                    $list[$i]["overdueNum"]++;
//                }
//                if (in_array(intval($loanOrder['status']), array(1))) {
//                    $list[$i]["overdueMoney"] += $loanOrder['money'] + $loanOrder['interest'];
//                }
//                if (in_array(intval($loanOrder['status']), array(1))) {
//                    $list[$i]["notrepayMoney"] += $loanOrder['money'] + $loanOrder['overdue'];
//                }
//            }
            $i = $i + 1;
        }
        $login2time=$userModel->where(array_merge($where, ["login_count"=>array("egt",2)]))->count();//登录2次或以上人数
        $reply2time=$userModel->where(array_merge($where, ["reply_count"=>array("egt",2)]))->count();//点还款按钮2次或以上人数：
        $get2order=$userModel->where(array_merge($where, ["get_order"=>array("egt",2)]))->count();//拉单2次或以上人数：：

        $this->assign("login_count", $login_count);
        $this->assign("reply_count", $reply_count);
        $this->assign("get_order", $get_order);
        $this->assign("pay_count", $pay_count);
        $this->assign("login2time", $login2time);
        $this->assign("reply2time", $reply2time);
        $this->assign("get2order", $get2order);
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }

    public function getRedis()
    {

        $redis = new \Redis();

    }

    public function resetPass()
    {
        $id = I("id");
        if (!$id) {
            $this->error("参数有误");
        }
        $userModel = D("User");
        $newPass = rand(0, 99) . rand(0, 99) . rand(0, 99) . rand(0, 99);
        $r = $userModel->where(array("id" => $id))->save(array("password" => $userModel->str2pass($newPass)));
        if (!$r) {
            $this->error("重置失败");
        }
        $this->success($newPass);
    }

    public function resetTel()
    {
        $id = I("id");
        if (!$id) {
            $this->error("参数有误");
        }
        $tel = I("tel");
        if (!$tel) {
            $this->error("请输入用户新手机号码");
        }
//        if (!isMobile($tel)) {
//            $this->error("手机号码不符合规范");
//        }
        $userModel = D("User");
        $r = $userModel->where(array("id" => $id))->save(array("telnum" => $tel));
        if (!$r) {
            $this->error("修改失败");
        }
        $this->success("修改成功");
    }

    public function resetBank()
    {
        $id = I("id");
        if (!$id) {
            $this->error("参数有误");
        }
        $bank = I("bank");
        if (!$bank) {
            $this->error("请输入用户新卡号");
        }
        $infoModel = D("Info");
        $r = $infoModel->where(array("uid" => $id))->find();
        if (empty($r['bank'])) {
            $this->error("用户已有卡号为空，不可以修改");
        }
        $bankinfo = json_decode($r['bank'], true);
        $bankinfo['bankNum'] = $bank;
        //var_dump($bankinfo);die;
        $r1 = $infoModel->where(array("uid" => $id))->save(array("bank" => json_encode($bankinfo)));
        //var_dump($bankinfo);die;
        if (!$r1) {
            $this->error("修改失败");
        }
        $this->success("修改成功");
    }

    public function resetQuota()
    {
        $id = I("id");
        if (!$id) {
            $this->error("参数有误");
        }
        $quota = I("quota", 0, "intval");
        if (!isset($quota)) {
            $this->error("请输入用户新额度");
        }
        $userModel = D("User");
        $r = $userModel->where(array("id" => $id))->save(array("quota" => $quota));
        if (!$r) {
            $this->error("修改失败");
        }
        $this->success("修改成功");
    }

    public function resetStatus()
    {
        $id = I("id");
        if (!$id) {
            $this->error("参数有误");
        }
        $status = I("status", 1, "intval");
        $userModel = D("User");
        $r = $userModel->where(array("id" => $id))->save(array("status" => $status));
        if (!$r) {
            $this->error("修改失败");
        }
        $this->success("修改成功");
        return NULL;
    }

    public function del()
    {
        $id = I("id");
        if (!$id) {
            $this->error("参数有误");
        }
        $loanbillModel = D("Loanbill");
        $t = $loanbillModel->where(array("uid" => $id, "status" => array("IN", "0,1")))->count();
        if ($t) {
            $this->error("该用户有未还清账单,无法删除");
        }
        $loanorderModel = D("Loanorder");
        $t = $loanorderModel->where(array("uid" => $id, "pending" => array("NOT IN", "0,2")))->count();
        // echo $loanorderModel->getLastSql();
        if ($t) {
            $this->error("该用户有未处理借款订单,无法删除");
        }
        $userModel = D("User");
        $info = $userModel->where(array("id" => $id))->find();
        if (!$info) {
            $this->error("该用户不存在");
        }
        $loanbillModel->where(array("uid" => $id))->delete();
//        if (!$r) {
//            $this->error("账单删除失败");
//        }
        $loanorderModel->where(array("uid" => $id))->delete();
//        if (!$r) {
//            $this->error("订单删除失败");
//        }
        $r = M("Info")->where(array("uid" => $id))->delete();
        if (!$r) {
            $this->error("资料删除失败");
        }
        $r = $userModel->where(array("id" => $id))->delete();
        if (!$r) {
            $this->error("用户删除失败");
        }
        $this->success("删除成功");
    }

    public function batchDelete()
    {
        if ($this->ispost()) {
            $ids = I('ids');
            $type = I("type");
            if (empty($ids) || count($ids) == 0 || empty($type)) {
                $this->error('要删除的数据必须填写');
            }
            $trans = M();
            $trans->startTrans();
            try {
                $userModel = D("User");
                $loanbillModel = D("Loanbill");
                $loanOrderModel = D('Loanorder');
                if ($type == 2) {
                    $where = array();
                    if (I("id")) {
                        $where["id"] = I("id");
                    }
                    if (I("username")) {
                        $where["telnum"] = array("LIKE", "%" . I("username") . "%");
                    }
                    if (I("telnum")) {
                        $where["telnum"] = array("LIKE", "%" . I("username") . "%");
                    }
                    if (I("timeStart")) {
                        $where["reg_time"] = array("EGT", strtotime(urldecode(I("timeStart"))));
                    }
                    if (I("s-timeEnd")) {
                        $where["reg_time"] = array("ELT", strtotime(urldecode(I("timeEnd"))));
                    }
                    if (I("timeStart") && I("timeEnd")) {
                        $where["reg_time"] = array(array("EGT", strtotime(urldecode(I("timeStart")))), array("ELT", strtotime(urldecode(I("timeEnd")))));
                    }
                    if (empty($where)) {
                        throw new Exception("请先搜索再执行删除");
                    }
                    $users = $userModel->where($where)->select();
//                    echo $userModel->getLastSql();
//                    echo "<pre>";
//                    print_r($users);
//                    exit;
                    $uids = array();
                    foreach ($users as $user) {
                        array_push($uids, $user['id']);
                    }
                    $idIn = implode(',', (array)$uids);
                    if (empty($uids) || count($uids) == 0) {
                        throw new Exception('没有要删除的数据');
                    }
                } else {
                    $idIn = implode(',', (array)$ids);
                }
                $status = $userModel->where(array('id' => array('in', $idIn)))->delete();
                if (!$status) {
                    throw new Exception('Unable to delete loan order,' . $userModel->getLastSql());
                }
                $billTotal = $loanbillModel->where(array('uid' => array('in', $idIn)))->count();
                if ($billTotal > 0) {
                    $billStatus = $loanbillModel->where(array('uid' => array('in', $idIn)))->delete();
                    if (!$billStatus) {
                        throw new Exception('Unable to delete loan bill');
                    }
                }

                $billTotal = $loanOrderModel->where(array('uid' => array('in', $idIn)))->count();
                if ($billTotal > 0) {
                    $status = $loanOrderModel->where(array('uid' => array('in', $idIn)))->delete();
                    if (!$status) {
                        throw new Exception('Unable to delete loan order,' . $loanOrderModel->getLastSql());
                    }
                }
                $trans->commit();
                $this->success("操作完成");
            } catch (Exception $e) {
                $trans->rollback();
                $this->error('操作失败:' . $e->getMessage());
            }

        } else {
            $this->error('请求方法不支持！');
        }
    }

    public function importUserFromExcel1()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        ini_set('max_execution_time', 864000);
        ini_set('memory_limit', '2048M');
        set_time_limit(0);
        ignore_user_abort(true);
        // vendor("PhpSpreadsheet.IOFactory");
        $path = I("path");
        $lastRepayTime = I("last_repaytime");
        $remark = I("remark");
        if (!$path) {
            $this->error("参数有误");
        }
        $basePATH = APP_PATH . "../Public/Upload/";
        $filePath = $basePATH . $path;
        if (!file_exists($filePath)) {
            $this->error("文件不存在");
        }

        if (!is_readable($filePath)) {
            $this->error("文件不可读");
            return;
        }
        try {
            require_once THINK_PATH . '/vendor/autoload.php';
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);
            print_r($data);
        } catch (Exception $e) {
            print_r($e);
        }


    }

    public function importUserFromExcel()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL | E_STRICT | E_NOTICE);
        ini_set('max_execution_time', 864000);
        ini_set('memory_limit', '3096M');
        set_time_limit(0);
//        ignore_user_abort(true);
        vendor("PHPExcel.Classes.PHPExcel");
        $path = I("path");
        $lastRepayTime = I("last_repaytime");
       
        $dataType = I("data_type");
        $remark = I("remark");
        if (!$path) {
            $this->error("参数有误");
        }
        $basePATH = APP_PATH . "../Public/Upload/";
        $filePath = $basePATH . $path;
        if (!file_exists($filePath)) {
            $this->error("文件不存在");
        }

        if (!is_readable($filePath)) {
            $this->error("文件不可读");
            return;
        }
        try {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array('memoryCacheSize' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $inputFileType = PHPExcel_IOFactory::identify($filePath);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            //        $objReader->setLoadSheetsOnly(1);
            $phpExcel = $objReader->load($filePath);
        } catch (Exception $e) {
            print_r($e);
        }

        $sheet = $phpExcel->getSheet(0);
        $excel_array = [];
//        $excel_array = $sheet->toArray();

        foreach ($sheet->getRowIterator() as $row)//逐行处理
        {
            $rowIndex = $row->getRowIndex();
            $excel_array[$rowIndex-1] = [];
            foreach ($row->getCellIterator() as $k => $cell)//逐列处理
            {
                $cellValue = $cell->getValue();//获取单元格数据
                array_push($excel_array[$rowIndex-1], $cellValue);
                if (!$cellValue) break;
            }
        }
//        print_r($excel_array);exit;
        if (empty($excel_array)) {
            $this->error("导入失败");
            return;
        }
        $redisModel = RedisModel::getInstance();
        $redisModel->select(10);
        $redisModel->hMSet(C('default_loan_setting'), array(
            'default_loan_time' => C('DEFAULT_LOAN_TIME') ? C('DEFAULT_LOAN_TIME') : 7,
            'default_loan_type' => C('LOAN_TYPE'),
            'default_loan_overdue_rate' => C('Overdue'),
            'default_loan_interest' => getInterest(),
            'default_loan_reply_rate' => getRepayRate(),
            'default_loan_quota' => C('DEFAULT_QUOTA'),
            'import_data_type'=>$dataType
        ));
      
        $data = $this->_filterExecel($excel_array, $lastRepayTime, $remark);
        $this->success('请求已执行，后台正在完成导入操作，请耐心等待导入完成');

    }

    private function processExcelData($rows)
    {
        $headers = $rows[0];
        $headers = array_filter($headers);
        $data = [];
        foreach ($rows as $key => $row) {
            $tmpRow = [];
            if ($key != 0) {
                foreach ($headers as $k => $name) {
                    $tmpRow[$name] = $row[$k];
                }
                array_push($data, $tmpRow);
            }
        }
        return $data;
    }

    private function _filterExecel($rows, $lastRepayTime, $remark)
    {
        $headers = $rows[0];
        $headers = array_filter($headers);
        $redisModel = RedisModel::getInstance();
        $redisModel->select(10);
        $dataSource = [];
        foreach ($rows as $key => $row) {
            $tmpRow = [];
            $row = array_filter($row);
            if ($key != 0 && !empty($row)) {
                foreach ($headers as $k => $name) {
                    $tmpRow[$name] = $row[$k];
                }
                $tmpRow['还款时间'] = $lastRepayTime;
                $tmpRow['remark'] = $remark;
                $redisModel->lPush(C('key_import_user_list'), json_encode($tmpRow, JSON_UNESCAPED_UNICODE));
                array_push($dataSource, $tmpRow);
            }
            if (empty($row)) {
                break;
            }
        }
        return $dataSource;
    }

    private function _importUserInfos($rows)
    {
        set_time_limit(0);
        $add_total = 0;
        $userModel = D("User");
        $infoModel = D("Info");
        $loanorderModel = D("Loanorder");

        foreach ($rows as $key => $row) {
            $username = isset($row['用户名/电话']) ? $row['用户名/电话'] : null;
            $password = isset($row['密码']) ? $row['密码'] : '123456';
            $email = isset($row['邮箱']) ? $row['邮箱'] : '';
            $idcard = isset($row['身份证号']) ? $row['身份证号'] : '';
            $firstname = isset($row['firstname']) ? $row['firstname'] : '';
            $lastname = isset($row['lastname']) ? $row['lastname'] : '';
            $quota = isset($row['总额度']) ? $row['总额度'] : C('DEFAULT_QUOTA');
            $credit = isset($row['征信值']) ? $row['征信值'] : '';
            $bankCardNo = isset($row['银行卡号']) ? $row['银行卡号'] : '';
            $bankName = isset($row['开户行']) ? $row['开户行'] : '';
            $bankPhone = isset($row['银行预留手机号']) ? $row['银行预留手机号'] : '';
            $marriage = isset($row['婚姻状态']) ? $row['婚姻状态'] : '';
            $education = isset($row['教育状况']) ? $row['教育状况'] : '';
            $industry = isset($row['工作行业']) ? $row['工作行业'] : '';
            $addess = isset($row['居住城市']) ? $row['居住城市'] : '';
            $addessMore = isset($row['详细地址']) ? $row['详细地址'] : '';
            $zhishuRelation = isset($row['联系人关系']) ? $row['联系人关系'] : '';
            $zhishuName = isset($row['联系人姓名']) ? $row['联系人姓名'] : '';
            $zhishuPhone = isset($row['联系人手机']) ? $row['联系人手机'] : '';

            $jinjiRelation = isset($row['联系人2关系']) ? $row['联系人2关系'] : '';
            $jinjiName = isset($row['联系人2姓名']) ? $row['联系人2姓名'] : '';
            $jinjiPhone = isset($row['联系人2手机']) ? $row['联系人2手机'] : '';
            $isAudit = isset($row['需要审核']) ? $row['需要审核'] : '否';
            $loanTime = isset($row['借款期限']) ? $row['借款期限'] : 7;
            if ($loanTime <= 0) {
                $loanTime = 7;
            }
            $currentMobile = '';
            if ($username) {
                $currentMobile = $username;
            } else if ($bankPhone && isMobile($bankPhone)) {
                $currentMobile = $bankPhone;
            }
            if ($currentMobile != null) {
                $password = $userModel->str2pass($password);
                $result = $userModel->where(array("telnum" => $currentMobile))->find();
                if (!$result) {
                    $trans = M();
                    $trans->startTrans();
                    try {
                        $uid = $userModel->addUserInfo($currentMobile, $password, $email, $firstname, $lastname, $quota, $credit);
                        $cardInfo = array("bankName" => $bankName, "bankNum" => $bankCardNo, "bankPhone" => $bankPhone);
                        $identity = array("name" => $firstname . $lastname, "idcard" => $idcard, "frontimg" => '', "backimg" => '', "personimg" => '');
                        $address = array("marriage" => $marriage, "education" => $education, "industry" => $industry, "addess" => $addess, "addessMore" => $addessMore);
                        $concacts = array("zhishuRelation" => $zhishuRelation, "zhishuName" => $zhishuName, "zhishuPhone" => $zhishuPhone, "jinjiRelation" => $jinjiRelation, "jinjiName" => $jinjiName, "jinjiPhone" => $jinjiPhone);
                        $info = $infoModel->where(array("mobile" => $currentMobile))->find();
                        if (!$uid) {
                            throw new Exception("User add failure!");
                        }
                        $status = 2;
                        if (in_array($isAudit, ['是', 'yes', 'YES'])) {
                            $status = 1;
                        }
                        $save_data = array(
                            "uid" => $uid,
                            'identity' => json_encode($identity, JSON_UNESCAPED_UNICODE),
                            'contacts' => json_encode($concacts, JSON_UNESCAPED_UNICODE),
                            'addess' => json_encode($address, JSON_UNESCAPED_UNICODE),
                            'bank' => json_encode($cardInfo, JSON_UNESCAPED_UNICODE),
                            'mobile' => "" . $currentMobile,
                            'status' => $status,
                            'sid' => 1,
                        );
                        if (!empty($info)) {
                            $flag = $infoModel->where(array("mobile" => $currentMobile))->save($save_data);
                            if (!$flag) {
                                throw new Exception("User info update failure!");
                            }
                        } else {
                            $save_data['uid'] = $uid;
                            $flag = $infoModel->add($save_data);
                            if (!$flag) {
                                throw new Exception("User info add error!");
                            }
                        }
                        $tmpUser = array(
                            "id" => $uid,
                            "telnum" => $currentMobile,
                            "email" => $email,
                            "firstname" => $firstname,
                            "lastname" => $lastname,
                            "quota" => $quota,
                            "credit" => $credit,
                        );
                        $tmpInfoData = $save_data;
                        $order = $loanorderModel->addUserOrder($uid, $loanTime, 1, array(), $tmpUser, $tmpInfoData, 0);
                        $trans->commit();
                        $add_total++;

                    } catch (Exception $e) {
                        $trans->rollback();
                    }
                }
            }
        }
        return $add_total;
    }

    public function addOrder()
    {
        if ($this->isPost()) {
            $uid = I('uid');
            $loanorderModel = D("Loanorder");
            $loanTime = C('DEFAULT_LOAN_TIME');
            if ($loanTime <= 0) {
                $loanTime = 7;
            }
            $order = $loanorderModel->addUserOrder($uid, $loanTime, 1);
            $this->success('操作完成，请刷新页面查看');
        } else {
            $this->error('不支持的请求类型');
        }
    }

    public function create()
    {
        $adminInfo = $this->isLogin();
        if (!$adminInfo["status"]) {
            $this->error("您没有权限进行该操作");
        }
        if ($this->isPost()) {
            $telnum = I("telnum");
            $pass = I("password");
            $repass = I("repassword");
            $email = I("email");
            $bankName = I("bank_name");
            $bankNum = I("bank_num");
            $loanTime = I("loan_time", 7);
            $realName = I("real_name");
            $idcard = I("idcard");
            $firstname = I("firstname");
            $lastname = I("lastname");
            $status = I("status", 1);
            $quota = I("quota", C('DEFAULT_QUOTA'));
            $credit = I("credit", 0);
            if (!$telnum) {
                $this->error("请输入用户名/手机号");
            }
            if (!$pass) {
                $this->error("请输入用户登录密码");
            }
//            if ($email && !ereg("/^[a-z]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i; ", $email)) {
//                $this->error("请输入正确的邮箱");
//            }
//            if (!$bankNum) {
//                $this->error("请输入银行卡号");
//            }
            if (!$quota) {
                $this->error("请输入额度");
            }
            if (strlen($pass) < 6) {
                $this->error("登录密码长度不能小于 6 位");
            }
            if ($repass != $pass) {
                $this->error("两次密码输入不一致");
            }
            if ($loanTime <= 0) {
                $loanTime = 7;
            }
            $userModel = D("User");
            $user = $userModel->where(array("telnum" => $telnum))->find();
            if ($user) {
                $this->error("用户已经存在!");
            }
            $pass = $userModel->str2pass($pass);
            $userModel->startTrans();
            try {
                $insertId = $userModel->addUserInfo($telnum, $pass, $email, $firstname, $lastname, $quota, $credit);
                if ($insertId) {
                    $infoModel = D("Info");
                    $identity = json_encode(array("name" => $realName ? $realName : $telnum, "idcard" => $idcard));
                    $bank = json_encode(array("bankName" => $bankName, "bankNum" => $bankNum, "bankPhone" => $telnum));
                    $infoData = array("uid" => $insertId, 'identity' => $identity, 'bank' => $bank, 'mobile' => $telnum, 'status' => 2, 'sid' => 1);
                    $info = $infoModel->add($infoData);
                    if ($info) {
                        $loanorderModel = D("Loanorder");
                        $order = $loanorderModel->addUserOrder($insertId, $loanTime, $adminInfo['id']);
                        $loanOrder=$loanorderModel->where(['uid'=>$insertId])->find();

                        $loanbillModel=D("Loanbill");
                        $loanbillModel->createOrderBill($loanOrder,true);
                    } else {
                        throw new Exception("添加用户信息失败");
                    }

                } else {
                    throw new Exception("添加用户失败");
                }
                $userModel->commit();
                $this->success("添加成功");
            } catch (\Exception $e) {
                $this->error("添加失败:" . $e->getMessage());
            }


        }
        $this->display();
    }
}