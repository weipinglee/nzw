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

     public function getDetail(){
         $M = new \IQuery('yuyue as y');
         $M->join = " left join company as c on y.company_id = c.user_id";
         $M->fields = "y.*,c.contacts_name,c.true_name";
         $M->limit = 1;
         $M->where = "y.user_id = ".$this->operUserId.' and y.id = '.$this->yuyueId;
         $data = $M->find();
         if(!empty($data)){
             $this->setState($this->yuyueId);
             $data[0]['statusText'] = $this->stateObj->getStateText();
             $data[0]['id'] = $this->yuyueId;
             return $data[0];
         }
         return array();
     }

     public function getProjectList($page=1)
     {
         $Q = new \IQuery('yuyue as y');
         $Q->join = "left join company as c on y.company_id = c.user_id";
         $Q->fields = " y.*,c.contacts_name,c.true_name";
         $Q->page = $page;
         $Q->where = 'y.isproject=1 and y.user_id='.$this->operUserId;
         $data = $Q->find();
         $pageBar = $Q->getPageBar();
         return array($data,$pageBar);
     }
}