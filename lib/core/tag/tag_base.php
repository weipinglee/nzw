<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/17 0017
 * Time: ���� 3:13
 */

 class tag_base
{

     //������������
     protected static $tagClasses = array(
         'lang_base',
         'html_base'

     );
     private static $tagObjArr = array();
     public function __construct()
     {

     }

     public function translate($str){
        return  preg_replace_callback('/{(\$|\/[a-zA-Z]+|[a-zA-Z]+)\s*(:?)([^}]*)}/i', array($this,'resolve'), $str);

     }

     protected function resolve($matches){
        foreach(self::$tagClasses as $item){
            if(array_key_exists($matches[1],$item::$tagNameParse))
                $matches[1] = $item::$tagNameParse[$matches[1]];
            if(method_exists($item,'_'.$matches[1])){
                $obj = new $item();
               $res = call_user_func_array(array($obj,'_'.$matches[1]),array($matches[3]));
                if(false === $res)
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