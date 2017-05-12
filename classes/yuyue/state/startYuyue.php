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

    public function handleSuccess(array $update)
    {
        $obj = new \IModel($this->table);
        if(!empty($update)){
            $update['status'] = self::SUCCESS;
            $update['order_time'] = \ITime::getDateTime();
            $obj->setData($update);
            if($obj->update('id='.$this->yuyueId)){
                return true;
            }
        }
        return false;

    }
}