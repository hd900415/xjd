<?php
#验证手机号规则
function isMobile($num = '')
{
    if (!is_numeric($num)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $num) ? true : false;
}

#CURL请求
function doCurl($url, $params = false, $ispost = 0)
{
    $httpInfo = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//    curl_setopt($ch, CURLOPT_USERAGENT, 'limuzhengxin.com');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === FALSE) {
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}

#获取期限列表带单位
function getDeadlineList()
{
    $type = C('Loan_TYPE');
    if ($type) {
        $str = 'Months';
        $list = C('Deadline_M');
    } else {
        $str = 'days';
        $list = C('Deadline_D');
    }
    return array(
        'str' => $str,
        'list' => $list
    );
}

#获取借款值度
function getMoneyScale()
{
    $min = C('Money_MIN');
    $max = C('Money_MAX');
    $step = C('Money_STEP');
    $currency = C('CURRENCY');
    $currency_symbol = C('CURRENCY_SYMBOL');
    $userinfo = @session("user");

    if ($userinfo) {
        $user = M('User')->where(array('id' => $userinfo['id']))->find();
        $max = $quota = $user['quota'];
        $loanbillModel = D("Loanbill");
        $notrepayMoney = $loanbillModel->getUserNotRepayMoney($userinfo['id']);
        if ($notrepayMoney) {
            $max = $max - $notrepayMoney;
        }

    }

    return array('min' => $min, 'max' => $max, 'step' => $step, 'currency' => $currency, 'currency_symbol' => $currency_symbol);
}

#获取借款利息
function getInterest()
{
    $type = C('Loan_TYPE');
    $d = C('Interest_D');
    $m = C('Interest_M');
    if ($type) {
        return $m;
    } else {
        return $d;
    }
}

//获取还款利息
function getRepayRate()
{
    return C('Interest_R');
}

#验证身份证号码有效性
function isIdCard($number)
{
    if (empty($number)) return false;
    //加权因子
    $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码串
    $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    //按顺序循环处理前17位
    $sigma = 0;
    for ($i = 0; $i < 17; $i++) {
        //提取前17位的其中一位，并将变量类型转为实数
        $b = (int)$number{$i};
        //提取相应的加权因子
        $w = $wi[$i];
        //把从身份证号码中提取的一位数字和加权因子相乘，并累加
        $sigma += $b * $w;
    }
    //计算序号
    $snumber = $sigma % 11;
    //按照序号从校验码串中提取相应的字符。
    $check_number = $ai[$snumber];
    if ($number{17} == $check_number) {
        return true;
    } else {
        return false;
    }
}

#验证姓名有效性
function isChineseName($name)
{
    if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name)) {
        return true;
    } else {
        return false;
    }
}

#金额格式化
function toMoney($num)
{
    $num_tmp = number_format($num, 2, '.', '');
    //$num_tmp = floatval($num_tmp);
    if ($num_tmp < $num) return $num_tmp + 0.01;
    return $num_tmp;
}

#是否为微信内访问
function isWchat()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)
        return true;
    else
        return false;
}

/**
 * @param $birthday 出生年月日（1992-1-3）
 * @return string 年龄
 */
function countage($birthday)
{
    $year = date('Y');
    $month = date('m');
    if (substr($month, 0, 1) == 0) {
        $month = substr($month, 1);
    }
    $day = date('d');
    if (substr($day, 0, 1) == 0) {
        $day = substr($day, 1);
    }
    $arr = explode('-', $birthday);
    $age = $year - $arr[0];
    if ($month < $arr[1]) {
        $age = $age - 1;
    } elseif ($month == $arr[1] && $day < $arr[2]) {
        $age = $age - 1;
    }
    return $age;
}

/**
 * 将数组写出配置文件
 * @param $arr 写入数组
 * @param $filename 保存文件名
 * @param $reset 是否合并
 * @param $delother 删除多余
 * @return bool
 */
function save_config($arr, $filename, $reset = false, $delother = false)
{
    $filepath = $filename;
    if (!file_exists($filepath)) {
        $file = fopen($filepath, "w");
        fclose($file);
    }
    $oldarr = include($filepath);
    if (is_array($oldarr)) {
        foreach ($oldarr as $key => $value) {
            if (!isset($arr[$key])) {
                if (!$delother) {
                    $arr[$key] = $value;
                }
            } else {
                if (!$reset) {
                    $arr[$key] = $value;
                }
            }
        }
    }
    //写出文件
    $str = '<?php return array(';
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            $str .= "'{$key}' => array(";
            foreach ($value as $key_ => $val_) {
                $val_ = htmlspecialchars($val_);
                $str .= "'{$key_}'=>'{$val_}',";
            }
            $str .= "),";
        } else {
            $value = htmlspecialchars($value);
            $str .= "'{$key}'=>'{$value}',";
        }
    }
    $str .= ');';
    if (!file_put_contents($filepath, $str))
        return false;
    return true;
}

#金额转大写
function cny($ns)
{
    static $cnums = array("零", "壹", "贰", "叁", "肆", "伍", "陆", "柒", "捌", "玖"),
    $cnyunits = array("圆", "角", "分"),
    $grees = array("拾", "佰", "仟", "万", "拾", "佰", "仟", "亿");
    list($ns1, $ns2) = explode(".", $ns, 2);
    $ns2 = array_filter(array($ns2[1], $ns2[0]));
    $ret = array_merge($ns2, array(implode("", _cny_map_unit(str_split($ns1), $grees)), ""));
    $ret = implode("", array_reverse(_cny_map_unit($ret, $cnyunits)));
    return str_replace(array_keys($cnums), $cnums, $ret);
}


function _cny_map_unit($list, $units)
{
    $ul = count($units);
    $xs = array();
    foreach (array_reverse($list) as $x) {
        $l = count($xs);
        if ($x != "0" || !($l % 4)) $n = ($x == '0' ? '' : $x) . ($units[($l - 1) % $ul]);
        else $n = is_numeric($xs[0][0]) ? $x : '';
        array_unshift($xs, $n);
    }
    return $xs;
}

/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
 * @param string $day1
 * @param string $day2
 * @return number
 */
function diffBetweenTwoDays($day1, $day2)
{
    $second1 = $day1;
    $second2 = $day2;
    if ($second1 < $second2) {
        $tmp = $second2;
        $second2 = $second1;
        $second1 = $tmp;
    }
    return ($second1 - $second2) / 86400;
}

function generateRandomString($len = 10)
{
    $str = "";
    $text = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for ($i = 0; $i < $len; $i++) {
        $index = mt_rand(0, 61);
        $str .= substr($text, $index, 1);
    }
    return $str;
}
