<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10 0010
 * Time: 下午 3:43
 */

namespace yuyue\state;


class nimuProject extends \yuyue\yuyue
{
    public function getStateText(){
        return '泥木';

    }

    public function handleFail(){

    }

    public function handleSuccess(array $update)
    {

    }

    public function setnextStep()
    {
        return $this->setStatus(self::YOUQI);
    }

    public function pingjia(array $update){

    }
}