<?php

class UserModel extends Model
{
    public function str2pass($str)
    {
        if (!$str) {
            return false;
        }
        return sha1(md5($str));
    }

    public function getInfo($par1, $par2 = null, $name = null)
    {
        $w = array();
        if (!$par1) {
            return false;
        }
        if (is_array($par1)) {
            $w = $par1;
            $info = $this->where($w)->find();
            if (!$info) {
                return false;
            }
            if ($name && isset($info[$name])) {
                return $info[$name];
            }
            if ($par2 && !is_array($par2) && isset($info[$par2])) {
                return $info[$par2];
            }
            return $info;
        }
        if (!$par2) {
            return false;
        }
        $w = array($par1 => $par2);
        $info = $this->where($w)->find();
        if (!$info) {
            return false;
        }
        if ($name && isset($info[$name])) {
            return $info[$name];
        }
        return $info;
    }

    public function addInfo($tel, $pass)
    {
        return $this->add(array("telnum" => $tel, "password" => $pass, "status" => 1, "reg_time" => time(), "reg_city" => getProvince(), "reg_ip" => get_client_ip()));
    }

    public function addUserInfo($tel, $pass, $email, $firstname, $lastname, $quota, $credit)
    {
        return $this->add(array("telnum" => $tel, "password" => $pass,"email"=>$email,"firstname"=>$firstname,"lastname"=>$lastname,"quota"=>$quota,"credit"=>$credit, "status" => 1, "reg_time" => time(), "reg_city" => getProvince(), "reg_ip" => get_client_ip()));
    }

    public function updateInfo($id = 0, $arr = array())
    {
        if (!$id || !$arr) {
            return false;
        }
        return $this->where(array("id" => $id))->save($arr);
    }

    public function getDoquota($id = 0)
    {
        if (!$id) {
            return false;
        }
        $quota = $this->getInfo("id", $id, "quota");
        if (!$quota) {
            return 0;
        }
        $loanbillModel = D("Loanbill");
        $has = $loanbillModel->where(array("uid" => $id, "status" => array("in", "0,1")))->sum("money");
        return !(toMoney($quota - $has) >= 0) ? 0 : toMoney($quota - $has);
    }
}