<?php

class LoanbillDelayModel extends RelationModel
{

    public function newOrderId($bid)
    {
        $oid = "DELAY" . $bid . "." . date("YmdHis") . rand(1000001, 9999999);
        return $oid;
    }
}