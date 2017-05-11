<?php
/**
 * @copyright (c) nainaiwang.com
 * @file identCompany.php
 * @brief 身份验证企业类
 * @author weipinglee
 * @date 2017-05-11
 * @version 1.0
 */

namespace yuyue;

 class yuyueHandleCompany extends yuyueHandle
{

     protected function check(){
         $M = new \IModel('yuyue');
         $where = 'company_id = '.$this->operUserId .' and id='.$this->yuyueId;
         $data = $M->getObj($where);
         if(!empty($data)){
             return true;
         }
         return false;
     }
}