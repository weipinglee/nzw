<?php
/**
 * @copyright (c) nainaiwang.com
 * @file yuyue.php
 * @brief 关于预约状态模式基类
 * @author weipinglee
 * @date 2017-05-10
 * @version 1.0
 */
namespace yuyue;


abstract class yuyue{


    public $id = 0;

    public function __construct($id)
    {
        $this->id = $id;
    }

    abstract public function getStateText();
    /**
     * 线下预约失败线上提现
     */
    abstract public function handleFail();

    /**
     * 确认要合作生成装修订单
     */
    abstract public function handleSuccess();


}