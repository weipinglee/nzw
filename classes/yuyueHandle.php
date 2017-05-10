<?php

/**
 * @copyright (c) nainaiwang.com
 * @file yuyueHandle.php
 * @brief 预约处理类
 * @author weipinglee
 * @date 2017-05-10
 * @version 1.0
 */

class yuyueHandle
{
    private $stateObj = null;

    const INIT = 0;//预约初始
    const FAIL = 2;//预约失败
    const SUCCESS = 1; //预约成功

    private $table = 'yuyue';//相关表
    private $staField = 'status';//状态字段


    /**
     *  设置获取当前状态的状态类
     * @param $id int 预约id
     */
    public function setState($id)
    {
        $state = null;
        $M = new \IModel($this->table);
        $where = 'id='.$id;
        $data = $M->getObj($where);


        if(isset($data[$this->staField]) ){
            switch($data[$this->staField]){
                case self::INIT : {
                    $state = new \yuyue\startYuyue($id);
                }
                    break;
                case self::FAIL : {
                    $state = new \yuyue\failYuyue($id);
                }
                    break;
                case self::SUCCESS : {
                    $state = new \yuyue\successYuyue($id);
                }
                    break;

            }
        }
        $this->stateObj = $state;


    }

    public function getStateText(){
        return $this->stateObj->getStateText();
    }
    //失败处理
    public function handleFail(){
        return $this->stateObj->handleFail();
    }

    //成功处理
    public function handleSuccess(){
        return $this->stateObj->handleSuccess();
    }


}