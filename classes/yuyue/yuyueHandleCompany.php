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

     public function getDetail(){
         $M = new \IQuery('yuyue as y');
         $M->join = " left join company as c on y.company_id = c.user_id";
         $M->fields = "y.*,c.contacts_name,c.true_name";
         $M->limit = 1;
         $M->where = "y.company_id = ".$this->operUserId.' and y.id = '.$this->yuyueId;
         $data = $M->find();
         if(!empty($data)){
             $this->setState($this->yuyueId);
             $data[0]['statusText'] = $this->stateObj->getStateText();
             $data[0]['id'] = $this->yuyueId;
             return $data[0];
         }
         return array();
     }

     public function getProjectList($page=1,$type='all')
     {
         $Q = new \IQuery('yuyue as y');
         $Q->page = $page;

         if($type=='ed'){
             $where = ' and y.status >=60';
         }
         elseif($type=='ing'){
             $where = ' and y.status < 60';
         }
         else{
             $where = '';
         }
         $Q->where = 'y.isproject=1 and y.company_id='.$this->operUserId.$where;
         $data = $Q->find();
         $pageBar = $Q->getPageBar();
         return array($data,$pageBar);
     }
 }