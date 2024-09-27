<?php

class HookAction extends Action
{
    protected $config;
    public $currency;
    public $symbol;
    public $domain;
    public $baseApi='m.shuju1688.top';

    public function __construct()
    {
        parent::__construct();
        $this->config = require_once(ROOT_PATH . 'App/Conf/config.php');
        $domainRuleConfig = require_once(ROOT_PATH . 'App/Conf/domain.php');
        $loan = require_once(ROOT_PATH . 'App/Conf/loan.php');
        print_r($this->config['DEFAULT_TIMEZONE']);
        ini_set('display_errors', 'on');
        ini_set('memory_limit', -1);
        date_default_timezone_set($this->config['DEFAULT_TIMEZONE']);
        error_reporting(E_ALL);

        ignore_user_abort(true);
        set_time_limit(0);

        $domainRule = $domainRuleConfig['APP_SUB_DOMAIN_RULES'];
        $this->domain = array_keys($domainRule);
        $this->currency = $loan['CURRENCY'];
        $this->symbol = $loan['CURRENCY_SYMBOL'];
    }

    /**
     * 导出数据
     * @return void
     */
    public function order()
    {
        if (!is_dir(APP_PATH . 'Runtime/order/')) {
            mkdir(APP_PATH . 'Runtime/order/');
        }
        $lastTime = file_get_contents(APP_PATH . 'Runtime/order/last_order_time.log');

        $loanOrderModel = D('Loanorder');
        $lastTime = $lastTime ? $lastTime : 0;

        $orders = $loanOrderModel->getAllOrder($lastTime, time());
        if (empty($orders)) exit('failure');
        print(count($orders));
        $records = [];
        foreach ($orders as $order) {
            $orderData = json_decode($order['data'], true);

            $record = [
                'add_time' => $order['add_time'],
                'phoneNumber' => isset($orderData['phoneNumber'])?$orderData['phoneNumber']:$order['asasddwssdneNumber'],
                'money' => $order['money'],
                'repaymenttime' => $orderData['repaymenttime'],
                'currency' => $this->currency,
                'symbol' => $this->symbol,
                'domain' => $this->domain[1],
                'remark' => $order['remark']
            ];
            array_push($records, $record);
        }
        $fileName='order_'.date('YmdHi') . '.json';
        $filePath=APP_PATH . 'Runtime/order/fd_' .$fileName;
        file_put_contents($filePath, json_encode($records));
        file_put_contents(APP_PATH . 'Runtime/order/last_order_time.log', time());
        $resp=$this->uploadFile('/import/loan',$filePath,$fileName);
        print_r($resp);

    }

    public function feedback()
    {
        if (!is_dir(APP_PATH . 'Runtime/feedback/')) {
            mkdir(APP_PATH . 'Runtime/feedback/');
        }

        $lastTime = file_get_contents(APP_PATH . 'Runtime/feedback/last_feedback_time.log');
        $feedbackModel = D('Feedback');
        $lastTime = $lastTime ? $lastTime : 0;
        $feedbacks = $feedbackModel->where(['add_time'=>[array("EGT", $lastTime),array("ELT", time())]])->select();
        if (empty($feedbacks)) return false;
        $records = [];
        foreach ($feedbacks as $feedback) {
            $record = [
                'uid' => $feedback['uid'],
                'uname' => $feedback['uname'],
                'content' => $feedback['content'],
                'add_time' => $feedback['add_time'],
                'attachment' => $feedback['attachment'],
                'contact' => $feedback['contact'],
                'currency' => $this->currency,
                'symbol' => $this->symbol,
                'domain' => $this->domain[1],
            ];
            array_push($records, $record);
        }
        print_r($records);
        $fileName='fd_'.date('YmdHi') . '.json';
        $filePath=APP_PATH . 'Runtime/feedback/fd_' .$fileName;
        file_put_contents($filePath, json_encode($records));
        file_put_contents(APP_PATH . 'Runtime/feedback/last_feedback_time.log', time());
        $resp=$this->uploadFile('/import/feedback',$filePath,$fileName);
        print_r($resp);
    }

    public function uploadFile($url,$path,$fileName)
    {
        $url=$this->baseApi.$url;
        $data = array(
            'file'=> new \CURLFile($path),
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);//此处以当前服务器为接收地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);//设置最长等待时间
        curl_setopt($ch, CURLOPT_POST, 1);//post提交
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);//执行
        if(curl_errno($ch)){
            return curl_error($ch);
        }
        curl_close($ch);//释放
        return $response;
    }

}