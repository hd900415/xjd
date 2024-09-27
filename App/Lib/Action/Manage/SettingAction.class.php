<?php

class SettingAction extends CommonAction
{
    public function index()
    {
        if ($this->isPost()) {
            $arr = I("post.");
            $file = CONF_PATH . "site.php";
            if ($arr["fileSuffix"]) {
                $str = str_replace("，", ",", $arr["fileSuffix"]);
                $arr["fileSuffix"] = explode(",", $str);
                $arr["fileSuffix"] = array_values($arr["fileSuffix"]);
            }
            $save=[
                'DEFAULT_TIMEZONE'=>$arr['DEFAULT_TIMEZONE'],
                'phone_number'=>$arr['phone_number'],
                'whatapp'=>$arr['whatapp'],
            ];
            unset($arr['DEFAULT_TIMEZONE']);
            unset($arr['phone_number']);
            unset($arr['whatapp']);
            if (!save_config($arr, $file, true)) {
                $this->error("配置保存失败");
            }
            $settingFile = CONF_PATH . 'settings.php';

            save_config($save, $settingFile, true);
            $this->success("操作成功");
        }
        $this->display();
    }

    public function api()
    {
        if ($this->isPost()) {
            $arr = I("post.");
            $file = CONF_PATH . "api.php";
            if (!save_config($arr, $file, true)) {
                $this->error("配置保存失败");
            }
            $this->success("操作成功");
        }
        $this->display();
    }

    public function loan()
    {
        $file = CONF_PATH . "loan.php";
        if ($this->isPost()) {
            $arr = I("post.");

            if ($arr["Deadline_D"]) {
                $arr["Deadline_D"] = str_replace("，", ",", $arr["Deadline_D"]);
                $arr["Deadline_D"] = explode(",", $arr["Deadline_D"]);
            }
            if ($arr["Deadline_M"]) {
                $arr["Deadline_M"] = str_replace("，", ",", $arr["Deadline_M"]);
                $arr["Deadline_M"] = explode(",", $arr["Deadline_M"]);
                $arr["Deadline_M"] = array_values($arr["Deadline_M"]);
            }
            $arr["DAISHOU_SUBMIT_TO_PAYER"] = intval($arr["DAISHOU_SUBMIT_TO_PAYER"]) == 1 ? 1 : 0;
            $arr["DAIFU_SUBMIT_TO_PAYER"] = intval($arr["DAIFU_SUBMIT_TO_PAYER"]) == 1 ? 1 : 0;
            $arr["REPAY_COST"] = intval($arr["REPAY_COST"]);
            if (!save_config($arr, $file, true)) {
                $this->error("配置保存失败");
            }
            $this->success("操作成功");
        }
        $allLang=explode(',',C('AllLanguages'));
        $allTargets=explode(',',C('AllTargets'));

        if(!C('AllLanguages')||empty(array_unique($allLang))) $allLang=['English','Spanish','Filipino'];
        if(!C('AllTargets')||empty(array_unique($allTargets))) $allTargets=['web','app'];
        $this->assign('Languages',explode(',',C('Languages')));
        $this->assign('AllLanguages',$allLang);
        $this->assign('allTargets',$allTargets);
        $this->display();
    }

    public function contract()
    {
        if ($this->isPost()) {
            $arr = I("post.");
            $file = CONF_PATH . "contract.php";
            if (!save_config($arr, $file, true)) {
                $this->error("配置保存失败");
            }
            $this->success("操作成功");
        }
        $this->display();
    }

    public function other()
    {
    }

    public function uploadImg()
    {
        if ($this->isPost()) {
            $fileName = I("fileName");
            if (!$fileName) {
                $this->error("提交参数有误");
            }
            $fileModel = D("File");
            $File = $fileModel->getFile($fileName);
            if (!$File) {
                $this->error("文件上传出错");
            }
            if (!$File["status"]) {
                $this->error($File["error"]);
            }
            $this->success($File["url"]);
        }
        $this->error("非法操作");
        return NULL;
    }


    public function clearData()
    {
        if ($this->isPost()) {
            $type = I('type');
            $date = I('date', 15);
            switch ($type) {
                case 'pay_order':
                    $payorderModel = D("Payorder");
                    $status = $payorderModel->where(array('add_time' => array('ELT', time() - $date * 86400)))->delete();
                    break;
                case 'delay_pay':
                    $loanbillDelayModel = D("LoanbillDelay");
                    $status = $loanbillDelayModel->where(array('created_at' => array('ELT', time() - $date * 86400)))->delete();
                    break;
                case 'loan_order':
                    $loanorderModel = D("Loanorder");
                    $loanbillModel = D("Loanbill");
                    $status = $loanorderModel->where(array('add_time' => array('ELT', time() - $date * 86400)))->delete();
                    $loanbillModel->where(array('_string' => 'toid not in (select id from ' . $loanorderModel->getTableName() . ')'))->delete();
                    break;
                case 'loan_bill':
                    $loanbillModel = D("Loanbill");
                    $status = $loanbillModel->where(array('add_time' => array('ELT', time() - $date * 86400)))->delete();
                    break;
                case 'pay_proof':
                    $payProofModel = D("PayProof");
                    $status = $payProofModel->where(array('add_time' => array('ELT', time() - $date * 86400)))->delete();
                    break;
                case 'user':
                    $userModel = D("User");
                    $status = $userModel->where(array('reg_time' => array('ELT', time() - $date * 86400)))->delete();
                    break;
            }
            if ($status) {
                $this->success("操作成功");
            } else {
                $this->success("操作失败");
            }

        }
    }
}