<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10 0010
 * Time: 下午 3:43
 */

namespace yuyue\state;


class zhongyanProject extends \yuyue\yuyue
{
    public function getStateText(){
        return '验收合格，项目已完成';

    }

    public function handleFail(){

    }

    public function handleSuccess(array $update)
    {

    }

    public function setnextStep()
    {
        return false;
    }
}