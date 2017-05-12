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
    const SUCCESS = 1; //预约成功,生成订单

    const SHUIDIAN = 3;//水电阶段
    const NIMU    = 4;//泥木阶段
    const YOUQI   = 5;//油漆阶段
    const JUNGONG = 6;//竣工阶段
    const ZHONGYAN = 7;//终验合格

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

    //获取状态数组
    public function getStatusArray(){
        return array(
            0 => '预约中',
            1 => '预约成功',
            2 => '预约失败',
            3 => '水电阶段',
            4 => '泥木阶段',
            5 => '油漆阶段',
            6 => '竣工阶段',
            7 => '终验结束',
        );
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
    abstract public function handleSuccess(array $update);

    /**
     * 设置进入下一阶段
     * @return mixed
     */
    abstract public function setnextStep();


}