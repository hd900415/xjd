<?php

class LoanorderModel extends RelationModel
{
    public function getAllOrder($startTime, $endTime)
    {
        $where=['add_time'=>[array("EGT", $startTime),array("ELT", $endTime)]];
        $orders = self::where($where)->select();
        return $orders;
    }
}