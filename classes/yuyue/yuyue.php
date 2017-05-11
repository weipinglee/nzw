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

    const INIT = 0;//预约初始
    const FAIL = 2;//预约失败
    const SUCCESS = 1; //预约成功

    protected $table = 'yuyue';//相关表
    protected $staField = 'status';//状态字段
    protected $operUserId = 0;//当前操作用户id
    protected $yuyueId = 0;

    /**
     * yuyueHandle constructor.
     * @param int $user_id当前操作用户id 对状态类无意义
     * @param int $yuyue_id预约id
     */
    public function __construct($yuyue_id=0,$user=0)
    {
        $this->yuyueId = $yuyue_id;

    }

    /**
     * 设置预约状态
     * @param $newStatus int 新状态
     * @return int
     */
    protected function setStatus($newStatus){
        $obj = new \IModel($this->table);
        $obj->setData(array($this->staField=>$newStatus));
        $where = 'id='.$this->yuyueId;
        return $obj->update($where);
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