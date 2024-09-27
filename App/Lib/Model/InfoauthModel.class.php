<?php
class InfoauthModel extends Model
{
	public function addAuth($type = '', $uid = 0, $callid = '')
	{
		$arr = $this->where(array("uid" => $uid, "type" => $type))->find();
		if ($arr) {
			$arr = array("callid" => $callid, "add_time" => time(), "data" => "");
			$result = $this->where(array("uid" => $uid, "type" => $type))->save($arr);
			return $result;
		}
		$result = $this->add(array("type" => $type, "uid" => $uid, "callid" => $callid, "add_time" => time(), "data" => ""));
		return $result;
	}
	public function setAuthData($callid, $data)
	{
		$result = $this->where(array("callid" => $callid))->save(array("data" => $data));
		return $result;
	}
	public function getData($callid, $type)
	{
		$arr = $this->where(array("callid" => $callid, "type" => $type))->find();
		return $arr;
	}
}