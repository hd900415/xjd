<?php
class RedisModel
{
    private static $instance;
    private function __construct(){

    }
    public static function getInstance() {
        if(null === self::$instance){
            $redisConfig=C('redis');
            self::$instance = new \Redis();
            self::$instance->connect();
            if ($redisConfig["pconnect"]){
                self::$instance -> pconnect($redisConfig['host'], $redisConfig['port']);
            }else{
                self::$instance -> connect($redisConfig['host'], $redisConfig['port']);
            }
            if($redisConfig['auth']){
                self::$instance->auth($redisConfig['auth']);
            }
            if(isset($redisConfig['db'])){
                self::$instance->select($redisConfig['db']);
            }
        }
        return self::$instance;
    }
    private function __clone() {

    }
}