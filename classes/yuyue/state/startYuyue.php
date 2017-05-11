<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10 0010
 * Time: 下午 3:43
 */

namespace yuyue\state;


class startYuyue extends \yuyue\yuyue
{


    public function getStateText(){
        return '线下洽谈中';
    }


    public function handleFail(){
        if($this->setStatus(self::FAIL)){
            return true;
        }
        return false;
    }

    public function handleSuccess()
    {

    }
}