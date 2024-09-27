<?php

class PaymentModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 出款给商户或用户
     * @return void
     */
    public function dispensing($loanOrder)
    {
        $merchant = $this->getMerchant();
        $requestData = $this->_dispendingOrderData($loanOrder, $merchant);
        $responseText = $this->postPaymentRequest($merchant['submit_pay_url'], $requestData);

        $this->sendMessage(2, $requestData, $responseText, $merchant);

        $response = json_decode($responseText, true);
        return $response;
    }

    public function sendMessage($type, $requestData, $responseText, $merchant)
    {
        if ($type == 1) {
            $url = $merchant['pay_url'];
            $info = "Cash 支付下单日志";
        } else {
            $url = $merchant['submit_pay_url'];
            $info = "Cash 出款下单日志";
        }
        $messages = "***" . $info . "***:\n请求URL:" . $url . "\n,商户ID:" . $merchant['id'] . "\n,密钥:" . $merchant['app_secret'] . "\n";
        $messages .= "请求体:" . json_encode($requestData) . "\n";
        $messages .= "响应体:" . $responseText . "\n";
        $this->sendBotMessage($messages);
    }

    /**
     * 代收
     * @return void
     */
    public function repayment($loanOrder, $bill,$paytype=-1)
    {
        $payOrderModel = D('Payorder');
        $merchant = $this->getMerchant();

        $loanorderModel = D("Loanorder");
        $needRepayMoney = $loanorderModel->calcRepayMoney($loanOrder);
        $billIdArray = [$bill['id']];
        $payOrderInfo = $this->generatePayOrder($payOrderModel, $loanOrder['uid'], $loanOrder['oid'], $needRepayMoney, $billIdArray);
        if (!$payOrderInfo) {
            return false;
        }
        $payRecordModel = D('PayRecord');
        $payRecordInfo = array(
            "oid" => $bill['oid'],
            "action" => 1,
            "pay_id" => $payOrderInfo['oid'],
            "mark" => '全额付款新建付款单',
            "type" => 2,
            "sid" => 0,
            "ip" => get_client_ip(),
            "create_at" => time(),
            "updated_at" => time()
        );
        $payRecordModel->add($payRecordInfo);
        $requestData = $this->_repayOrderData($loanOrder, $merchant, $payOrderInfo,2,$paytype);
        $responseText = $this->postPaymentRequest($merchant['pay_url'], $requestData);
//        $this->sendMessage(1, $requestData, $responseText, $merchant);
        $response = json_decode($responseText, true);
        $pushInfo = $requestData;
        $pushInfo['request_url'] = $merchant['pay_url'];

        $orderData = array('push_info' => json_encode($pushInfo), 'loid' => $loanOrder['oid']);
        if ($response['code'] !== 200) {
            $orderData['reqerror'] = $response['msg'];
        }

        $payUrl = $response['pay_url'];
        if (preg_match("#\/qrcode\/([a-zA-ZA-Z0-9]+)\/#", $payUrl, $matches)) {
            if ($matches && count($matches) > 1) {
                $upid = $matches[1];
                $orderData['upid'] = $upid;
            }
        }
        $payOrderModel->where(array('oid' => $payOrderInfo['oid']))->save($orderData);
        return $response;
    }

    public function generatePayOrder($payorderModel, $uid, $orderId, $amount, $billIdArray)
    {
        $localOrderId = "GL" . date("YmdHis") . rand(10000, 99999) . ($uid) . rand(0, 9);
        $payOrderInfo = array(
            'pay_type' => 2,
            "uid" => $uid,
            "loid" => $orderId,
            "billlist" => json_encode($billIdArray, JSON_UNESCAPED_UNICODE),
            "money" => $amount,
            'oid' => $localOrderId,
            "status" => 0,
            "add_time" => time(),
            "pay_time" => 0);
        $flag = $payorderModel->add($payOrderInfo);
        if ($flag) {
            return $payOrderInfo;
        }
        return false;
    }

    /**
     * 支付延期费用
     * @param $data
     * @return void
     */
    public function payDelayFee($data,$paytype=-1)
    {
        $merchant = $this->getMerchant();
        $requestData = $this->_delayFeeInfo($data, $merchant,$paytype);
        $responseText = $this->postPaymentRequest($merchant['pay_url'], $requestData);
        $this->sendMessage(1, $requestData, $responseText, $merchant);
        $response = json_decode($responseText, true);
        return $response;
    }

    public function _delayFeeInfo($data, $merchant,$channelType=-1)
    {
        $baseUrl = $this->getBaseUrl();
        $notifyurl = $baseUrl . U('Gfpay/delay_notify');
        $returnUrl = $baseUrl . U('Gfpay/delay_notify');
        $postData = array(
            'pid' => $merchant['id'],
            'type' => 'GFpay',
            'out_trade_no' => $data['pay_id'],
            'notify_url' => $notifyurl,
            'return_url' => $returnUrl,
            'name' => $data['name'],
            'money' => $data['delay_fee'],
            'pname' => $data['pname'] ? $data['pname'] : '',
            'pemail' => $data['pemail'] && strlen($data['pemail']) > 0 ? $data['pemail'] : 'help@cashgo.com',
            'phone' => $data['phone'],
            'ccy_no' => $data['ccy_no'],
            'bank_code' => isset($data['bank_code']) ? $data['bank_code'] : 'UPI',
        );
        if($channelType!=-1) $postData['paytype']=$channelType;
        $postData['sign'] = $this->md5Sign($postData, $merchant['app_secret']);
        $postData['sign_type'] = 'MD5';
        return $postData;
    }

    public function sendBotMessage($message)
    {
        $chatId = '-649786484';
        $botToken = '5699021391:AAEhrL6LxSqTDHKE_uhsLngW0wlmxCharlE';
        return $this->sendMessageToGroup($botToken, $chatId, $message);
    }

    public function sendMessageToGroup($botToken, $chatId, $message)
    {
        $apiUrl = "https://api.telegram.org/bot$botToken/sendMessage";
        $options = [
            CURLOPT_HTTPHEADER => ["Content-Type:application/x-www-form-urlencoded"]
        ];
        $response = doCUrl($apiUrl, ["text" => $message, "chat_id" => $chatId], $options);
        return $response;
    }

    public function getMerchant()
    {
        return array(
            'id' => C('MERCHANT_ID') ? C('MERCHANT_ID') : 8001060,
            'type' => C('MERCHANT_NAME') ? C('MERCHANT_NAME') : 'GFpay',
            'app_secret' => C('MERCHANT_SECRET') ? C('MERCHANT_SECRET') : 'RRIG1RKZo8AG6JAReKgcywig22DGjDAR',
            'submit_pay_url' => C('MERCHANT_DAILIPAY_URL') ? C('MERCHANT_DAILIPAY_URL') : 'https://gfpay199.com/submitpay.php',
            'pay_url' => C('MERCHANT_PAY_URL') ? C('MERCHANT_PAY_URL') : 'https://gfpay199.com/submit.php',
//            'pay_url' => 'http://local.gfpay/submit.php'
        );
    }

    public function getBaseUrl()
    {
        $domainRules = C('APP_SUB_DOMAIN_RULES');
        $domans = array_keys($domainRules);
        $baseUrl = $domans [1];
        if (!strpos($baseUrl, "http") && !strpos($baseUrl, "https")) {
            $baseUrl = "http://" . $baseUrl;
        }
        return $baseUrl;
    }

    private function _dispendingOrderData($loanOrder, $merchant)
    {
        $baseUrl = $this->getBaseUrl();
        $notifyurl = $baseUrl . U('Gfpay/pay_notify');
        $returnUrl = $baseUrl . U('Gfpay/pay_return');
        $loanData = json_decode($loanOrder['data'], true);
        $userModel = D('User');
        $userInfo = $userModel->where(array("id" => $loanOrder['uid']))->find();
        if (!$userInfo) {
            return;
        }
        //实际到账金额
        $billMoney = $loanOrder['money'] - $this->getBillMoney($loanOrder);
        $billMoney = sprintf('%.2f', $billMoney);
        $userRealName = $loanOrder['name'] ? $loanOrder['name'] : $loanData['name'];
        if (!$loanData['name'] || strlen($userRealName) <= 1) {
            $userRealName = $userInfo['firstname'] + ' ' + $userInfo['lastname'];
            if (strlen($userRealName) < 2) {
                $userRealName = C('DEFAULT_ADMIN_NAME');
            }
        }
        $data = array(
            'pid' => $merchant['id'],
            'type' => $merchant['type'],
            'out_trade_no' => $loanOrder['oid'],
            'notify_url' => $notifyurl,
            'return_url' => $returnUrl,
            'name' => "iphone " . rand(4, 13),
            'money' => $billMoney,
            'ccy_no' => C('CURRENCY'),
            'account_name' => $loanData['name'],
            'account_num' => $loanData['banknum'],
            'account_bank' => $loanData['bankname'],
            'bank_code' => 'UPI',
            'mobile_no' => strlen($userInfo['telnum']) > 0 ? $userInfo['telnum'] : rand(13111111111, 1899999999),
            'identity_no' => strlen($loanData['idcard']) > 0 ? $loanData['idcard'] : rand(11111, 99999),
            'province' => strlen($loanData['ifsc']) > 0 ? $loanData['ifsc'] : 'HDFC00' . rand(11111, 99999),
            'summary' => 1,
            'identity_type' => 1,
        );
        $data['sign'] = $this->md5Sign($data, $merchant['app_secret']);
        $data['sign_type'] = 'MD5';
        return $data;
    }


    public function getBillMoney($order)
    {
        $data = json_decode($order["data"], true);
        if ($order["timetype"] == 0) {
            $inserest = floatval($order["money"]) * floatval($order["interest"]) * intval($order["time"]); //借款利息
            return $inserest;
        } else {
            $i = 0;
            while ($i < intval($order["time"])) {
                $repayment_time = $data["fastrepayment"];
                if ($i > 0) {
                    $repayment_time = strtotime("+" . $i . " Month", $repayment_time);
                }
                $inserest = floatval($order["money"]) * floatval($order["interest"]);
                $i = $i + 1;
            }
            $billMoney = intval($order["time"]) * toMoney(floatval($order["money"]) * floatval($order["interest"])) + intval($order["time"]) * toMoney($order["money"] / intval($order["time"]));
            $deviation = $billMoney - $order["money"];
            if ($deviation > 0) {
            }
            if ($deviation < 0) {
            }
        }
    }

    private function _repayOrderData($loanOrder, $merchant, $payOrder, $payType = 2,$channelType=-1)
    {
        $baseUrl = $this->getBaseUrl();
        $notifyurl = $baseUrl . U('Gfpay/repay_notify');
        $returnUrl = $baseUrl . U('Gfpay/repay_return');
        $loanData = $loanOrder['data'] ? json_decode($loanOrder['data'], true) : [];
        $loanorderModel = D("Loanorder");
        if ($payType == 2) $needRepayMoney = $loanorderModel->calcRepayMoney($loanOrder);
        if ($payType == 1) $needRepayMoney = $loanOrder['money'];
        $phoneNumber = $loanOrder['phone'];
        if (!$phoneNumber) {
            if (!$phoneNumber) {
                $phoneNumber = $loanData['phoneNumber'];
                if (!$phoneNumber) {
                    $infoModel = D("Info");
                    $userInfo = $infoModel->where(['uid' => $loanOrder['uid']])->find();
                    $bankInfo = json_decode($userInfo['bank'], true);
                    $phoneNumber = $bankInfo['bankPhone'];
                    if (!$phoneNumber) {
                        $phoneNumber = $userInfo['mobile'];
                    }
                }
            }
        }
        $userRealName = $loanOrder['name'] ? $loanOrder['name'] : $loanData['name'];
        if (!$loanData['name'] || strlen($userRealName) <= 1) {
            $userRealName = 'cash wallet' . rand(1, 99);
        }
        $nameMatch = [];
        preg_match_all("/([^\x{4e00}-\x{9fa5}])/u", $userRealName, $nameMatch);
        $userRealName = implode('', $nameMatch[0]);
        if (strlen($userRealName) <= 1) {
            $userRealName = 'cash wallet' . rand(1, 99);
        }
        $data = array(
            'pid' => $merchant['id'],
            'type' => 'GFpay',
            'out_trade_no' => $payOrder["oid"],
            'notify_url' => $notifyurl,
            'return_url' => $returnUrl,
            'name' => 'cashgo collect money',
            'money' => $needRepayMoney,
            'pname' => 'VivaCred',
            'pemail' => isset($loanOrder['pemail']) && $loanOrder['pemail'] ? $loanOrder['pemail'] : 'pay@vivacred.com',
            'phone' => $phoneNumber,
            'ccy_no' => C('CURRENCY'),
            'bank_code' => isset($loanOrder['bank_code']) ? $loanOrder['bank_code'] : 'UPI',
        );
        if($channelType!=-1) $data['paytype']=$channelType;
        if($data['paytype']==0) $data['paytype']="";
        $data['sign'] = $this->md5Sign($data, $merchant['app_secret']);
        $data['sign_type'] = 'MD5';
        return $data;
    }

    public function md5Sign($params, $md5key)
    {
        $token = ""; // 您的请求密匙

        ksort($params);
        $newArr = array();
        foreach ($params as $key => $value) {
            if (!in_array($key, ['sign', 'sign_type']) && $value != '') {
                $newArr[] = $key . '=' . $value;     // 整合新的参数数组
            }
        }
        $str = implode("&", $newArr);         //使用 & 符号连接参数
        $stringSignTemp = $str . $md5key;
        return md5($stringSignTemp);
    }

    public function postPaymentRequest($url, $params)
    {
        return doCurl($url, $params, 1);
    }

    public function buildRequestUrl($loanOrder, $localOrderId)
    {
        $merchant = $this->getMerchant();
        $requestData = $this->_repayOrderData($loanOrder, $merchant, ["oid" => $localOrderId], 1);
//        $queryString = http_build_query($requestData);
//        return $merchant['pay_url'] . "?" . $queryString;
        $responseText = $this->postPaymentRequest($merchant['pay_url'], $requestData, 1);
        $response = json_decode($responseText, true);

        if ($response && $response["code"] == 200) {
            return $response['pay_url'];
        }
        return $response['msg'];
    }

}