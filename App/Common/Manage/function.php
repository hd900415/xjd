<?php

function num2str($num = 0)
{
	$str = "";
	if ($num > 9999999 || strlen(intval($num)) > 7) {
		$num = toMoney($num / 100000);
		if (intval($num) > 9) {
			$num = round($num, 1);
		}
		if (intval($num) > 99) {
			$num = round($num);
		}
		if ($num >= 1000) {
			$num = toMoney($num / 1000);
			$arr = str_split($num);
			foreach ($arr as $val) {
				$str .= "<em>" . $val . "</em>" . "\n";
			}
			$str .= "<em>亿</em>";
			return $str;
		}
		$arr = str_split($num);
		foreach ($arr as $val) {
			$str .= "<em>" . $val . "</em>" . "\n";
		}
		$str .= "<em>千万</em>";
	} else {
		if ($num > 9999999 || strlen(intval($num)) > 7) {
			$num = toMoney($num / 10000);
			if (intval($num) > 9) {
				$num = round($num, 1);
			}
			if (intval($num) > 99) {
				$num = round($num);
			}
			$arr = str_split($num);
			foreach ($arr as $val) {
				$str .= "<em>" . $val . "</em>" . "\n";
			}
			$str .= "<em>百万</em>";
		} else {
			if ($num > 99999 || strlen($num) > 4) {
				$num = toMoney($num / 10000);
				if (intval($num) > 9) {
					$num = round($num, 1);
				}
				if (intval($num) > 99) {
					$num = round($num);
				}
				$arr = str_split($num);
				foreach ($arr as $val) {
					$str .= "<em>" . $val . "</em>" . "\n";
				}
				$str .= "<em>万</em>";
			} else {
				$arr = str_split($num);
				foreach ($arr as $val) {
					$str .= "<em>" . $val . "</em>" . "\n";
				}
			}
		}
	}
	return $str;
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