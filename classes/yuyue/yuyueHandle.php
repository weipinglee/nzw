<?php

/**
 * @copyright (c) nainaiwang.com
 * @file yuyueHandle.php
 * @brief 预约处理类
 * @author weipinglee
 * @date 2017-05-10
 * @version 1.0
 */
namespace yuyue;
class yuyueHandle extends yuyue
{
    private $stateObj = null;

    /**
     * yuyueHandle constructor.
     * @param int $user_id当前操作用户id
     * @param int $yuyue_id预约id
     */
    public function __construct($yuyue_id=0,$user_id=0)
    {
        parent::__construct($yuyue_id);
        $this->operUserId = $user_id;
        if($yuyue_id){
            $this->setState($yuyue_id);
        }


    }

    /**
     * 验证操作预约订单是否是当前用户的
     * @return bool
     */
    protected function check(){

    }
    /**
     *  设置获取当前状态的状态类
     * @param $id int 预约id
     */
    public function setState($yuyueId)
    {
        $state = null;
        $M = new \IModel($this->table);
        $where = 'id='.$yuyueId;
        $data = $M->getObj($where);


        if(isset($data[$this->staField]) ){
            switch($data[$this->staField]){
                case self::INIT : {
                    $state = new \yuyue\state\startYuyue($yuyueId);
                }
                    break;
                case self::FAIL : {
                    $state = new \yuyue\state\failYuyue($yuyueId);
                }
                    break;
                case self::SUCCESS : {
                    $state = new \yuyue\state\successYuyue($yuyueId);
                }
                    break;

            }
        }
        $this->stateObj = $state;


    }

    public function getStateText(){
        if($this->check()){
            return $this->stateObj->getStateText();
        }
        return null;

    }
    //失败处理
    public function handleFail(){
        if($this->check()){
            return $this->stateObj->handleFail();
        }
        return null;
    }

    //成功处理
    public function handleSuccess(){
        if($this->check()) {
            return $this->stateObj->handleSuccess();
        }
        return null;
    }


}