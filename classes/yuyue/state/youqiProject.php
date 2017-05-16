<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10 0010
 * Time: 下午 3:43
 */

namespace yuyue\state;


class youqiProject extends \yuyue\yuyue
{
    public function getStateText(){
        return '油漆阶段';

    }

    public function handleFail(){

    }

    public function handleSuccess(array $update)
    {

    }

    public function setnextStep()
    {
        return $this->setStatus(self::JUNGONG);
    }

    public function pingjia(array $update){

    }
}