<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10 0010
 * Time: 下午 3:43
 */

namespace yuyue\state;


class jungongProject extends \yuyue\yuyue
{
    public function getStateText(){
        return '竣工阶段';

    }

    public function handleFail(){

    }

    public function handleSuccess(array $update)
    {

    }

    public function setnextStep()
    {
        return $this->setStatus(self::ZHONGYAN);
    }

    public function pingjia(array $update){
        $pingObj = new \IModel('yuyue_pingjia');
        if($pingObj->getObj('yuyue_id='.$update['yuyue_id'])){//评价过不允许再评价
            return false;
        }
        $pingObj->setData($update);
        return $pingObj->add();
    }
}