<?php
class InfoModel extends RelationModel
{
	protected $_link = array('User' => array('mapping_type' => BELONGS_TO, 'foreign_key' => 'uid', 'mapping_name' => 'user'));
	public function checkInfo($uid = 0)
	{
		if (!$uid) {
			return false;
		}
		$userAuth = $this->where(array("uid" => $uid))->find();
		if ($userAuth) {
			return true;
		}
		$result = $this->add(array("uid" => $uid));
		return $result;
	}
	public function checkAllInfo($uid = 0)
	{
		if (!$uid) {
			return false;
		}
		$userAuth = $this->where(array("uid" => $uid))->find();
		if (!$userAuth) {
			return false;
		}
		foreach ($userAuth as $key => $val) {
			if (strlen($val) == 0) {
				return false;
			}
		}
		return true;
	}
	public function getAuthInfo($uid = 0, $name = null)
	{
		if (!$uid) {
			return false;
		}
		$userAuth = $this->where(array("uid" => $uid))->relation(true)->find();
		if (!$name) {
			return $userAuth;
		}
		if (!isset($userAuth[$name])) {
			return false;
		}
		return $userAuth[$name];
	}
	public function hasSetIdentity($uid = 0)
	{
		if (!$uid) {
			return false;
		}
		return true;
		$userAuth = $this->where(array("uid" => $uid))->find();
		if (!$userAuth) {
			return false;
		}
		if (!empty($userAuth['identity'])) {
			return true;
		}
		return false;
	}
	public function setIdentity($uid = 0, $arr = array())
	{
		if (!$uid || !$arr) {
			return false;
		}
		$result = $this->where(array("uid" => $uid))->save(array("identity" => json_encode($arr)));
		return $result;
	}
	public function hasSetContacts($uid = 0)
	{
		if (!$uid) {
			return false;
		}
			return true;
		$userAuth = $this->where(array("uid" => $uid))->find();
		if (!$userAuth) {
			return false;
		}
		if (!empty($userAuth['contacts'])) {
			return true;
		}
		return false;
	}
	public function setContacts($uid = 0, $arr = array())
	{
		if (!$uid || !$arr) {
			return false;
		}
		$result = $this->where(array("uid" => $uid))->save(array("contacts" => json_encode($arr)));
		return $result;
	}
	public function hasSetBank($uid = 0)
	{
		if (!$uid) {
			return false;
		}
			return true;
		$userAuth = $this->where(array("uid" => $uid))->find();
		if (!$userAuth) {
			return false;
		}
		if (!empty($userAuth['bank'])) {
			return true;
		}
		return false;
	}
	public function setBank($uid = 0, $arr = array())
	{
		if (!$uid || !$arr) {
			return false;
		}
	
		$result = $this->where(array("uid" => $uid))->save(array("bank" => json_encode($arr)));
		return $result;
	}
	public function hasSetAddess($uid = 0)
	{
		if (!$uid) {
			return false;
		}
			return true;
		$userAuth = $this->where(array("uid" => $uid))->find();
		if (!$userAuth) {
			return false;
		}
		if (!empty($userAuth['addess'])) {
			return true;
		}
		return false;
	}
	public function setAddess($uid = 0, $arr = array())
	{
		if (!$uid || !$arr) {
			return false;
		}
		$result = $this->where(array("uid" => $uid))->save(array("addess" => json_encode($arr)));
		return $result;
	}
	public function hasSetMobile($uid = 0)
	{
		if (!$uid) {
			return false;
		}
			return true;
		$userAuth = $this->where(array("uid" => $uid))->find();
		if (!$userAuth) {
			return false;
		}
		if (!empty($userAuth['mobile'])) {
			return true;
		}
		return false;
	}
	public function setMobile($uid = 0, $data = '')
	{
		if (!$uid || !$data) {
			return false;
		}
		$result = $this->where(array("uid" => $uid))->save(array("mobile" => $data));
		return $result;
	}
	public function hasSetTaobao($uid = 0)
	{
		if (!$uid) {
			return false;
		}
			return true;
		$userAuth = $this->where(array("uid" => $uid))->find();
		if (!$userAuth) {
			return false;
		}
		if (!empty($userAuth['taobao'])) {
			return true;
		}
		return false;
	}
	public function setTaobao($uid = 0, $data = '')
	{
		if (!$uid || !$data) {
			return false;
		}
		$result = $this->where(array("uid" => $uid))->save(array("taobao" => $data));
		return $result;
	}
	public function getStatus($uid = 0)
	{
		if (!$uid) {
			return false;
		}
		$userAuth = $this->where(array("uid" => $uid))->find();
		if (!$userAuth) {
			return false;
		}
		if (!isset($userAuth['status'])) {
			return false;
		}
		return $userAuth["status"];
	}
	public function setStatus($uid = 0, $status = 0)
	{
		if (!$uid || !isset($status)) {
			return false;
		}
		//分配订单
		if($status == 0){
			$admins = M('admin')->where(array('type'=>2))->select();
			$carrays = array();
			if($admins){
				foreach($admins as $v){
					$count = $this->getOrderCount($v['id']);
					$carrays[] = array('uid'=>$v['id'],'count'=>$count);
				}
			}
			$carrays = sigcol_arrsort($carrays,'count',SORT_ASC);
			//var_dump($carrays);die;
			$sid = $carrays[0]['uid'];
			if(!$sid){
				$sid = 0;
			}		
			$result = $this->where(array("uid" => $uid))->save(array("status" => $status,"sid"=>$sid));
		}else{
			$result = $this->where(array("uid" => $uid))->save(array("status" => $status));
		}
		return $result;
	}
	public function getOrderCount($adminid){
		$count = $this->where(array('sid'=>$adminid,'status'=>1))->count();
		//var_dump($this->getLastSql());
		return $count;
	}	
}