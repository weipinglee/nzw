<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10 0010
 * Time: 下午 3:43
 */

namespace yuyue\state;


class shuidianProject extends \yuyue\yuyue
{
    public function getStateText(){
        return '水电阶段';

    }

    public function handleFail(){

    }

    public function handleSuccess(array $update)
    {

    }

    public function setnextStep()
    {
       return $this->setStatus(self::NIMU);
    }

    public function pingjia(array $update){

    }
}