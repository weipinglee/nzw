<?php

/**
 * @copyright (c) nainaiwang.com
 * @file tag_base.php
 * @brief ��ǩ��������
 * @author weipinglee
 * @date 2017-05-18
 * @version 1.0
 */

 class tag_base
{

     //������������
      protected static $tagClasses = array(
         'lang_base',
         'html_base',

     );

     public  function addTagClass($array=array()){
         self::$tagClasses = array_merge(self::$tagClasses,$array);
     }
     private static $tagObjArr = array();

     public function init()
     {
         print_r(self::$tagClasses);
        foreach(self::$tagClasses as $item){
            if(!isset(self::$tagObjArr[$item]) ){
                self::$tagObjArr[$item] = new $item();
            }

        }
     }


     /**
      * �����ַ��� ƥ��ı�ǩ������$��/Ӣ����ĸ���磺/if)��Ӣ����ĸ(��if��foreach)
      * @param $str string  Ҫ�������ַ���
      * @return mixed
      */
     public function translate($str){
        return  preg_replace_callback('/{(\$|\/[a-zA-Z]+|[a-zA-Z]+)\s*(:?)([^}]*)}/i', array($this,'resolve'), $str);

     }

     /**
      * ������ƥ����������
      * @param $matches
      * @return mixed
      */
     protected function resolve($matches){
         //�����ǩ������Ϊ�գ����г�ʼ��
         if(empty(self::$tagObjArr)){
             $this->init();
         }
        foreach(self::$tagClasses as $item){
            if(array_key_exists($matches[1],$item::$tagNameParse))//���ƥ��ı�ǩ�ڱ�ǩ���ת�������У�����ת����ǩ�����罫$תΪdollars
                $matches[1] = $item::$tagNameParse[$matches[1]];
            //����ڸñ�ǩ���д��������ǩ���������ñ�ǩ��÷�������
            if(method_exists($item,'_'.$matches[1])){
                $obj = self::$tagObjArr[$item];
               $res = call_user_func_array(array($obj,'_'.$matches[1]),array($matches[3]));
                if(false === $res)//�������false,�򷵻�ԭ�ַ���
                    return $matches[0];
                return $res;
            }

        }
         return $matches[0];//���ƥ�䲻����Ӧ�Ľ����������򷵻�ԭ�ַ���
     }

    /**
     * @brief ������ǩ����
     * @param string $str
     * @return array���������ʽ��������ֵ
     */
    protected function getAttrs($str)
    {
        preg_match_all('/\w+\s*=(?:[^=]+?)(?=(\S+\s*=)|$)/i', trim($str), $attrs);
        $attr = array();
        foreach($attrs[0] as $value)
        {
            $tem = explode('=',$value);
            $attr[trim($tem[0])] = trim($tem[1]);
        }
        return $attr;
    }

     /**
      * @brief �����滻����
      * @param string $str
      * @return string
      */
     protected function varReplace($str)
     {
         return preg_replace(array("#(\\$.*?(?=$|\/))#","#(\\$\w+)\[(\w+)\]#"),array("\".$1.\"","$1['$2']"),$str);
     }
}