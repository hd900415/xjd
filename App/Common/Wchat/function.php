<?php
function getUserInfo()
{
	@($userinfo = session("user"));
	return empty($userinfo) ? false : $userinfo;
}
function getViewPhone($number, $str = '*', $start = 4, $num = 4)
{
	$newstr = "";
	$newstr .= substr($number, 0, $start - 1);
	$i = 0;
	while ($i < $num) {
		$newstr .= $str;
		$i = $i + 1;
	}
	$newstr .= substr($number, $start - 1 + $num);
	return $newstr;
}
function paraFilter($para)
{
	$para_filter = array();
	while (list($key, $val) = each($para)) {
		if ($key == "sign" || $key == "sign_type" || $val == "") {
			continue;
		}
		$para_filter[$key] = $para[$key];
	}
	return $para_filter;
}
function argSort($para)
{
	ksort($para);
	reset($para);
	return $para;
}
function createLinkstring($para)
{
	$arg = "";
	while (list($key, $val) = each($para)) {
		$arg .= $key . "=" . $val . "&";
	}
	$arg = substr($arg, 0, count($arg) - 2);
	if (get_magic_quotes_gpc()) {
		$arg = stripslashes($arg);
	}
	return $arg;
}
function createLinkstringUrlencode($para)
{
	$arg = "";
	while (list($key, $val) = each($para)) {
		$arg .= $key . "=" . urlencode($val) . "&";
	}
	$arg = substr($arg, 0, count($arg) - 2);
	if (get_magic_quotes_gpc()) {
		$arg = stripslashes($arg);
	}
	return $arg;
}
function md5Sign($prestr, $key)
{
	$prestr = $prestr . $key;
	return md5($prestr);
}
function getProvince($ip = null)
{
	if (!$ip) {
		$ip = get_client_ip();
	}
//	$result = curl("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=" . $ip, array(), 0);
//	if (!$result) {
//		return "未知";
//	}
//	$arr = json_decode($result, true);
//	if ($arr && $arr["province"]) {
//		return $arr["province"];
//	}
	return "未知";
}
/*************************************/
function sigcol_arrsort($data,$col,$type=SORT_DESC){
  if(is_array($data)){
    $i=0;
    foreach($data as $k=>$v){
      if(key_exists($col,$v)){
        $arr[$i] = $v[$col];
        $i++;
      }else{
        continue;
      }
    }
  }else{
    return false;
  }
  array_multisort($arr,$type,$data);
  return $data;
}