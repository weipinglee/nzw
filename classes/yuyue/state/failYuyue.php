<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10 0010
 * Time: 下午 3:43
 */

namespace yuyue\state;


class failYuyue extends \yuyue\yuyue
{
    public function getStateText(){
            return '预约失败';

    }

    public function handleFail(){

    }

    public function handleSuccess(array $update)
    {

    }
}