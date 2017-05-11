<?php
/**
 * @copyright (c) nainaiwang.com
 * @file identUser.php
 * @brief 身份验证业主类
 * @author weipinglee
 * @date 2017-05-11
 * @version 1.0
 */

namespace yuyue;


 class yuyueHandleUser extends yuyueHandle
{

     public function check(){
         $M = new \IModel('yuyue');
         $where = 'user_id = '.$this->operUserId .' and id='.$this->yuyueId;
         $data = $M->getObj($where);
         if(!empty($data)){
             return true;
         }
         return false;
     }
}