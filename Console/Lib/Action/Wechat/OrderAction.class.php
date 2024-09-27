<?php

class OrderAction extends Action
{
    private $apiConfig;

    public function __construct()
    {
        parent::__construct();
        $this->apiConfig = require_once(ROOT_PATH . 'App/Conf/api.php');
        ini_set('display_errors', 'on');
        error_reporting(E_ALL);
        ignore_user_abort(true);
        set_time_limit(0);
    }
    public function sync(){
        while(true){
            $this->syncData();
            sleep(1);
        }
    }
    public function delaySync(){
        while(true){
            $this->delaySyncData();
            sleep(1);
        }
    }
    public function syncData()
    {
        $payorderModel = D("Payorder");
        $time=time()-600;
        $order = $payorderModel->where('api_trade_no is null and add_time<'.$time)->order('add_time DESC')->field('oid')->find();
        if(empty($order)||empty($order['oid'])) return;
        $merchant = $this->getMerchant();
        $responseTxt = $this->getOrderDetail($merchant, $order['oid']);
        $response = json_decode($responseTxt, JSON_UNESCAPED);
        print($order['oid']);
        if ($response['code'] == 1) {
            if ($response['api_no']) {
                $payorderModel->where(array("oid" => $response['out_trade_no']))->save(['api_trade_no' => $response['api_no']]);
            } else {
                $payorderModel->where(array("oid" => $response['out_trade_no']))->save(['api_trade_no' => 0]);
            }

        }
    }

    public function delaySyncData()
    {
        $loanbillDelay = D("LoanbillDelay");
        $time=time()-600;
        $order = $loanbillDelay->where('api_trade_no is null and created_at<'.$time)->order('created_at DESC')->field('pay_id')->find();
        if(empty($order)||empty($order['pay_id'])) return;
        $merchant = $this->getMerchant();
        $responseTxt = $this->getOrderDetail($merchant, $order['pay_id']);
        $response = json_decode($responseTxt, JSON_UNESCAPED);
        print($order['pay_id']);
        if ($response['code'] == 1) {
            if ($response['api_no']) {
                $loanbillDelay->where(array("pay_id" => $response['out_trade_no']))->save(['api_trade_no' => $response['api_no']]);
            } else {
                $loanbillDelay->where(array("pay_id" => $response['out_trade_no']))->save(['api_trade_no' => 0]);
            }

        }
    }
    public function clear(){
        $loanbillDelay = D("LoanbillDelay");
        $payorderModel = D("Payorder");

        $payorderModel->where(['api_trade_no'=>'0'])->save(['api_trade_no'=>null]);
        echo $payorderModel->getLastSql();echo "\n";
        $loanbillDelay->where(['api_trade_no'=>'0'])->save(['api_trade_no'=>null]);
        echo $loanbillDelay->getLastSql();
    }

    public function getOrderDetail($merchant, $oid)
    {
        $postData = [
            'act' => 'order',
            'pid' => $merchant['id'],
            'key' => $merchant['app_secret'],
            'out_trade_no' => $oid
        ];
        $response = $this->doCurl($merchant['api_url'], http_build_query($postData));
        return $response;
    }

    public function getMerchant()
    {
        return array(
            'id' => $this->getConfig('MERCHANT_ID') ? $this->getConfig('MERCHANT_ID') : 8001060,
            'type' => $this->getConfig('MERCHANT_NAME') ? $this->getConfig('MERCHANT_NAME') : 'GFpay',
            'app_secret' => $this->getConfig('MERCHANT_SECRET') ? $this->getConfig('MERCHANT_SECRET') : 'RRIG1RKZo8AG6JAReKgcywig22DGjDAR',
            'submit_pay_url' => $this->getConfig('MERCHANT_DAILIPAY_URL') ? $this->getConfig('MERCHANT_DAILIPAY_URL') : 'https://gfpay199.com/submitpay.php',
            'pay_url' => $this->getConfig('MERCHANT_PAY_URL') ? $this->getConfig('MERCHANT_PAY_URL') : 'https://gfpay199.com/submit.php',
            'api_url' => $this->getConfig('MERCHANT_API_URL') ? $this->getConfig('MERCHANT_API_URL') : 'https://gfpay199.com/api.php',
//            'pay_url' => 'http://local.gfpay/submit.php'
        );
    }

    public function getConfig($key)
    {
        if (key_exists($key, $this->apiConfig)) {
            return $this->apiConfig[$key];
        }
        return false;
    }

    public function doCurl($url, $params = false, $ispost = 0)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//    curl_setopt($ch, CURLOPT_USERAGENT, 'limuzhengxin.com');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
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
}